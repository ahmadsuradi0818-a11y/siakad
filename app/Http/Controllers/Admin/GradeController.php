<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\StudentGrade;
use App\Models\Subject;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $academicYears = AcademicYear::latest()->get();
        $classes = ClassModel::all();
        $subjects = Subject::orderBy('name')->get();

        $grades = collect();
        $selectedClass = null;
        $selectedSubject = null;

        if ($request->filled('class_id') && $request->filled('subject_id') && $request->filled('academic_year_id')) {
            $selectedClass = ClassModel::find($request->class_id);
            $selectedSubject = Subject::find($request->subject_id);

            $grades = StudentGrade::with(['student.user', 'subject', 'class'])
                ->where('class_id', $request->class_id)
                ->where('subject_id', $request->subject_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->orderBy(\App\Models\Student::select('nis')->whereColumn('students.id', 'student_grades.student_id'))
                ->get();
        }

        return view('admin.grades.index', compact('academicYears', 'classes', 'subjects', 'grades', 'selectedClass', 'selectedSubject'));
    }

    public function show(Request $request, $classId, $subjectId)
    {
        $academicYears = AcademicYear::latest()->get();
        $class = ClassModel::findOrFail($classId);
        $subject = Subject::findOrFail($subjectId);

        $selectedYear = $request->filled('academic_year_id')
            ? AcademicYear::findOrFail($request->academic_year_id)
            : AcademicYear::where('is_active', true)->first();

        $grades = StudentGrade::with(['student.user'])
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->where('academic_year_id', $selectedYear->id)
            ->orderBy(\App\Models\Student::select('nis')->whereColumn('students.id', 'student_grades.student_id'))
            ->get();

        return view('admin.grades.show', compact('class', 'subject', 'grades', 'academicYears', 'selectedYear'));
    }

    public function destroy($id)
    {
        $grade = StudentGrade::findOrFail($id);
        $grade->delete();

        return redirect()->back()->with('success', 'Data nilai berhasil dihapus.');
    }
}
