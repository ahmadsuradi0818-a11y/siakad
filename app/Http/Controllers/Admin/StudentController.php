<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\IdCardSetting;
use App\Models\SchoolProfile;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('user', 'class')->paginate(10);
        $classes = ClassModel::all();
        return view('admin.students.index', compact('students', 'classes'));
    }

    public function show(Student $student)
    {
        return redirect()->route('admin.students.edit', $student);
    }

    public function create()
    {
        $classes = ClassModel::all();
        return view('admin.students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'nis' => 'required|string|unique:students,nis',
            'class_id' => 'required|exists:classes,id',
            'place_of_birth' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'nisn' => 'nullable|string|max:20|unique:students,nisn',
            'gender' => 'nullable|in:L,P',
            'religion' => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'siswa',
        ]);

        Student::create([
            'user_id' => $user->id,
            'class_id' => $validated['class_id'],
            'nis' => $validated['nis'],
            'place_of_birth' => $validated['place_of_birth'],
            'date_of_birth' => $validated['date_of_birth'],
            'address' => $validated['address'],
            'parent_name' => $validated['parent_name'],
            'parent_phone' => $validated['parent_phone'],
            'nisn' => $validated['nisn'],
            'gender' => $validated['gender'],
            'religion' => $validated['religion'],
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit(Student $student)
    {
        $student->load('user');
        $classes = ClassModel::all();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'nis' => 'required|string|unique:students,nis,' . $student->id,
            'class_id' => 'required|exists:classes,id',
            'password' => 'nullable|string|min:8',
            'place_of_birth' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'nisn' => 'nullable|string|max:20|unique:students,nisn,' . $student->id,
            'gender' => 'nullable|in:L,P',
            'religion' => 'nullable|string|max:50',
        ]);

        $student->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $student->user->update(['password' => Hash::make($validated['password'])]);
        }

        $student->update([
            'nis' => $validated['nis'],
            'class_id' => $validated['class_id'],
            'place_of_birth' => $validated['place_of_birth'],
            'date_of_birth' => $validated['date_of_birth'],
            'address' => $validated['address'],
            'parent_name' => $validated['parent_name'],
            'parent_phone' => $validated['parent_phone'],
            'nisn' => $validated['nisn'],
            'gender' => $validated['gender'],
            'religion' => $validated['religion'],
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->user->delete();
        $student->delete();
        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,docx',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        $imported = 0;
        $errors = [];

        try {
            $rows = [];

            if (in_array($extension, ['xlsx', 'xls'])) {
                $spreadsheet = SpreadsheetIOFactory::load($file->getPathname());
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                foreach ($data as $i => $row) {
                    if ($i === 0) continue;
                    if (empty(array_filter($row))) continue;
                    $rows[] = $row;
                }
            } else {
                $phpWord = WordIOFactory::load($file->getPathname());
                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if (method_exists($element, 'getRows')) {
                            foreach ($element->getRows() as $r => $row) {
                                if ($r === 0) continue;
                                $cells = [];
                                foreach ($row->getCells() as $cell) {
                                    $cells[] = trim(strip_tags($cell->getText()));
                                }
                                if (!empty(array_filter($cells))) {
                                    $rows[] = $cells;
                                }
                            }
                        } elseif (method_exists($element, 'getText')) {
                            $text = trim($element->getText());
                            if (str_contains($text, "\t")) {
                                $cols = explode("\t", $text);
                                if (!empty(array_filter($cols))) {
                                    $rows[] = $cols;
                                }
                            }
                        }
                    }
                }
            }

            $classes = ClassModel::pluck('id', 'name');

            foreach ($rows as $idx => $row) {
                $name = trim($row[0] ?? '');
                $email = trim($row[1] ?? '');
                $password = trim($row[2] ?? '');
                $nis = trim($row[3] ?? '');
                $className = trim($row[4] ?? '');
                $parentPhone = trim($row[5] ?? '');

                if (empty($name) || empty($email) || empty($password) || empty($nis) || empty($className)) {
                    $errors[] = "Baris " . ($idx + 2) . ": Data tidak lengkap (butuh Nama, Email, Password, NIS, Nama Kelas)";
                    continue;
                }

                if (User::where('email', $email)->exists()) {
                    $errors[] = "Baris " . ($idx + 2) . ": Email '$email' sudah terdaftar";
                    continue;
                }

                if (Student::where('nis', $nis)->exists()) {
                    $errors[] = "Baris " . ($idx + 2) . ": NIS '$nis' sudah terdaftar";
                    continue;
                }

                $classId = $classes[$className] ?? null;
                if (!$classId) {
                    $errors[] = "Baris " . ($idx + 2) . ": Kelas '$className' tidak ditemukan";
                    continue;
                }

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => 'siswa',
                ]);

                Student::create([
                    'user_id' => $user->id,
                    'class_id' => $classId,
                    'nis' => $nis,
                    'parent_phone' => $parentPhone ?: null,
                ]);

                $imported++;
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }

        $message = "Berhasil mengimport $imported data siswa.";
        if (!empty($errors)) {
            $message .= " " . implode('; ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= " (dan " . (count($errors) - 5) . " error lainnya)";
            }
        }

        return redirect()->route('admin.students.index')
            ->with($imported > 0 ? 'success' : 'error', $message);
    }

    public function idCardIndex(Request $request)
    {
        $classes = ClassModel::all();
        $query = Student::with('user', 'class');

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->paginate(20);
        return view('admin.students.id-card-index', compact('students', 'classes'));
    }

    public function idCardPrint(Request $request)
    {
        $ids = $request->input('students', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu siswa.');
        }

        $students = Student::with('user', 'class')->whereIn('id', $ids)->get();
        $school = SchoolProfile::first();
        $settings = IdCardSetting::getSettings();

        return view('admin.students.id-card-print', compact('students', 'school', 'settings'));
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Nama', 'Email', 'Password', 'NIS', 'Nama Kelas', 'No. WA Orang Tua'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $col++;
        }

        $sheet->setCellValue('A2', 'Budi Santoso');
        $sheet->setCellValue('B2', 'budi@example.com');
        $sheet->setCellValue('C2', 'password123');
        $sheet->setCellValue('D2', '1234567890');
        $sheet->setCellValue('E2', 'XII RPL 1');
        $sheet->setCellValue('F2', '081234567890');

        foreach (range('A', 'F') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        ob_end_clean();
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_import_siswa.xlsx"',
        ]);
    }
}
