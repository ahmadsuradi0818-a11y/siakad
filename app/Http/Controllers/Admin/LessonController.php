<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with('class', 'teacher')->paginate(10);
        $classes = ClassModel::all();
        $teachers = User::where('role', 'guru')->get();
        return view('admin.lessons.index', compact('lessons', 'classes', 'teachers'));
    }

    public function create()
    {
        $classes = ClassModel::all();
        $teachers = User::where('role', 'guru')->get();
        return view('admin.lessons.create', compact('classes', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,docx,doc|max:20480',
            'video_link' => 'nullable|string|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('uploads', 'public');
        }

        Lesson::create($validated);

        return redirect()->route('admin.lessons.index')
            ->with('success', 'Materi pembelajaran berhasil ditambahkan.');
    }

    public function edit(Lesson $lesson)
    {
        $classes = ClassModel::all();
        $teachers = User::where('role', 'guru')->get();
        return view('admin.lessons.edit', compact('lesson', 'classes', 'teachers'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,docx,doc|max:20480',
            'video_link' => 'nullable|string|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('uploads', 'public');
        }

        $lesson->update($validated);

        return redirect()->route('admin.lessons.index')
            ->with('success', 'Materi pembelajaran berhasil diperbarui.');
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return redirect()->route('admin.lessons.index')
            ->with('success', 'Materi pembelajaran berhasil dihapus.');
    }
}
