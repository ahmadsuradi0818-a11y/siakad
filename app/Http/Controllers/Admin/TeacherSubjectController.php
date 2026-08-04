<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherSubjectController extends Controller
{
    public function index()
    {
        $teachers = User::where('role', 'guru')
            ->with('subjects', 'teachingClasses', 'teacher')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.teacher-subjects.index', compact('teachers'));
    }

    public function edit(User $teacher)
    {
        if ($teacher->role !== 'guru') {
            abort(404);
        }

        $teacher->load('subjects', 'teachingClasses');
        $subjects = Subject::orderBy('name')->get();
        $classes = ClassModel::orderBy('name')->get();

        $assigned = DB::table('teacher_subjects')
            ->where('teacher_id', $teacher->id)
            ->get()
            ->keyBy(fn($item) => $item->subject_id . '-' . $item->class_id);

        return view('admin.teacher-subjects.edit', compact('teacher', 'subjects', 'classes', 'assigned'));
    }

    public function update(Request $request, User $teacher)
    {
        if ($teacher->role !== 'guru') {
            abort(404);
        }

        $validated = $request->validate([
            'assignments' => 'nullable|array',
            'assignments.*' => 'nullable|array',
            'assignments.*.*' => 'nullable|in:1',
        ]);

        DB::transaction(function () use ($teacher, $validated) {
            DB::table('teacher_subjects')->where('teacher_id', $teacher->id)->delete();

            $rows = [];
            foreach (($validated['assignments'] ?? []) as $subjectId => $classes) {
                foreach (($classes ?? []) as $classId => $val) {
                    $rows[] = [
                        'teacher_id' => $teacher->id,
                        'subject_id' => $subjectId,
                        'class_id' => $classId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($rows)) {
                DB::table('teacher_subjects')->insert($rows);
            }
        });

        return redirect()->route('admin.teacher-subjects.index')
            ->with('success', 'Mata pelajaran dan kelas berhasil diperbarui untuk ' . $teacher->name);
    }
}
