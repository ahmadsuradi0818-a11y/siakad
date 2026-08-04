<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\StudentGrade;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

abstract class Controller
{
    protected function getAccessibleClassIds($userId)
    {
        $homeroomClassIds = ClassModel::where('homeroom_teacher_id', $userId)->pluck('id');

        $teacherSubjectClassIds = DB::table('teacher_subjects')
            ->where('teacher_id', $userId)
            ->pluck('class_id')
            ->unique()
            ->values();

        $subjectIds = Subject::whereHas('teachers', fn($q) => $q->where('users.id', $userId))->pluck('id');

        $examClassIds = Exam::whereIn('subject_id', $subjectIds)
            ->whereHas('classes')
            ->with('classes')
            ->get()
            ->flatMap(fn($e) => $e->classes->pluck('id'))
            ->unique()
            ->values();

        $assignmentClassIds = Assignment::whereIn('subject_id', $subjectIds)
            ->pluck('class_id')
            ->unique()
            ->values();

        $gradeClassIds = StudentGrade::whereIn('subject_id', $subjectIds)
            ->pluck('class_id')
            ->unique()
            ->values();

        return $homeroomClassIds
            ->concat($teacherSubjectClassIds)
            ->concat($examClassIds)
            ->concat($assignmentClassIds)
            ->concat($gradeClassIds)
            ->unique()
            ->values()
            ->all();
    }
}
