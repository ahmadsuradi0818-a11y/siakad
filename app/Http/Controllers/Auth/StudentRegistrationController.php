<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentRegistrationDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class StudentRegistrationController extends Controller
{
    public function create()
    {
        return view('auth.register-student');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'        => ['required', 'confirmed', Rules\Password::defaults()],
            'nis'             => ['required', 'string', 'max:30', 'unique:students,nis'],
            'nisn'            => ['nullable', 'string', 'max:30'],
            'place_of_birth'  => ['nullable', 'string', 'max:100'],
            'date_of_birth'   => ['nullable', 'date'],
            'gender'          => ['nullable', 'in:L,P'],
            'religion'        => ['nullable', 'string', 'max:50'],
            'address'         => ['nullable', 'string', 'max:500'],
            'parent_name'     => ['nullable', 'string', 'max:255'],
            'parent_phone'    => ['nullable', 'string', 'max:20'],
        ]);

        try {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'siswa',
            ]);

            $student = Student::create([
                'user_id'             => $user->id,
                'nis'                 => $validated['nis'],
                'nisn'                => $validated['nisn'] ?? null,
                'place_of_birth'      => $validated['place_of_birth'] ?? null,
                'date_of_birth'       => $validated['date_of_birth'] ?? null,
                'gender'              => $validated['gender'] ?? null,
                'religion'            => $validated['religion'] ?? null,
                'address'             => $validated['address'] ?? null,
                'parent_name'         => $validated['parent_name'] ?? null,
                'parent_phone'        => $validated['parent_phone'] ?? null,
                'registration_status' => 'pending',
                'registered_online'   => true,
            ]);

            return redirect()->route('register.student.documents', $student)
                ->with('success', 'Data diri berhasil disimpan. Silakan unggah berkas persyaratan.');
        } catch (\Exception $e) {
            Log::error('Student registration failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.']);
        }
    }

    public function documents(Student $student)
    {
        $documents = $student->registrationDocuments;
        return view('auth.register-student-documents', compact('student', 'documents'));
    }

    public function storeDocuments(Request $request, Student $student)
    {
        $rules = [
            'ktp'    => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
            'ijazah' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
            'kk'     => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
        ];

        $request->validate($rules);

        $fields = [
            'ktp'    => 'KTP Orang Tua',
            'ijazah' => 'Ijazah SMP/SKL',
            'kk'     => 'Kartu Keluarga',
        ];

        foreach ($fields as $inputName => $docType) {
            if ($request->hasFile($inputName)) {
                $file = $request->file($inputName);
                $path = $file->store('registration-documents/' . $student->id, 'public');

                StudentRegistrationDocument::updateOrCreate(
                    [
                        'student_id'    => $student->id,
                        'document_type' => $docType,
                    ],
                    [
                        'file_path'     => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ]
                );
            }
        }

        return redirect()->route('login')
            ->with('status', 'Pendaftaran berhasil! Silakan masuk menggunakan email dan password yang telah didaftarkan.');
    }
}
