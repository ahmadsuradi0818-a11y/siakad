<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('user')->paginate(10);
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,guru',
            'nip' => 'required|string|unique:teachers,nip',
            'place_of_birth' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:L,P',
            'religion' => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        Teacher::create([
            'user_id' => $user->id,
            'nip' => $validated['nip'],
            'place_of_birth' => $validated['place_of_birth'],
            'date_of_birth' => $validated['date_of_birth'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'religion' => $validated['religion'],
        ]);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Guru berhasil ditambahkan.');
    }

    public function edit(Teacher $teacher)
    {
        $teacher->load('user');
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->user_id,
            'role' => 'required|in:admin,guru',
            'nip' => 'required|string|unique:teachers,nip,' . $teacher->id,
            'password' => 'nullable|string',
            'place_of_birth' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:L,P',
            'religion' => 'nullable|string|max:50',
        ]);

        $teacher->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if (!empty($request->password)) {
            if (strlen($request->password) < 8) {
                return redirect()->back()->withErrors(['password' => 'Password minimal 8 karakter.'])->withInput();
            }
            $teacher->user->update(['password' => Hash::make($request->password)]);
        }

        $teacher->update([
            'nip' => $validated['nip'],
            'place_of_birth' => $validated['place_of_birth'],
            'date_of_birth' => $validated['date_of_birth'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'religion' => $validated['religion'],
        ]);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->user->delete();
        $teacher->delete();
        return redirect()->route('admin.teachers.index')
            ->with('success', 'Guru berhasil dihapus.');
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

            foreach ($rows as $idx => $row) {
                $name = trim($row[0] ?? '');
                $email = trim($row[1] ?? '');
                $password = trim($row[2] ?? '');
                $nip = trim($row[3] ?? '');
                $role = strtolower(trim($row[4] ?? 'guru'));

                if (empty($name) || empty($email) || empty($password) || empty($nip)) {
                    $errors[] = "Baris " . ($idx + 2) . ": Data tidak lengkap (butuh Nama, Email, Password, NIP)";
                    continue;
                }

                if (!in_array($role, ['admin', 'guru'])) {
                    $role = 'guru';
                }

                if (User::where('email', $email)->exists()) {
                    $errors[] = "Baris " . ($idx + 2) . ": Email '$email' sudah terdaftar";
                    continue;
                }

                if (Teacher::where('nip', $nip)->exists()) {
                    $errors[] = "Baris " . ($idx + 2) . ": NIP '$nip' sudah terdaftar";
                    continue;
                }

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => $role,
                ]);

                Teacher::create([
                    'user_id' => $user->id,
                    'nip' => $nip,
                ]);

                $imported++;
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.teachers.index')
                ->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }

        $message = "Berhasil mengimport $imported data guru.";
        if (!empty($errors)) {
            $message .= " " . implode('; ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= " (dan " . (count($errors) - 5) . " error lainnya)";
            }
        }

        return redirect()->route('admin.teachers.index')
            ->with($imported > 0 ? 'success' : 'error', $message);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Nama', 'Email', 'Password', 'NIP', 'Role'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $col++;
        }

        $sheet->setCellValue('A2', 'John Doe');
        $sheet->setCellValue('B2', 'john@example.com');
        $sheet->setCellValue('C2', 'password123');
        $sheet->setCellValue('D2', '1987654321');
        $sheet->setCellValue('E2', 'guru');

        foreach (range('A', 'E') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        ob_end_clean();
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_import_guru.xlsx"',
        ]);
    }
}
