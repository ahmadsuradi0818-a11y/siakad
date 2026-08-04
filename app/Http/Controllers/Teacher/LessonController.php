<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $lessons = Lesson::with('class')
            ->where('teacher_id', $userId)
            ->paginate(10);

        return view('teacher.lessons.index', compact('lessons'));
    }

    public function create()
    {
        $userId = auth()->id();
        $classIds = $this->getAccessibleClassIds($userId);
        $myClasses = ClassModel::whereIn('id', $classIds)->get();
        return view('teacher.lessons.create', compact('myClasses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,docx,doc|max:20480',
            'video_link' => 'nullable|string|max:2048',
        ]);

        $validated['teacher_id'] = auth()->id();
        $validated['file_path'] = $request->file('file')->store('uploads', 'public');

        Lesson::create($validated);

        return redirect()->route('teacher.lessons.index')
            ->with('success', 'Materi berhasil diunggah.');
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return redirect()->route('teacher.lessons.index')
            ->with('success', 'Materi berhasil dihapus.');
    }
}
