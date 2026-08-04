<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = ClassModel::with('homeroomTeacher', 'students')->paginate(10);
        $guruList = User::where('role', 'guru')->get();
        return view('admin.classes.index', compact('classes', 'guruList'));
    }

    public function create()
    {
        $guruList = User::where('role', 'guru')->get();
        return view('admin.classes.create', compact('guruList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ]);

        ClassModel::create($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(ClassModel $class)
    {
        $guruList = User::where('role', 'guru')->get();
        return view('admin.classes.edit', compact('class', 'guruList'));
    }

    public function update(Request $request, ClassModel $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ]);

        $class->update($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(ClassModel $class)
    {
        $class->delete();
        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
