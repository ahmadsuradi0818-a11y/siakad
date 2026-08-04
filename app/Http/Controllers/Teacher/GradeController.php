<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Presence;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = auth()->id();
        $academicYears = AcademicYear::latest()->get();
        $selectedYear = $request->filled('academic_year_id')
            ? AcademicYear::find($request->academic_year_id)
            : AcademicYear::where('is_active', true)->first();

        $schedulePairs = TeachingSchedule::where('teacher_id', $teacherId)
            ->select('subject_id', 'class_id')
            ->distinct()
            ->get();

        $pairs = collect();
        foreach ($schedulePairs as $p) {
            $class = ClassModel::find($p->class_id);
            $subject = Subject::find($p->subject_id);
            if ($class && $subject) {
                $pairs->push(['class' => $class, 'subject' => $subject]);
            }
        }

        $pairs = $pairs->unique(fn($p) => $p['class']->id . '-' . $p['subject']->id)
            ->sortBy(fn($p) => $p['class']->name . ' - ' . $p['subject']->name)
            ->values();

        return view('teacher.grades.index', compact('pairs', 'academicYears', 'selectedYear'));
    }

    public function show(Request $request, $classId, $subjectId)
    {
        $teacherId = auth()->id();
        $class = ClassModel::findOrFail($classId);
        $subject = Subject::findOrFail($subjectId);

        $this->authorizeAccess($teacherId, $class, $subject);

        $academicYears = AcademicYear::latest()->get();
        $selectedYear = $request->filled('academic_year_id')
            ? AcademicYear::findOrFail($request->academic_year_id)
            : AcademicYear::where('is_active', true)->first();

        $students = Student::with('user')
            ->where('class_id', $classId)
            ->orderBy('nis')
            ->get();

        $recalculate = $request->boolean('recalculate');

        $grades = [];

        foreach ($students as $student) {
            $saved = StudentGrade::where('student_id', $student->id)
                ->where('subject_id', $subjectId)
                ->where('academic_year_id', $selectedYear->id)
                ->first();

            if ($saved && !$recalculate) {
                $grades[$student->id] = $saved;
            } else {
                $grades[$student->id] = $this->computeGrade($student, $subject, $selectedYear);
            }
        }

        if ($recalculate) {
            foreach ($students as $student) {
                $computed = $this->computeGrade($student, $subject, $selectedYear);
                StudentGrade::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $subjectId,
                        'academic_year_id' => $selectedYear->id,
                    ],
                    [
                        'subject_name' => $subject->name,
                        'class_id' => $classId,
                        'teacher_id' => $teacherId,
                        'attendance_score' => $computed->attendance_score,
                        'assignment_score' => $computed->assignment_score,
                        'daily_test_avg' => $computed->daily_test_avg,
                        'midterm_score' => $computed->midterm_score,
                        'final_score' => $computed->final_score,
                        'final_grade' => $computed->final_grade,
                    ]
                );
            }

            return redirect()->route('teacher.grades.show', [
                $classId, $subjectId,
                'academic_year_id' => $selectedYear->id,
            ])->with('success', 'Nilai berhasil dihitung ulang dari sistem.');
        }

        return view('teacher.grades.show', compact(
            'class', 'subject', 'students', 'grades', 'academicYears', 'selectedYear'
        ));
    }

    public function store(Request $request, $classId, $subjectId)
    {
        $teacherId = auth()->id();
        $class = ClassModel::findOrFail($classId);
        $subject = Subject::findOrFail($subjectId);
        $academicYear = AcademicYear::findOrFail($request->academic_year_id);

        $this->authorizeAccess($teacherId, $class, $subject);

        $validated = $request->validate([
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.attendance_score' => 'nullable|numeric|min:0|max:100',
            'grades.*.assignment_score' => 'nullable|numeric|min:0|max:100',
            'grades.*.daily_test_avg' => 'nullable|numeric|min:0|max:100',
            'grades.*.midterm_score' => 'nullable|numeric|min:0|max:100',
            'grades.*.final_score' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($validated['grades'] as $data) {
            $components = [];
            if (($data['attendance_score'] ?? null) !== null) $components[] = (float) $data['attendance_score'];
            if (($data['assignment_score'] ?? null) !== null) $components[] = (float) $data['assignment_score'];
            if (($data['daily_test_avg'] ?? null) !== null) $components[] = (float) $data['daily_test_avg'];
            if (($data['midterm_score'] ?? null) !== null) $components[] = (float) $data['midterm_score'];
            if (($data['final_score'] ?? null) !== null) $components[] = (float) $data['final_score'];

            $finalGrade = !empty($components) ? round(array_sum($components) / count($components), 2) : null;

            StudentGrade::updateOrCreate(
                [
                    'student_id' => $data['student_id'],
                    'subject_id' => $subjectId,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'subject_name' => $subject->name,
                    'class_id' => $classId,
                    'teacher_id' => $teacherId,
                    'attendance_score' => $data['attendance_score'] ?? null,
                    'assignment_score' => $data['assignment_score'] ?? null,
                    'daily_test_avg' => $data['daily_test_avg'] ?? null,
                    'midterm_score' => $data['midterm_score'] ?? null,
                    'final_score' => $data['final_score'] ?? null,
                    'final_grade' => $finalGrade,
                ]
            );
        }

        return redirect()->route('teacher.grades.show', [$classId, $subjectId, 'academic_year_id' => $academicYear->id])
            ->with('success', 'Nilai berhasil disimpan.');
    }

    private function computeGrade(Student $student, Subject $subject, AcademicYear $academicYear): StudentGrade
    {
        $startDate = $academicYear->start_date;
        $endDate = $academicYear->end_date;

        $totalPresences = Presence::where('student_id', $student->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->count();

        $alphaCount = Presence::where('student_id', $student->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'alpha')
            ->count();

        $attendanceScore = $totalPresences > 0
            ? round((($totalPresences - $alphaCount) / $totalPresences) * 100, 2)
            : null;

        $assignmentScore = Assignment::where('teacher_id', auth()->id())
            ->where('subject_id', $subject->id)
            ->whereHas('class', fn($q) => $q->where('classes.id', $student->class_id))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->flatMap->submissions
            ->where('student_id', $student->id)
            ->whereNotNull('score')
            ->avg('score');

        $assignmentScore = $assignmentScore !== null ? round((float) $assignmentScore, 2) : null;

        $examIds = Exam::where('subject_id', $subject->id)
            ->whereHas('classes', fn($c) => $c->where('classes.id', $student->class_id))
            ->whereBetween('date', [$startDate, $endDate])
            ->pluck('id');

        $results = ExamResult::whereIn('exam_id', $examIds)
            ->where('student_id', $student->id)
            ->get();

        $uhScores = [];
        $midtermScore = null;
        $finalScore = null;

        foreach ($results as $result) {
            if ($result->score === null) continue;
            switch ($result->exam->type) {
                case 'uh':
                    $uhScores[] = (float) $result->score;
                    break;
                case 'uts':
                    $midtermScore = (float) $result->score;
                    break;
                case 'uas':
                case 'pat':
                    $finalScore = (float) $result->score;
                    break;
            }
        }

        $uhAvg = !empty($uhScores) ? round(array_sum($uhScores) / count($uhScores), 2) : null;

        $components = [];
        if ($attendanceScore !== null) $components[] = $attendanceScore;
        if ($assignmentScore !== null) $components[] = $assignmentScore;
        if ($uhAvg !== null) $components[] = $uhAvg;
        if ($midtermScore !== null) $components[] = $midtermScore;
        if ($finalScore !== null) $components[] = $finalScore;

        $finalGrade = !empty($components) ? round(array_sum($components) / count($components), 2) : null;

        $grade = new StudentGrade();
        $grade->attendance_score = $attendanceScore;
        $grade->assignment_score = $assignmentScore;
        $grade->daily_test_avg = $uhAvg;
        $grade->midterm_score = $midtermScore;
        $grade->final_score = $finalScore;
        $grade->final_grade = $finalGrade;

        return $grade;
    }

    private function authorizeAccess(int $teacherId, ClassModel $class, Subject $subject): void
    {
        $hasSchedule = TeachingSchedule::where('teacher_id', $teacherId)
            ->where('subject_id', $subject->id)
            ->where('class_id', $class->id)
            ->exists();

        if ($hasSchedule) return;

        abort(403, 'Anda tidak memiliki akses untuk mengolah nilai pada kelas dan mata pelajaran ini.');
    }
}
