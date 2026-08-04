<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $assignments = Assignment::with('class', 'subject')
            ->where('teacher_id', $userId)
            ->latest()
            ->paginate(10);
        return view('teacher.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $classes = ClassModel::all();
        $subjects = Subject::all();
        return view('teacher.assignments.create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpeg,png,jpg|max:10240',
            'due_date' => 'required|date',
        ]);

        $validated['teacher_id'] = auth()->id();

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('assignments', 'public');
        }

        Assignment::create($validated);

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function edit(Assignment $assignment)
    {
        $this->authorizeTeacher($assignment);
        $classes = ClassModel::all();
        $subjects = Subject::all();
        return view('teacher.assignments.edit', compact('assignment', 'classes', 'subjects'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $this->authorizeTeacher($assignment);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
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

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Assignment $assignment)
    {
        $this->authorizeTeacher($assignment);

        if ($assignment->file_path) {
            Storage::disk('public')->delete($assignment->file_path);
        }
        $assignment->delete();

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Tugas berhasil dihapus.');
    }

    public function nilai()
    {
        $userId = auth()->id();
        $assignments = Assignment::with('class', 'subject')
            ->withCount('submissions')
            ->where('teacher_id', $userId)
            ->latest()
            ->paginate(10);
        return view('teacher.assignments.nilai', compact('assignments'));
    }

    public function nilaiDetail(Assignment $assignment)
    {
        $this->authorizeTeacher($assignment);
        $assignment->load('class', 'subject');
        $students = Student::with('user')
            ->where('class_id', $assignment->class_id)
            ->get();
        $submissions = $assignment->submissions()->get()->keyBy('student_id');

        return view('teacher.assignments.nilai-detail', compact('assignment', 'students', 'submissions'));
    }

    public function storeNilai(Request $request, Assignment $assignment)
    {
        $this->authorizeTeacher($assignment);

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
            $teacherId = auth()->id();

            foreach ($validated['scores'] as $studentId => $score) {
                $avgScore = Assignment::where('teacher_id', $teacherId)
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

        return redirect()->route('teacher.assignments.nilai-detail', $assignment)
            ->with('success', 'Nilai tugas berhasil disimpan.');
    }

    private function authorizeTeacher(Assignment $assignment): void
    {
        if ($assignment->teacher_id !== auth()->id()) {
            abort(403);
        }
    }
}
