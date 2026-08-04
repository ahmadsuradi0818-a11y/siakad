<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Presence;
use App\Models\Student;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Presence::with('student.user', 'student.class');

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $presences = $query->paginate(10);
        $classes = ClassModel::with('students.user')->get();

        return view('admin.presences.index', compact('presences', 'classes'));
    }

    public function create()
    {
        $classes = ClassModel::with('students.user')->get();
        return view('admin.presences.create', compact('classes'));
    }

    public function store(Request $request, WhatsAppService $whatsapp)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpha',
        ]);

        Presence::updateOrCreate(
            ['student_id' => $validated['student_id'], 'date' => $validated['date']],
            ['status' => $validated['status']]
        );

        $student = Student::with('user', 'class')->find($validated['student_id']);

        if ($student && $student->parent_phone) {
            $statusLabels = [
                'hadir' => 'Hadir',
                'izin' => 'Izin',
                'sakit' => 'Sakit',
                'alpha' => 'Alpha (Tanpa Keterangan)',
            ];
            $tanggal = \Carbon\Carbon::parse($validated['date'])->isoFormat('dddd, D MMMM YYYY');
            $statusLabel = $statusLabels[$validated['status']];
            $message = "Assalamu'alaikum Wr. Wb.\n\n";
            $message .= "Dengan ini kami informasikan bahwa:\n";
            $message .= "Nama: {$student->user->name}\n";
            $message .= "Kelas: {$student->class->name}\n";
            $message .= "Tanggal: {$tanggal}\n";
            $message .= "Status Presensi: *{$statusLabel}*\n\n";
            $message .= "Terima kasih.\n\n";
            $message .= "- SMK Satya Bhakti -";

            $whatsapp->send($student->parent_phone, $message);
        }

        return redirect()->route('admin.presences.index')
            ->with('success', 'Presensi berhasil dicatat.');
    }

    public function destroy(Presence $presence)
    {
        $presence->delete();
        return redirect()->route('admin.presences.index')
            ->with('success', 'Data presensi berhasil dihapus.');
    }
}
