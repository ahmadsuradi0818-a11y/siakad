<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamResult;
use App\Models\Question;
use App\Models\SchoolProfile;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with('class', 'classes', 'teacher', 'subject')->paginate(10);
        $classes = ClassModel::all();
        $teachers = User::where('role', 'guru')->get();
        $subjects = Subject::all();
        return view('admin.exams.index', compact('exams', 'classes', 'teachers', 'subjects'));
    }

    public function soal()
    {
        $exams = Exam::with('class', 'classes', 'teacher', 'subject')->paginate(10);
        return view('admin.exams.soal', compact('exams'));
    }

    public function monitoring(Request $request)
    {
        $query = Exam::with('class', 'classes', 'teacher', 'subject')
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

        $totalExams = Exam::count();
        $totalGraded = Exam::has('results')->count();
        $totalStudents = Student::count();

        return view('admin.exams.monitoring', compact('exams', 'totalExams', 'totalGraded', 'totalStudents'));
    }

    public function monitoringDetail(Exam $exam)
    {
        $students = Student::with('user')
            ->whereIn('class_id', $exam->classes->pluck('id'))
            ->get();

        $results = ExamResult::where('exam_id', $exam->id)
            ->get()
            ->keyBy('student_id');

        $answerCounts = \DB::table('exam_answers')
            ->where('exam_id', $exam->id)
            ->selectRaw('student_id, count(*) as total')
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        $totalQuestions = $exam->questions()->count();

        return view('admin.exams.monitoring-detail', compact('exam', 'students', 'results', 'answerCounts', 'totalQuestions'));
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

        return redirect()->route('admin.exams.monitoring-detail', $exam)
            ->with('success', 'Status blokir siswa berhasil diperbarui.');
    }

    public function nilai()
    {
        $subjects = Subject::all();
        $classes = ClassModel::all();
        $exams = Exam::with('class', 'classes', 'teacher', 'subject')->paginate(10);
        return view('admin.exams.nilai', compact('exams', 'subjects', 'classes'));
    }

    public function downloadNilai(Request $request)
    {
        $query = Exam::with('classes', 'subject', 'teacher');

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

        $answerCounts = \DB::table('exam_answers')
            ->where('exam_id', $exam->id)
            ->selectRaw('student_id, count(*) as total')
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        $totalQuestions = $exam->questions()->count();

        return view('admin.exams.nilai-detail', compact('exam', 'students', 'results', 'answerCounts', 'totalQuestions'));
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

        return view('admin.exams.koreksi', compact('exam', 'student', 'questions', 'answers', 'result'));
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

        return redirect()->route('admin.exams.nilai-detail', $exam)
            ->with('success', 'Nilai berhasil diperbarui. (' . $earnedPoints . ' / ' . $totalPoints . ' poin)');
    }

    public function create()
    {
        $classes = ClassModel::all();
        $teachers = User::where('role', 'guru')->get();
        $subjects = Subject::all();
        return view('admin.exams.create', compact('classes', 'teachers', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'type' => 'required|in:uh,uts,uas,pat,tryout',
            'anti_curang' => 'nullable|boolean',
        ]);

        $validated['anti_curang'] = $request->boolean('anti_curang');
        $validated['class_id'] = $request->class_ids[0];

        $exam = Exam::create($validated);
        $exam->classes()->sync($request->class_ids);

        return redirect()->route('admin.exams.index')
            ->with('success', 'Ujian berhasil ditambahkan.');
    }

    public function edit(Exam $exam)
    {
        $classes = ClassModel::all();
        $teachers = User::where('role', 'guru')->get();
        $subjects = Subject::all();
        return view('admin.exams.edit', compact('exam', 'classes', 'teachers', 'subjects'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'type' => 'required|in:uh,uts,uas,pat,tryout',
            'anti_curang' => 'nullable|boolean',
        ]);

        $validated['anti_curang'] = $request->boolean('anti_curang');
        $validated['class_id'] = $request->class_ids[0];

        $exam->update($validated);
        $exam->classes()->sync($request->class_ids);

        return redirect()->route('admin.exams.index')
            ->with('success', 'Ujian berhasil diperbarui.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('admin.exams.index')
            ->with('success', 'Ujian berhasil dihapus.');
    }

    public function duplicate(Exam $exam)
    {
        $exam->load('questions', 'classes');

        $newExam = Exam::create([
            'name' => $exam->name . ' (Susulan)',
            'class_id' => $exam->class_id,
            'teacher_id' => $exam->teacher_id,
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

        return redirect()->route('admin.exams.soal')
            ->with('success', 'Ujian "' . $exam->name . '" berhasil digandakan sebagai "' . $newExam->name . '".');
    }

    public function idCardSelectExam()
    {
        $exams = Exam::with('classes', 'subject')->orderBy('date', 'desc')->paginate(20);
        return view('admin.exams.id-card-select-exam', compact('exams'));
    }

    public function idCardIndex(Exam $exam)
    {
        $exam->load('classes', 'subject', 'teacher');
        $classIds = $exam->classes->pluck('id');
        $students = Student::with('user', 'class')
            ->whereIn('class_id', $classIds)
            ->orderBy('class_id')
            ->get();
        $school = SchoolProfile::first();

        return view('admin.exams.id-card-index', compact('exam', 'students', 'school'));
    }

    public function idCardPrint(Request $request, Exam $exam)
    {
        $ids = $request->input('students', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu siswa.');
        }

        $exam->load('classes', 'subject', 'teacher');
        $students = Student::with('user', 'class')->whereIn('id', $ids)->get();
        $school = SchoolProfile::first();

        return view('admin.exams.id-card-print', compact('exam', 'students', 'school'));
    }
}
