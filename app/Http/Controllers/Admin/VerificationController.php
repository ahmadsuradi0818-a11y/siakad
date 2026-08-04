<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ClassModel;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index()
    {
        $students = Student::where('registered_online', true)
            ->with('user', 'class', 'registrationDocuments')
            ->orderByRaw("FIELD(registration_status, 'pending', 'verified', 'accepted', 'rejected')")
            ->latest()
            ->paginate(20);

        $counts = [
            'pending'  => Student::where('registered_online', true)->where('registration_status', 'pending')->count(),
            'verified' => Student::where('registered_online', true)->where('registration_status', 'verified')->count(),
            'accepted' => Student::where('registered_online', true)->where('registration_status', 'accepted')->count(),
            'rejected' => Student::where('registered_online', true)->where('registration_status', 'rejected')->count(),
        ];

        $classes = ClassModel::all();

        return view('admin.verification.index', compact('students', 'counts', 'classes'));
    }

    public function show(Student $student)
    {
        $student->load('user', 'class', 'registrationDocuments');
        $classes = ClassModel::all();
        return view('admin.verification.show', compact('student', 'classes'));
    }

    public function verify(Student $student, WhatsAppService $wa)
    {
        $student->update(['registration_status' => 'verified']);

        if ($student->parent_phone) {
            $wa->send($student->parent_phone, implode("\n", [
                "*PEMBERITAHUAN VERIFIKASI DATA*",
                "",
                "Yth. Orang Tua/Wali dari {$student->user->name},",
                "",
                "Data pendaftaran ananda *{$student->user->name}* (NIS: {$student->nis}) telah diverifikasi oleh pihak sekolah.",
                "",
                "Saat ini data sedang dalam proses peninjauan kelulusan.",
                "Mohon menunggu informasi selanjutnya.",
                "",
                "Terima kasih.",
                "- Panitia PPDB",
            ]));
        }

        return back()->with('success', 'Data calon siswa telah diverifikasi. WhatsApp notification terkirim.');
    }

    public function accept(Request $request, Student $student, WhatsAppService $wa)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        $student->update([
            'registration_status' => 'accepted',
            'class_id'            => $request->class_id,
            'registration_note'   => $request->note,
        ]);

        if ($student->parent_phone) {
            $wa->send($student->parent_phone, implode("\n", [
                "*SELAMAT! ANDA DITERIMA*",
                "",
                "Yth. Orang Tua/Wali dari {$student->user->name},",
                "",
                "Dengan ini kami mengumumkan bahwa ananda *{$student->user->name}* (NIS: {$student->nis}) dinyatakan *LULUS* dan diterima sebagai siswa baru.",
                "",
                "*LANJUTKAN DAFTAR ULANG:*",
                "Segera lakukan daftar ulang ke sekolah dengan membawa:",
                "1. KTP Orang Tua (asli & fotokopi)",
                "2. Ijazah SMP/SKL (asli & fotokopi)",
                "3. Kartu Keluarga (asli & fotokopi)",
                "4. Pas foto 3x4 (2 lembar)",
                "",
                "Terima kasih.",
                "- Panitia PPDB",
            ]));
        }

        return redirect()->route('admin.verification.index')
            ->with('success', 'Calon siswa dinyatakan LULUS. Pemberitahuan Daftar Ulang telah dikirim ke WA.');
    }

    public function reject(Request $request, Student $student, WhatsAppService $wa)
    {
        $request->validate([
            'note' => 'required|string|max:500',
        ]);

        $student->update([
            'registration_status' => 'rejected',
            'registration_note'   => $request->note,
        ]);

        if ($student->parent_phone) {
            $wa->send($student->parent_phone, implode("\n", [
                "*PEMBERITAHUAN PENOLAKAN*",
                "",
                "Yth. Orang Tua/Wali dari {$student->user->name},",
                "",
                "Mohon maaf, pendaftaran ananda *{$student->user->name}* (NIS: {$student->nis}) *dinyatakan ditolak*.",
                "",
                "Alasan: {$request->note}",
                "",
                "Untuk informasi lebih lanjut, silakan menghubungi pihak sekolah.",
                "Terima kasih.",
                "- Panitia PPDB",
            ]));
        }

        return redirect()->route('admin.verification.index')
            ->with('success', 'Calon siswa ditolak. WhatsApp notification terkirim.');
    }
}
