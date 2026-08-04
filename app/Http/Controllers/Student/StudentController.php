<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->user()->student;
        $classes = ClassModel::orderBy('name')->get();

        $query = Student::with('user', 'class');

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $students = $query->orderBy('nis')->paginate(20);

        return view('student.students.index', compact('students', 'classes'));
    }
}
