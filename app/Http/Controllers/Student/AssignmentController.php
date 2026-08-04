<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $assignments = Assignment::with('subject', 'teacher')
            ->where('class_id', $student->class_id)
            ->latest()
            ->paginate(10);

        $submittedIds = AssignmentSubmission::where('student_id', $student->id)
            ->pluck('assignment_id')
            ->toArray();

        return view('student.assignments.index', compact('assignments', 'submittedIds', 'student'));
    }

    public function show(Assignment $assignment)
    {
        $student = auth()->user()->student;
        if (!$student || $student->class_id !== $assignment->class_id) {
            abort(403);
        }

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        return view('student.assignments.show', compact('assignment', 'submission', 'student'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $student = auth()->user()->student;
        if (!$student || $student->class_id !== $assignment->class_id) {
            abort(403);
        }

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpeg,png,jpg|max:10240',
            'notes' => 'nullable|string|max:1000',
        ]);

        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing && $existing->file_path) {
            Storage::disk('public')->delete($existing->file_path);
        }

        $validated['file_path'] = $request->file('file')->store('assignment-submissions', 'public');
        $validated['assignment_id'] = $assignment->id;
        $validated['student_id'] = $student->id;
        $validated['submitted_at'] = now();

        AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => $student->id],
            $validated
        );

        return redirect()->route('student.assignments.show', $assignment)
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }
}
