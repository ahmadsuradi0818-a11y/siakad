<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $scheduleClassIds = TeachingSchedule::where('teacher_id', $userId)
            ->distinct('class_id')
            ->pluck('class_id');

        $myClasses = ClassModel::whereIn('id', $scheduleClassIds)->get();

        $subjectIds = TeachingSchedule::where('teacher_id', $userId)
            ->distinct('subject_id')
            ->pluck('subject_id');
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        $query = Student::with('user', 'class')->whereIn('class_id', $scheduleClassIds);

        if ($request->filled('class_id') && in_array($request->class_id, $scheduleClassIds->toArray())) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('subject_id')) {
            $scheduleClassIdsForSubject = TeachingSchedule::where('teacher_id', $userId)
                ->where('subject_id', $request->subject_id)
                ->distinct('class_id')
                ->pluck('class_id');
            $query->whereIn('class_id', $scheduleClassIdsForSubject);
        }

        $students = $query->paginate(10);

        return view('teacher.students.index', compact('students', 'myClasses', 'subjects'));
    }
}
