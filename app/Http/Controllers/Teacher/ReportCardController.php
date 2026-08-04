<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ReportCard;
use App\Models\ReportCardExtracurricular;
use App\Models\ReportCardSubject;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = auth()->id();
        $academicYears = AcademicYear::latest()->get();

        $query = Student::with('user', 'class', 'reportCards')
            ->whereHas('class.homeroomTeacher', function ($q) use ($teacherId) {
                $q->where('users.id', $teacherId);
            });

        if ($request->filled('academic_year_id')) {
            $query->whereHas('reportCards', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
            });
        }

        $students = $query->paginate(10);

        return view('teacher.report-cards.index', compact('students', 'academicYears'));
    }

    public function edit(ReportCard $reportCard)
    {
        $this->authorizeTeacher($reportCard);

        $reportCard->load([
            'student.user',
            'class',
            'academicYear',
            'subjects.subject',
            'extracurriculars',
        ]);

        $studentGrades = StudentGrade::where('student_id', $reportCard->student_id)
            ->where('academic_year_id', $reportCard->academic_year_id)
            ->get()
            ->keyBy('subject_id');

        return view('teacher.report-cards.edit', compact('reportCard', 'studentGrades'));
    }

    public function update(Request $request, ReportCard $reportCard)
    {
        $this->authorizeTeacher($reportCard);

        $validated = $request->validate([
            'attitude_religious' => 'nullable|string',
            'attitude_social' => 'nullable|string',
            'homeroom_notes' => 'nullable|string',
            'subjects' => 'nullable|array',
            'subjects.*.teacher_notes' => 'nullable|string',
            'subjects.*.daily_test_avg' => 'nullable|numeric|min:0|max:100',
            'subjects.*.midterm_score' => 'nullable|numeric|min:0|max:100',
            'subjects.*.final_score' => 'nullable|numeric|min:0|max:100',
            'subjects.*.final_grade' => 'nullable|numeric|min:0|max:100',
            'extracurriculars' => 'nullable|array',
            'extracurriculars.*.id' => 'nullable|exists:report_card_extracurriculars,id',
            'extracurriculars.*.activity_name' => 'required|string|max:255',
            'extracurriculars.*.description' => 'nullable|string',
            'new_extracurriculars' => 'nullable|array',
            'new_extracurriculars.*.activity_name' => 'nullable|string|max:255',
            'new_extracurriculars.*.description' => 'nullable|string',
            'status' => 'sometimes|in:draft,finalized',
        ]);

        $reportCard->update([
            'attitude_religious' => $validated['attitude_religious'] ?? null,
            'attitude_social' => $validated['attitude_social'] ?? null,
            'homeroom_notes' => $validated['homeroom_notes'] ?? null,
            'status' => $validated['status'] ?? $reportCard->status,
        ]);

        if (isset($validated['subjects'])) {
            foreach ($validated['subjects'] as $subjectId => $data) {
                ReportCardSubject::updateOrCreate(
                    [
                        'report_card_id' => $reportCard->id,
                        'subject_id' => $subjectId,
                    ],
                    [
                        'subject_name' => Subject::find($subjectId)?->name,
                        'teacher_notes' => $data['teacher_notes'] ?? null,
                        'daily_test_avg' => $data['daily_test_avg'] ?? null,
                        'midterm_score' => $data['midterm_score'] ?? null,
                        'final_score' => $data['final_score'] ?? null,
                        'final_grade' => $data['final_grade'] ?? null,
                    ]
                );

                StudentGrade::updateOrCreate(
                    [
                        'student_id' => $reportCard->student_id,
                        'subject_id' => $subjectId,
                        'academic_year_id' => $reportCard->academic_year_id,
                    ],
                    [
                        'subject_name' => Subject::find($subjectId)?->name,
                        'class_id' => $reportCard->class_id,
                        'teacher_id' => auth()->id(),
                        'final_grade' => $data['final_grade'] ?? null,
                        'teacher_notes' => $data['teacher_notes'] ?? null,
                    ]
                );
            }
        }

        if (isset($validated['extracurriculars'])) {
            foreach ($validated['extracurriculars'] as $data) {
                if (isset($data['id'])) {
                    ReportCardExtracurricular::find($data['id'])->update([
                        'activity_name' => $data['activity_name'],
                        'description' => $data['description'] ?? null,
                    ]);
                }
            }
        }

        if (isset($validated['new_extracurriculars'])) {
            foreach ($validated['new_extracurriculars'] as $data) {
                if (!empty($data['activity_name'])) {
                    $reportCard->extracurriculars()->create([
                        'activity_name' => $data['activity_name'],
                        'description' => $data['description'] ?? null,
                    ]);
                }
            }
        }

        if ($request->input('action') === 'finalize') {
            return redirect()->route('teacher.report-cards.show', $reportCard)
                ->with('success', 'Raport berhasil difinalisasi dan disimpan.');
        }

        return redirect()->route('teacher.report-cards.edit', $reportCard)
            ->with('success', 'Raport berhasil diperbarui.');
    }

    public function show(ReportCard $reportCard)
    {
        $this->authorizeTeacher($reportCard);

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

        foreach ($reportCard->subjects as $rcs) {
            $sg = $studentGrades->get($rcs->subject_id);
            if ($sg && $sg->final_grade !== null) {
                $rcs->final_grade = $sg->final_grade;
            }
            if ($sg && $sg->teacher_notes !== null) {
                $rcs->teacher_notes = $sg->teacher_notes;
            }
        }

        $schoolProfile = \App\Models\SchoolProfile::first();

        return view('teacher.report-cards.show', compact('reportCard', 'schoolProfile', 'studentGrades'));
    }

    public function leger(Request $request)
    {
        $teacherId = auth()->id();
        $academicYears = AcademicYear::latest()->get();

        $homeroomClass = \App\Models\ClassModel::where('homeroom_teacher_id', $teacherId)->first();

        if (!$homeroomClass) {
            $unsyncedCount = 0;
            return view('teacher.report-cards.leger', compact('academicYears', 'unsyncedCount'))->with('error', 'Anda bukan wali kelas.');
        }

        $students = collect();
        $allSubjects = collect();
        $gradeMap = [];
        $unsyncedCount = 0;

        if ($request->filled('academic_year_id')) {
            $students = Student::with('user')
                ->where('class_id', $homeroomClass->id)
                ->orderBy('nis')
                ->get();

            $sgSubjectIds = StudentGrade::where('class_id', $homeroomClass->id)
                ->where('academic_year_id', $request->academic_year_id)
                ->distinct()->pluck('subject_id');

            $rcSubjectIds = ReportCardSubject::whereHas('reportCard', function ($q) use ($request, $homeroomClass) {
                $q->where('class_id', $homeroomClass->id)
                  ->where('academic_year_id', $request->academic_year_id);
            })->distinct()->pluck('subject_id');

            $allSubjectIds = $sgSubjectIds->merge($rcSubjectIds)->unique()->values();
            $allSubjects = Subject::whereIn('id', $allSubjectIds)->orderBy('name')->get();

            $studentGrades = StudentGrade::where('class_id', $homeroomClass->id)
                ->where('academic_year_id', $request->academic_year_id)
                ->get();

            foreach ($studentGrades as $sg) {
                $gradeMap[$sg->student_id][$sg->subject_id] = $sg;
            }

            $reportCards = ReportCard::with('subjects')
                ->where('class_id', $homeroomClass->id)
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

        return view('teacher.report-cards.leger', compact('students', 'allSubjects', 'gradeMap', 'academicYears', 'homeroomClass', 'unsyncedCount'));
    }

    public function legerUpdate(Request $request)
    {
        $teacherId = auth()->id();
        $homeroomClass = \App\Models\ClassModel::where('homeroom_teacher_id', $teacherId)->first();

        if (!$homeroomClass) {
            return redirect()->back()->with('error', 'Anda bukan wali kelas.');
        }

        $validated = $request->validate([
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.subject_id' => 'required|exists:subjects,id',
            'grades.*.final_grade' => 'nullable|numeric|min:0|max:100',
            'grades.*.capaian' => 'nullable|string',
        ]);

        $academicYearId = $request->academic_year_id;

        foreach ($validated['grades'] as $data) {
            $student = Student::find($data['student_id']);
            if (!$student || $student->class_id !== $homeroomClass->id) continue;

            $grade = StudentGrade::updateOrCreate(
                [
                    'student_id' => $data['student_id'],
                    'subject_id' => $data['subject_id'],
                    'academic_year_id' => $academicYearId,
                ],
                [
                    'subject_name' => Subject::find($data['subject_id'])?->name,
                    'class_id' => $homeroomClass->id,
                    'teacher_id' => $teacherId,
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
        $teacherId = auth()->id();
        $homeroomClass = \App\Models\ClassModel::where('homeroom_teacher_id', $teacherId)->first();

        if (!$homeroomClass) {
            return redirect()->back()->with('error', 'Anda bukan wali kelas.');
        }

        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $reportCards = ReportCard::where('class_id', $homeroomClass->id)
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

    private function authorizeTeacher(ReportCard $reportCard): void
    {
        $teacherId = auth()->id();
        $class = $reportCard->class;

        if (!$class || $class->homeroom_teacher_id !== $teacherId) {
            abort(403, 'Anda bukan wali kelas siswa ini.');
        }
    }
}
