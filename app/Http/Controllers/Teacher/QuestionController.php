<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;

class QuestionController extends Controller
{
    public function index(Exam $exam)
    {
        $userId = auth()->id();
        if ($exam->teacher_id !== $userId) {
            abort(403);
        }

        $questions = $exam->questions()->orderBy('id')->paginate(20);
        $bankQuestions = QuestionBank::where('created_by', $userId)
            ->orWhereHas('createdBy', fn($q) => $q->where('role', 'admin'))
            ->with(['createdBy', 'subject'])
            ->orderBy('id', 'desc')
            ->get();
        $subjects = Subject::orderBy('name')->get();
        return view('teacher.questions.index', compact('exam', 'questions', 'bankQuestions', 'subjects'));
    }

    public function create(Exam $exam)
    {
        $userId = auth()->id();
        if ($exam->teacher_id !== $userId) {
            abort(403);
        }

        return view('teacher.questions.create', compact('exam'));
    }

    public function store(Request $request, Exam $exam)
    {
        $userId = auth()->id();
        if ($exam->teacher_id !== $userId) {
            abort(403);
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:pilihan_ganda,pilihan_ganda_kompleks,benar_salah,isian_singkat,essay',
            'options' => 'nullable|array',
            'options.*' => 'required|string|distinct',
            'correct_answer' => 'nullable|string|max:255',
            'points' => 'required|integer|min:0',
        ]);

        if (in_array($validated['type'], ['pilihan_ganda', 'pilihan_ganda_kompleks'])) {
            $validated['options'] = array_values(array_filter($validated['options'] ?? []));
        } else {
            $validated['options'] = null;
        }

        $question = $exam->questions()->create($validated);

        // Auto-save to bank soal
        QuestionBank::create([
            'question_text' => $validated['question_text'],
            'type' => $validated['type'],
            'options' => $validated['options'] ?? null,
            'correct_answer' => $validated['correct_answer'] ?? null,
            'points' => $validated['points'],
            'created_by' => auth()->id(),
            'subject_id' => $exam->subject_id,
        ]);

        return redirect()->route('teacher.exams.questions.index', $exam)
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(Exam $exam, Question $question)
    {
        $userId = auth()->id();
        if ($exam->teacher_id !== $userId) {
            abort(403);
        }

        return view('teacher.questions.edit', compact('exam', 'question'));
    }

    public function update(Request $request, Exam $exam, Question $question)
    {
        $userId = auth()->id();
        if ($exam->teacher_id !== $userId) {
            abort(403);
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:pilihan_ganda,pilihan_ganda_kompleks,benar_salah,isian_singkat,essay',
            'options' => 'nullable|array',
            'options.*' => 'required|string|distinct',
            'correct_answer' => 'nullable|string|max:255',
            'points' => 'required|integer|min:0',
        ]);

        if (in_array($validated['type'], ['pilihan_ganda', 'pilihan_ganda_kompleks'])) {
            $validated['options'] = array_values(array_filter($validated['options'] ?? []));
        } else {
            $validated['options'] = null;
        }

        $question->update($validated);

        return redirect()->route('teacher.exams.questions.index', $exam)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Exam $exam, Question $question)
    {
        $userId = auth()->id();
        if ($exam->teacher_id !== $userId) {
            abort(403);
        }

        $question->delete();

        return redirect()->route('teacher.exams.questions.index', $exam)
            ->with('success', 'Soal berhasil dihapus.');
    }

    public function import(Request $request, Exam $exam)
    {
        $userId = auth()->id();
        if ($exam->teacher_id !== $userId) {
            abort(403);
        }

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

            $validTypes = ['pilihan_ganda', 'pilihan_ganda_kompleks', 'benar_salah', 'isian_singkat', 'essay'];

            foreach ($rows as $idx => $row) {
                $type = strtolower(trim($row[0] ?? ''));
                $questionText = trim($row[1] ?? '');
                $optA = trim($row[2] ?? '');
                $optB = trim($row[3] ?? '');
                $optC = trim($row[4] ?? '');
                $optD = trim($row[5] ?? '');
                $optE = trim($row[6] ?? '');
                $correctAnswer = trim($row[7] ?? '');
                $points = (int) trim($row[8] ?? '0');

                if (empty($questionText) || empty($type)) {
                    $errors[] = "Baris " . ($idx + 2) . ": Soal dan Tipe wajib diisi";
                    continue;
                }

                if (!in_array($type, $validTypes)) {
                    $errors[] = "Baris " . ($idx + 2) . ": Tipe '$type' tidak valid";
                    continue;
                }

                $data = [
                    'question_text' => $questionText,
                    'type' => $type,
                    'correct_answer' => $correctAnswer ?: null,
                    'points' => max(0, $points),
                ];

                if (in_array($type, ['pilihan_ganda', 'pilihan_ganda_kompleks'])) {
                    $options = array_values(array_filter([$optA, $optB, $optC, $optD, $optE]));
                    if (empty($options)) {
                        $errors[] = "Baris " . ($idx + 2) . ": Soal $type membutuhkan minimal 2 pilihan jawaban";
                        continue;
                    }
                    $data['options'] = $options;
                } else {
                    $data['options'] = null;
                }

                $exam->questions()->create($data);
                QuestionBank::create([
                    'question_text' => $data['question_text'],
                    'type' => $data['type'],
                    'options' => $data['options'] ?? null,
                    'correct_answer' => $data['correct_answer'] ?? null,
                    'points' => $data['points'],
                    'created_by' => auth()->id(),
                    'subject_id' => $exam->subject_id,
                ]);
                $imported++;
            }
        } catch (\Exception $e) {
            return redirect()->route('teacher.exams.questions.index', $exam)
                ->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }

        $message = "Berhasil mengimport $imported soal.";
        if (!empty($errors)) {
            $message .= " " . implode('; ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= " (dan " . (count($errors) - 5) . " error lainnya)";
            }
        }

        return redirect()->route('teacher.exams.questions.index', $exam)
            ->with($imported > 0 ? 'success' : 'error', $message);
    }

    public function downloadTemplate(Exam $exam)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Tipe', 'Soal', 'Pilihan A', 'Pilihan B', 'Pilihan C', 'Pilihan D', 'Pilihan E', 'Jawaban', 'Poin'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $col++;
        }

        $examples = [
            ['pilihan_ganda', 'Apa ibu kota Indonesia?', 'Jakarta', 'Surabaya', 'Bandung', 'Medan', '', 'A', '10'],
            ['pilihan_ganda_kompleks', 'Manakah bilangan genap?', '2', '3', '4', '7', '8', 'A,C,E', '10'],
            ['benar_salah', 'Matahari terbit di timur.', '', '', '', '', '', 'benar', '5'],
            ['isian_singkat', 'Sebutkan ibukota Jawa Barat!', '', '', '', '', '', 'Bandung', '5'],
            ['essay', 'Jelaskan proses fotosintesis!', '', '', '', '', '', '', '20'],
        ];

        foreach ($examples as $r => $row) {
            $col = 'A';
            foreach ($row as $cell) {
                $sheet->setCellValue($col . ($r + 2), $cell);
                $col++;
            }
        }

        foreach (range('A', 'I') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        ob_end_clean();
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_import_soal.xlsx"',
        ]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $file->move(public_path('uploads/questions'), $filename);
        $url = asset('uploads/questions/' . $filename);

        return response()->json(['location' => $url]);
    }
}
