<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->user()->student;

        $query = Presence::where('student_id', $student->id);

        if ($request->filled('bulan')) {
            $query->whereMonth('date', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('date', $request->tahun);
        }

        $presences = $query->orderBy('date', 'desc')->paginate(10);

        $todayPresence = Presence::where('student_id', $student->id)
            ->whereDate('date', today())
            ->first();

        return view('student.presences', compact('presences', 'todayPresence'));
    }

    public function store(Request $request)
    {
        $student = auth()->user()->student;

        $validated = $request->validate([
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $existing = Presence::where('student_id', $student->id)
            ->whereDate('date', today())
            ->first();

        if ($existing) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan presensi hari ini pukul ' . $existing->presence_at?->format('H:i') . ' WIB.']);
            }
            return redirect()->route('student.presensi')
                ->with('error', 'Anda sudah melakukan presensi hari ini pukul ' . $existing->presence_at?->format('H:i') . ' WIB.');
        }

        $school = SchoolProfile::first();
        if ($school && $school->presence_radius > 0 && $school->presence_latitude && $school->presence_longitude) {
            $distance = $this->haversineDistance(
                $school->presence_latitude,
                $school->presence_longitude,
                $validated['latitude'],
                $validated['longitude']
            );

            if ($distance > $school->presence_radius) {
                $msg = 'Anda berada di luar radius presensi yang diizinkan. Jarak: ' . round($distance) . ' m.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $msg]);
                }
                return redirect()->route('student.presensi')->with('error', $msg);
            }
        }

        $photoPath = $request->file('photo')->store('presences', 'public');

        Presence::create([
            'student_id' => $student->id,
            'date' => today(),
            'status' => $validated['status'],
            'presence_at' => now(),
            'photo' => $photoPath,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        $statusLabels = [
            'hadir' => 'Hadir',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha',
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Presensi berhasil! Status: ' . $statusLabels[$validated['status']] . '.']);
        }

        return redirect()->route('student.presensi')
            ->with('success', 'Presensi berhasil! Status: ' . $statusLabels[$validated['status']] . '.');
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
