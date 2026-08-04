<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamResult;
use App\Models\Question;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function soal()
    {
        $userId = auth()->id();
        $exams = Exam::with('class', 'classes', 'teacher', 'subject')
            ->where('teacher_id', $userId)
            ->paginate(10);
        return view('teacher.exams.soal', compact('exams'));
    }

    public function monitoring(Request $request)
    {
        $userId = auth()->id();

        $query = Exam::with('class', 'classes', 'teacher', 'subject')
            ->where('teacher_id', $userId)
            ->withCount('results as graded_count')
            ->addSelect(['student_count' => Student::selectRaw('count(*)')
                ->whereIn('class_id', function ($q) {
                    $q->select('class_id')->from('exam_class')->whereColumn('exam_id', 'exams.id');
                })
            ]);

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $exams = $query->latest('date')->paginate(10)->appends($request->query());

        $totalExams = Exam::where('teacher_id', $userId)->count();
        $totalGraded = Exam::where('teacher_id', $userId)->has('results')->count();

        return view('teacher.exams.monitoring', compact('exams', 'totalExams', 'totalGraded'));
    }

    public function monitoringDetail(Exam $exam)
    {
        $students = Student::with('user')
            ->whereIn('class_id', $exam->classes->pluck('id'))
            ->get();

        $results = ExamResult::where('exam_id', $exam->id)
            ->get()
            ->keyBy('student_id');

        $answerCounts = DB::table('exam_answers')
            ->where('exam_id', $exam->id)
            ->selectRaw('student_id, count(*) as total')
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        $totalQuestions = $exam->questions()->count();

        return view('teacher.exams.monitoring-detail', compact('exam', 'students', 'results', 'answerCounts', 'totalQuestions'));
    }

    public function toggleBlock(Exam $exam, Student $student)
    {
        $result = ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        if ($result) {
            $result->update(['is_blocked' => !$result->is_blocked]);
        } else {
            ExamResult::create([
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'is_blocked' => true,
                'started_at' => null,
                'score' => null,
            ]);
        }

        return redirect()->route('teacher.exams.monitoring-detail', $exam)
            ->with('success', 'Status blokir siswa berhasil diperbarui.');
    }

    public function nilai()
    {
        $userId = auth()->id();
        $subjects = Subject::all();
        $classes = ClassModel::all();
        $exams = Exam::with('class', 'classes', 'teacher', 'subject')
            ->where('teacher_id', $userId)
            ->paginate(10);
        return view('teacher.exams.nilai', compact('exams', 'subjects', 'classes'));
    }

    public function downloadNilai(Request $request)
    {
        $userId = auth()->id();
        $query = Exam::with('classes', 'subject', 'teacher')
            ->where('teacher_id', $userId);

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('class_id')) {
            $query->whereHas('classes', fn($q) => $q->where('class_id', $request->class_id));
        }

        $exams = $query->get();

        $rows = [];
        $no = 1;
        $typeLabels = ['uh' => 'Ulangan Harian', 'uts' => 'UTS', 'uas' => 'UAS', 'pat' => 'PAT', 'tryout' => 'Try Out'];

        foreach ($exams as $exam) {
            $students = Student::with('user')
                ->whereIn('class_id', $exam->classes->pluck('id'))
                ->get();

            $results = ExamResult::where('exam_id', $exam->id)
                ->get()
                ->keyBy('student_id');

            foreach ($students as $student) {
                $result = $results->get($student->id);
                $rows[] = [
                    $no++,
                    $student->user->name ?? '-',
                    $student->nis ?? '-',
                    $exam->classes->pluck('name')->join(', '),
                    $exam->name,
                    $exam->subject?->name ?? '-',
                    $typeLabels[$exam->type] ?? $exam->type,
                    $exam->date instanceof \Carbon\Carbon ? $exam->date->format('d/m/Y') : $exam->date,
                    $result ? number_format($result->score, 0) : '-',
                ];
            }
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['No', 'Nama Siswa', 'NIS', 'Kelas', 'Ujian', 'Mapel', 'Tipe', 'Tanggal', 'Nilai'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $col++;
        }

        $sheet->fromArray($rows, null, 'A2');

        foreach (range('A', 'I') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'nilai-ujian-' . date('Y-m-d-His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function nilaiDetail(Exam $exam)
    {
        $students = Student::with('user')
            ->whereIn('class_id', $exam->classes->pluck('id'))
            ->get();

        $results = ExamResult::where('exam_id', $exam->id)
            ->get()
            ->keyBy('student_id');

        $answerCounts = DB::table('exam_answers')
            ->where('exam_id', $exam->id)
            ->selectRaw('student_id, count(*) as total')
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        $totalQuestions = $exam->questions()->count();

        return view('teacher.exams.nilai-detail', compact('exam', 'students', 'results', 'answerCounts', 'totalQuestions'));
    }

    public function koreksi(Exam $exam, Student $student)
    {
        $exam->load('subject', 'teacher');
        $questions = Question::where('exam_id', $exam->id)->orderBy('id')->get();

        $answers = ExamAnswer::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('question_id');

        $result = ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        return view('teacher.exams.koreksi', compact('exam', 'student', 'questions', 'answers', 'result'));
    }

    public function storeKoreksi(Request $request, Exam $exam, Student $student)
    {
        $questions = Question::where('exam_id', $exam->id)->get();
        $earnedPoints = 0;
        $totalPoints = 0;

        foreach ($questions as $question) {
            if ($question->type === 'essay' || $question->type === 'isian_singkat') {
                $pointsEarned = $request->integer('points_' . $question->id, 0);
                $pointsEarned = min($pointsEarned, $question->points);
                ExamAnswer::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'student_id' => $student->id,
                        'question_id' => $question->id,
                    ],
                    [
                        'is_correct' => $pointsEarned > 0,
                        'points_earned' => $pointsEarned,
                    ]
                );
                $earnedPoints += $pointsEarned;
            } else {
                $answer = ExamAnswer::where('exam_id', $exam->id)
                    ->where('student_id', $student->id)
                    ->where('question_id', $question->id)
                    ->first();

                if ($answer) {
                    $earnedPoints += $answer->points_earned ?? ($answer->is_correct ? $question->points : 0);
                }
            }
            $totalPoints += $question->points;
        }

        $score = $totalPoints > 0 ? $earnedPoints : 0;

        ExamResult::updateOrCreate(
            ['exam_id' => $exam->id, 'student_id' => $student->id],
            ['score' => $score]
        );

        return redirect()->route('teacher.exams.nilai-detail', $exam)
            ->with('success', 'Nilai berhasil diperbarui. (' . $earnedPoints . ' / ' . $totalPoints . ' poin)');
    }

    public function index()
    {
        $userId = auth()->id();
        $exams = Exam::with('class', 'classes', 'teacher', 'subject')
            ->where('teacher_id', $userId)
            ->paginate(10);

        return view('teacher.exams.index', compact('exams'));
    }

    public function create()
    {
        $userId = auth()->id();
        $classIds = $this->getAccessibleClassIds($userId);
        $myClasses = ClassModel::whereIn('id', $classIds)->get();
        $subjects = Subject::all();
        return view('teacher.exams.create', compact('myClasses', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'type' => 'required|in:uh,uts,uas,pat,tryout',
            'anti_curang' => 'nullable|boolean',
        ]);

        $validated['teacher_id'] = auth()->id();
        $validated['anti_curang'] = $request->boolean('anti_curang');
        $validated['class_id'] = $request->class_ids[0];

        $exam = Exam::create($validated);
        $exam->classes()->sync($request->class_ids);

        return redirect()->route('teacher.exams.index')
            ->with('success', 'Ujian berhasil dibuat.');
    }

    public function inputNilai(Exam $exam)
    {
        $students = Student::with('user')
            ->whereIn('class_id', $exam->classes->pluck('id'))
            ->get();

        $existingResults = ExamResult::where('exam_id', $exam->id)
            ->get()
            ->keyBy('student_id');

        return view('teacher.exams.input-nilai', compact('exam', 'students', 'existingResults'));
    }

    public function storeNilai(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($validated['scores'] as $studentId => $score) {
            ExamResult::updateOrCreate(
                ['exam_id' => $exam->id, 'student_id' => $studentId],
                ['score' => $score]
            );
        }

        return redirect()->route('teacher.exams.index')
            ->with('success', 'Nilai berhasil diinput.');
    }

    public function duplicate(Exam $exam)
    {
        $exam->load('questions', 'classes');

        $newExam = Exam::create([
            'name' => $exam->name . ' (Susulan)',
            'class_id' => $exam->class_id,
            'teacher_id' => auth()->id(),
            'subject_id' => $exam->subject_id,
            'date' => $exam->date,
            'start_time' => $exam->start_time,
            'end_time' => $exam->end_time,
            'type' => $exam->type,
            'anti_curang' => $exam->anti_curang,
        ]);

        $newExam->classes()->sync($exam->classes->pluck('id'));

        foreach ($exam->questions as $question) {
            $newExam->questions()->create([
                'question_text' => $question->question_text,
                'type' => $question->type,
                'options' => $question->options,
                'correct_answer' => $question->correct_answer,
                'points' => $question->points,
                'question_bank_id' => $question->question_bank_id,
            ]);
        }

        return redirect()->route('teacher.exams.soal')
            ->with('success', 'Ujian "' . $exam->name . '" berhasil digandakan sebagai "' . $newExam->name . '".');
    }
}
