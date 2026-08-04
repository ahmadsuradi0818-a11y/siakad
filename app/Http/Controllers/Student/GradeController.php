<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamResult;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->user()->student;

        $query = ExamResult::with('exam.subject', 'exam.teacher')
            ->where('student_id', $student->id)
            ->whereNotNull('score');

        if ($request->filled('subject_id')) {
            $query->whereHas('exam', fn($q) => $q->where('subject_id', $request->subject_id));
        }

        $results = $query->orderBy('created_at', 'desc')->get();

        $grouped = $results->groupBy(fn($r) => $r->exam->subject?->name ?? 'Tanpa Mapel');

        $subjects = \App\Models\Subject::orderBy('name')->get();

        return view('student.grades.index', compact('grouped', 'subjects'));
    }
}
