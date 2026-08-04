<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use App\Models\Subject;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;

class BankSoalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $subjects = Subject::orderBy('name')->get();

        if ($user->role === 'admin') {
            $questions = QuestionBank::with('createdBy', 'subject')->orderBy('id', 'desc')->paginate(20);
            return view('admin.bank-soal.index', compact('questions', 'subjects'));
        }

        $questions = QuestionBank::where('created_by', $user->id)
            ->orWhereHas('createdBy', fn($q) => $q->where('role', 'admin'))
            ->with('createdBy', 'subject')
            ->orderBy('id', 'desc')
            ->paginate(20);
        return view('teacher.bank-soal.index', compact('questions', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:pilihan_ganda,pilihan_ganda_kompleks,benar_salah,isian_singkat,essay',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string|distinct:ignore',
            'correct_answer' => 'nullable|string|max:255',
            'points' => 'required|integer|min:0',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        if (in_array($validated['type'], ['pilihan_ganda', 'pilihan_ganda_kompleks'])) {
            $validated['options'] = array_values(array_filter($validated['options'] ?? []));
        } else {
            $validated['options'] = null;
        }

        $validated['created_by'] = auth()->id();

        QuestionBank::create($validated);

        $route = auth()->user()->role === 'admin' ? 'admin.bank-soal.index' : 'teacher.bank-soal.index';
        return redirect()->route($route)->with('success', 'Soal berhasil ditambahkan ke bank.');
    }

    public function update(Request $request, QuestionBank $questionBank)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:pilihan_ganda,pilihan_ganda_kompleks,benar_salah,isian_singkat,essay',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string|distinct:ignore',
            'correct_answer' => 'nullable|string|max:255',
            'points' => 'required|integer|min:0',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        if (in_array($validated['type'], ['pilihan_ganda', 'pilihan_ganda_kompleks'])) {
            $validated['options'] = array_values(array_filter($validated['options'] ?? []));
        } else {
            $validated['options'] = null;
        }

        $questionBank->update($validated);

        $route = auth()->user()->role === 'admin' ? 'admin.bank-soal.index' : 'teacher.bank-soal.index';
        return redirect()->route($route)->with('success', 'Soal bank berhasil diperbarui.');
    }

    public function destroy(QuestionBank $questionBank)
    {
        $questionBank->delete();

        $route = auth()->user()->role === 'admin' ? 'admin.bank-soal.index' : 'teacher.bank-soal.index';
        return redirect()->route($route)->with('success', 'Soal bank berhasil dihapus.');
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
                $subjectName = trim($row[9] ?? '');

                if (empty($questionText) || empty($type)) {
                    $errors[] = "Baris " . ($idx + 2) . ": Soal dan Tipe wajib diisi";
                    continue;
                }

                if (!in_array($type, $validTypes)) {
                    $errors[] = "Baris " . ($idx + 2) . ": Tipe '$type' tidak valid";
                    continue;
                }

                $subjectId = null;
                if (!empty($subjectName)) {
                    $subject = Subject::where('name', $subjectName)->first();
                    $subjectId = $subject?->id;
                }

                $data = [
                    'question_text' => $questionText,
                    'type' => $type,
                    'correct_answer' => $correctAnswer ?: null,
                    'points' => max(0, $points),
                    'created_by' => auth()->id(),
                    'subject_id' => $subjectId,
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

                QuestionBank::create($data);
                $imported++;
            }
        } catch (\Exception $e) {
            $route = auth()->user()->role === 'admin' ? 'admin.bank-soal.index' : 'teacher.bank-soal.index';
            return redirect()->route($route)->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }

        $message = "Berhasil mengimport $imported soal ke bank.";
        if (!empty($errors)) {
            $message .= " " . implode('; ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= " (dan " . (count($errors) - 5) . " error lainnya)";
            }
        }

        $route = auth()->user()->role === 'admin' ? 'admin.bank-soal.index' : 'teacher.bank-soal.index';
        return redirect()->route($route)->with($imported > 0 ? 'success' : 'error', $message);
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

    public function ambil(Request $request)
    {
        $ids = $request->input('ids', []);
        $examId = $request->input('exam_id');

        if (empty($ids) || !$examId) {
            return back()->with('error', 'Pilih soal yang akan diambil.');
        }

        $bankQuestions = QuestionBank::whereIn('id', $ids)->get();
        $copied = 0;

        foreach ($bankQuestions as $bank) {
            \App\Models\Question::create([
                'exam_id' => $examId,
                'question_text' => $bank->question_text,
                'type' => $bank->type,
                'options' => $bank->options,
                'correct_answer' => $bank->correct_answer,
                'points' => $bank->points,
                'question_bank_id' => $bank->id,
            ]);
            $copied++;
        }

        return back()->with('success', "$copied soal berhasil diambil dari bank.");
    }
}
