<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeachingSchedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $schedules = TeachingSchedule::with('subject', 'class')
            ->where('teacher_id', $userId)
            ->orderByRaw("FIELD(day_of_week, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $totalKelas = TeachingSchedule::where('teacher_id', $userId)->distinct('class_id')->count('class_id');
        $totalMapel = TeachingSchedule::where('teacher_id', $userId)->distinct('subject_id')->count('subject_id');
        $totalJam = TeachingSchedule::where('teacher_id', $userId)->count();

        return view('teacher.schedule.index', compact('schedules', 'days', 'totalKelas', 'totalMapel', 'totalJam'));
    }
}
