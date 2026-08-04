<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassModel;
use App\Models\StudentGrade;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::with('class', 'teacher', 'subject')
            ->latest()
            ->paginate(10);
        return view('admin.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $classes = ClassModel::all();
        $subjects = Subject::all();
        $teachers = User::where('role', 'guru')->get();
        return view('admin.assignments.create', compact('classes', 'subjects', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpeg,png,jpg|max:10240',
            'due_date' => 'required|date',
        ]);

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('assignments', 'public');
        }

        Assignment::create($validated);

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function edit(Assignment $assignment)
    {
        $classes = ClassModel::all();
        $subjects = Subject::all();
        $teachers = User::where('role', 'guru')->get();
        return view('admin.assignments.edit', compact('assignment', 'classes', 'subjects', 'teachers'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpeg,png,jpg|max:10240',
            'due_date' => 'required|date',
        ]);

        if ($request->hasFile('file')) {
            if ($assignment->file_path) {
                Storage::disk('public')->delete($assignment->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('assignments', 'public');
        }

        $assignment->update($validated);

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Assignment $assignment)
    {
        if ($assignment->file_path) {
            Storage::disk('public')->delete($assignment->file_path);
        }
        $assignment->delete();

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Tugas berhasil dihapus.');
    }

    public function nilai()
    {
        $assignments = Assignment::with('class', 'subject', 'teacher')
            ->withCount('submissions')
            ->latest()
            ->paginate(10);
        return view('admin.assignments.nilai', compact('assignments'));
    }

    public function nilaiDetail(Assignment $assignment)
    {
        $assignment->load('class', 'subject', 'teacher');
        $students = \App\Models\Student::with('user')
            ->where('class_id', $assignment->class_id)
            ->get();
        $submissions = $assignment->submissions()->get()->keyBy('student_id');

        return view('admin.assignments.nilai-detail', compact('assignment', 'students', 'submissions'));
    }

    public function storeNilai(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($validated['scores'] as $studentId => $score) {
            if ($score !== null && $score !== '') {
                AssignmentSubmission::updateOrCreate(
                    ['assignment_id' => $assignment->id, 'student_id' => $studentId],
                    ['score' => $score]
                );
            }
        }

        $academicYear = AcademicYear::where('is_active', true)->first();
        if ($academicYear) {
            $startDate = $academicYear->start_date;
            $endDate = $academicYear->end_date;

            foreach ($validated['scores'] as $studentId => $score) {
                $avgScore = Assignment::where('teacher_id', $assignment->teacher_id)
                    ->where('subject_id', $assignment->subject_id)
                    ->where('class_id', $assignment->class_id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get()
                    ->flatMap->submissions
                    ->where('student_id', $studentId)
                    ->whereNotNull('score')
                    ->avg('score');

                $avgScore = $avgScore !== null ? round((float) $avgScore, 2) : null;

                $grade = StudentGrade::where('student_id', $studentId)
                    ->where('subject_id', $assignment->subject_id)
                    ->where('academic_year_id', $academicYear->id)
                    ->first();

                if ($grade) {
                    $grade->assignment_score = $avgScore;

                    $components = [];
                    if ($grade->attendance_score !== null) $components[] = (float) $grade->attendance_score;
                    if ($grade->assignment_score !== null) $components[] = (float) $grade->assignment_score;
                    if ($grade->daily_test_avg !== null) $components[] = (float) $grade->daily_test_avg;
                    if ($grade->midterm_score !== null) $components[] = (float) $grade->midterm_score;
                    if ($grade->final_score !== null) $components[] = (float) $grade->final_score;

                    $grade->final_grade = !empty($components) ? round(array_sum($components) / count($components), 2) : null;
                    $grade->save();
                }
            }
        }

        return redirect()->route('admin.assignments.nilai-detail', $assignment)
            ->with('success', 'Nilai tugas berhasil disimpan.');
    }
}
