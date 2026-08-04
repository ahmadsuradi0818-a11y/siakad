<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\Lesson;
use App\Models\Presence;
use App\Models\Student;
use App\Models\TeachingSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $scheduleClassIds = TeachingSchedule::where('teacher_id', $userId)->distinct('class_id')->pluck('class_id');
        $myClasses = ClassModel::whereIn('id', $scheduleClassIds)->withCount('students')->get();

        $classIds = $this->getAccessibleClassIds($userId);

        $totalSiswa = Student::whereIn('class_id', $classIds)->count();
        $totalKelasDiajar = TeachingSchedule::where('teacher_id', $userId)->distinct('class_id')->count('class_id');
        $totalMapelDiajar = TeachingSchedule::where('teacher_id', $userId)->distinct('subject_id')->count('subject_id');

        $presensiHariIni = Presence::whereIn('student_id', function ($q) use ($classIds) {
            $q->select('id')->from('students')->whereIn('class_id', $classIds);
        })->whereDate('date', today())->count();

        $totalHadir = Presence::whereIn('student_id', function ($q) use ($classIds) {
            $q->select('id')->from('students')->whereIn('class_id', $classIds);
        })->whereDate('date', today())->where('status', 'hadir')->count();

        $totalUjian = Exam::where('teacher_id', $userId)->count();
        $totalMateri = Lesson::where('teacher_id', $userId)->count();

        $chartPresensiHarian = Presence::select(
            DB::raw("DATE(date) as tgl"),
            DB::raw("COUNT(*) as total"),
            DB::raw("SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir")
        )
            ->whereIn('student_id', function ($q) use ($classIds) {
                $q->select('id')->from('students')->whereIn('class_id', $classIds);
            })
            ->whereBetween('date', [today()->subDays(13), today()])
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        $chartHariLabel = $chartPresensiHarian->pluck('tgl')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'));
        $chartHariTotal = $chartPresensiHarian->pluck('total');
        $chartHariHadir = $chartPresensiHarian->pluck('hadir');

        $chartPerKelas = [];
        foreach ($myClasses as $class) {
            $total = Student::where('class_id', $class->id)->count();
            $hadir = Presence::whereIn('student_id', function ($q) use ($class) {
                $q->select('id')->from('students')->where('class_id', $class->id);
            })->whereDate('date', today())->where('status', 'hadir')->count();
            $chartPerKelas[] = ['name' => $class->name, 'total' => $total, 'hadir' => $hadir];
        }

        $jadwalUjian = Exam::with('class')
            ->where('teacher_id', $userId)
            ->where('date', '>=', today())
            ->orderBy('date')
            ->get();

        $today = today();
        $month = request('month', $today->month);
        $year = request('year', $today->year);
        $firstDay = \Carbon\Carbon::create($year, $month, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDayOfWeek = $firstDay->dayOfWeek;

        $examDates = Exam::where('teacher_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get()
            ->groupBy(fn($e) => $e->date->day);

        $hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $hariSekarang = $hariIndo[now()->format('l')];

        $jadwalHariIni = TeachingSchedule::with('subject', 'class')
            ->where('teacher_id', $userId)
            ->where('day_of_week', $hariSekarang)
            ->orderBy('start_time')
            ->get();

        return view('teacher.dashboard', compact(
            'myClasses', 'totalSiswa', 'presensiHariIni',
            'totalHadir', 'totalUjian', 'totalMateri',
            'chartHariLabel', 'chartHariTotal', 'chartHariHadir',
            'chartPerKelas',
            'jadwalUjian',
            'month', 'year', 'daysInMonth', 'startDayOfWeek', 'examDates',
            'totalKelasDiajar', 'totalMapelDiajar', 'jadwalHariIni', 'hariSekarang'
        ));
    }
}
