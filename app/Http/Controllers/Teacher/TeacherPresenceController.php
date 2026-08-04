<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherPresence;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;

class TeacherPresenceController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $presences = TeacherPresence::where('teacher_id', $userId)
            ->orderBy('date', 'desc')
            ->paginate(10);

        $todayPresence = TeacherPresence::where('teacher_id', $userId)
            ->whereDate('date', today())
            ->first();

        return view('teacher.teacher-presences.index', compact('presences', 'todayPresence'));
    }

    public function store(Request $request)
    {
        $userId = auth()->id();

        $validated = $request->validate([
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $existing = TeacherPresence::where('teacher_id', $userId)
            ->whereDate('date', today())
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan presensi hari ini pukul ' . $existing->presence_at?->format('H:i') . ' WIB.'
            ]);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Anda berada di luar radius presensi. Jarak: ' . round($distance) . ' m.'
                ]);
            }
        }

        $photoPath = $request->file('photo')->store('teacher-presences', 'public');

        TeacherPresence::create([
            'teacher_id' => $userId,
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

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil! Status: ' . $statusLabels[$validated['status']] . '.'
        ]);
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
