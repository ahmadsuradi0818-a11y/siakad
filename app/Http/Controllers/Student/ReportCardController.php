<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ReportCard;
use App\Models\StudentGrade;

class ReportCardController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        $reportCards = ReportCard::with('academicYear', 'class')
            ->where('student_id', $student->id)
            ->where('status', 'finalized')
            ->latest()
            ->paginate(10);

        return view('student.report-cards.index', compact('reportCards'));
    }

    public function show(ReportCard $reportCard)
    {
        $student = auth()->user()->student;

        if ($reportCard->student_id !== $student->id || $reportCard->status !== 'finalized') {
            abort(403);
        }

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

        return view('student.report-cards.show', compact('reportCard', 'schoolProfile', 'studentGrades'));
    }
}
