<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Presence;
use App\Models\ReportCard;
use App\Models\ReportCardSubject;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    public function index(Request $request)
    {
        $academicYears = AcademicYear::latest()->get();
        $classes = ClassModel::all();

        $query = ReportCard::with('student.user', 'class', 'academicYear', 'homeroomTeacher');

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reportCards = $query->latest()->paginate(10);

        return view('admin.report-cards.index', compact('reportCards', 'academicYears', 'classes'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $student = Student::with('class.homeroomTeacher')->findOrFail($validated['student_id']);
        $academicYear = AcademicYear::findOrFail($validated['academic_year_id']);

        $existing = ReportCard::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Raport sudah ada untuk siswa ini pada tahun ajaran tersebut.');
        }

        $subjectIdsFromExams = Subject::whereHas('exams', function ($q) use ($student, $academicYear) {
            $q->whereHas('results', function ($r) use ($student) {
                $r->where('student_id', $student->id);
            })
            ->whereHas('classes', function ($c) use ($student) {
                $c->where('classes.id', $student->class_id);
            })
            ->whereBetween('date', [$academicYear->start_date, $academicYear->end_date]);
        })->pluck('id');

        $subjectIdsFromGrades = StudentGrade::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->whereNotNull('final_grade')
            ->pluck('subject_id');

        $allSubjectIds = $subjectIdsFromExams->merge($subjectIdsFromGrades)->unique()->values()->all();

        $subjects = Subject::whereIn('id', $allSubjectIds)->orderBy('name')->get();

        if ($subjects->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data nilai untuk siswa ini pada tahun ajaran tersebut.');
        }

        $startDate = $academicYear->start_date;
        $endDate = $academicYear->end_date;

        $attendanceSick = Presence::where('student_id', $student->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'sakit')->count();
        $attendancePermit = Presence::where('student_id', $student->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'izin')->count();
        $attendanceAbsent = Presence::where('student_id', $student->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'alpha')->count();

        $reportCard = ReportCard::create([
            'student_id' => $student->id,
            'academic_year_id' => $academicYear->id,
            'class_id' => $student->class_id,
            'homeroom_teacher_id' => $student->class?->homeroom_teacher_id,
            'attendance_sick' => $attendanceSick,
            'attendance_permit' => $attendancePermit,
            'attendance_absent' => $attendanceAbsent,
            'status' => 'draft',
        ]);

        foreach ($subjects as $subject) {
            $studentGrade = \App\Models\StudentGrade::where('student_id', $student->id)
                ->where('subject_id', $subject->id)
                ->where('academic_year_id', $academicYear->id)
                ->first();

            if ($studentGrade && $studentGrade->final_grade !== null) {
                ReportCardSubject::create([
                    'report_card_id' => $reportCard->id,
                    'subject_id' => $subject->id,
                    'subject_name' => $subject->name,
                    'daily_test_avg' => $studentGrade->daily_test_avg,
                    'midterm_score' => $studentGrade->midterm_score,
                    'final_score' => $studentGrade->final_score,
                    'final_grade' => $studentGrade->final_grade,
                ]);
                continue;
            }

            $examIds = Exam::where('subject_id', $subject->id)
                ->whereHas('results', fn($r) => $r->where('student_id', $student->id))
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
                $exam = $result->exam;
                $score = $result->score;

                if ($score === null) continue;

                switch ($exam->type) {
                    case 'uh':
                        $uhScores[] = (float) $score;
                        break;
                    case 'uts':
                        $midtermScore = (float) $score;
                        break;
                    case 'uas':
                    case 'pat':
                        $finalScore = (float) $score;
                        break;
                }
            }

            $uhAvg = !empty($uhScores) ? round(array_sum($uhScores) / count($uhScores), 2) : null;

            $components = [];
            if ($uhAvg !== null) $components[] = $uhAvg;
            if ($midtermScore !== null) $components[] = $midtermScore;
            if ($finalScore !== null) $components[] = $finalScore;

            $finalGrade = !empty($components) ? round(array_sum($components) / count($components), 2) : null;

            ReportCardSubject::create([
                'report_card_id' => $reportCard->id,
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'daily_test_avg' => $uhAvg,
                'midterm_score' => $midtermScore,
                'final_score' => $finalScore,
                'final_grade' => $finalGrade,
            ]);
        }

        return redirect()->route('admin.report-cards.show', $reportCard)
            ->with('success', 'Raport berhasil dibuat.');
    }

    public function show(ReportCard $reportCard)
    {
        $reportCard->load([
            'student.user',
            'class',
            'academicYear',
            'homeroomTeacher',
            'subjects.subject',
            'extracurriculars',
        ]);

        $studentGrades = StudentGrade::where('student_id', $reportCard->student_id)
            ->where('academic_year_id', $reportCard->academic_year_id)
            ->get()
            ->keyBy('subject_id');

        $schoolProfile = \App\Models\SchoolProfile::first();

        return view('admin.report-cards.show', compact('reportCard', 'schoolProfile', 'studentGrades'));
    }

    public function destroy(ReportCard $reportCard)
    {
        $reportCard->delete();

        return redirect()->route('admin.report-cards.index')
            ->with('success', 'Raport berhasil dihapus.');
    }

    public function leger(Request $request)
    {
        $academicYears = AcademicYear::latest()->get();
        $classes = ClassModel::all();

        $students = collect();
        $allSubjects = collect();
        $gradeMap = []; // [student_id][subject_id] => StudentGrade
        $unsyncedCount = 0;

        if ($request->filled('class_id') && $request->filled('academic_year_id')) {
            $students = Student::with('user')
                ->where('class_id', $request->class_id)
                ->orderBy('nis')
                ->get();

            $sgSubjectIds = StudentGrade::where('class_id', $request->class_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->distinct()->pluck('subject_id');

            $rcSubjectIds = ReportCardSubject::whereHas('reportCard', function ($q) use ($request) {
                $q->where('class_id', $request->class_id)
                  ->where('academic_year_id', $request->academic_year_id);
            })->distinct()->pluck('subject_id');

            $allSubjectIds = $sgSubjectIds->merge($rcSubjectIds)->unique()->values();
            $allSubjects = Subject::whereIn('id', $allSubjectIds)->orderBy('name')->get();

            $studentGrades = StudentGrade::where('class_id', $request->class_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->get();

            foreach ($studentGrades as $sg) {
                $gradeMap[$sg->student_id][$sg->subject_id] = $sg;
            }

            $reportCards = ReportCard::with('subjects')
                ->where('class_id', $request->class_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->get()
                ->keyBy('student_id');

            foreach ($students as $student) {
                $rc = $reportCards->get($student->id);
                if (!$rc) continue;
                foreach ($rc->subjects as $rcs) {
                    $sg = $gradeMap[$student->id][$rcs->subject_id] ?? null;
                    if ($sg && $sg->final_grade !== null && $sg->final_grade != $rcs->final_grade) {
                        $unsyncedCount++;
                    }
                }
            }
        }

        return view('admin.report-cards.leger', compact('students', 'allSubjects', 'gradeMap', 'academicYears', 'classes', 'unsyncedCount'));
    }

    public function legerUpdate(Request $request)
    {
        $validated = $request->validate([
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.subject_id' => 'required|exists:subjects,id',
            'grades.*.final_grade' => 'nullable|numeric|min:0|max:100',
            'grades.*.capaian' => 'nullable|string',
        ]);

        $academicYearId = $request->academic_year_id ?? request('academic_year_id');

        foreach ($validated['grades'] as $data) {
            $student = Student::find($data['student_id']);
            if (!$student) continue;

            $grade = StudentGrade::updateOrCreate(
                [
                    'student_id' => $data['student_id'],
                    'subject_id' => $data['subject_id'],
                    'academic_year_id' => $academicYearId,
                ],
                [
                    'subject_name' => Subject::find($data['subject_id'])?->name,
                    'class_id' => $student->class_id,
                    'teacher_id' => auth()->id(),
                    'final_grade' => $data['final_grade'] ?? null,
                    'teacher_notes' => $data['capaian'] ?? null,
                ]
            );

            $rc = ReportCard::where('student_id', $data['student_id'])
                ->where('academic_year_id', $academicYearId)
                ->first();

            if ($rc) {
                ReportCardSubject::updateOrCreate(
                    [
                        'report_card_id' => $rc->id,
                        'subject_id' => $data['subject_id'],
                    ],
                    [
                        'subject_name' => Subject::find($data['subject_id'])?->name,
                        'final_grade' => $data['final_grade'] ?? null,
                        'teacher_notes' => $data['capaian'] ?? null,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Leger nilai berhasil diperbarui.');
    }

    public function syncFromGrades(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $reportCards = ReportCard::where('class_id', $validated['class_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->get();

        $updated = 0;
        $created = 0;

        foreach ($reportCards as $rc) {
            $existingSubjectIds = $rc->subjects->pluck('subject_id')->toArray();

            $studentGrades = StudentGrade::where('student_id', $rc->student_id)
                ->where('academic_year_id', $validated['academic_year_id'])
                ->whereNotNull('final_grade')
                ->get();

            foreach ($studentGrades as $sg) {
                $rcSubject = $rc->subjects->firstWhere('subject_id', $sg->subject_id);

                if ($rcSubject) {
                    $rcSubject->update([
                        'final_grade' => $sg->final_grade,
                        'daily_test_avg' => $sg->daily_test_avg,
                        'midterm_score' => $sg->midterm_score,
                        'final_score' => $sg->final_score,
                        'teacher_notes' => $sg->teacher_notes,
                    ]);
                    $updated++;
                } else {
                    ReportCardSubject::create([
                        'report_card_id' => $rc->id,
                        'subject_id' => $sg->subject_id,
                        'subject_name' => $sg->subject_name,
                        'daily_test_avg' => $sg->daily_test_avg,
                        'midterm_score' => $sg->midterm_score,
                        'final_score' => $sg->final_score,
                        'final_grade' => $sg->final_grade,
                        'teacher_notes' => $sg->teacher_notes,
                    ]);
                    $created++;
                }
            }
        }

        $total = $updated + $created;
        $msg = "Nilai berhasil disinkronisasi dari Olah Nilai. ($total mata pelajaran diproses";
        if ($created > 0) $msg .= ", $created baru";
        $msg .= ").";

        return redirect()->back()->with('success', $msg);
    }

    public function getStudentsByClass(Request $request)
    {
        $students = Student::with('user')
            ->where('class_id', $request->class_id)
            ->get()
            ->map(fn($s) => ['id' => $s->id, 'text' => $s->user->name . ' (' . $s->nis . ')']);

        return response()->json($students);
    }
}
