<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\Presence;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalGuru = User::where('role', 'guru')->count();
        $totalKelas = ClassModel::count();

        $presensiHariIni = Presence::whereDate('date', today())->count();
        $totalHadir = Presence::whereDate('date', today())->where('status', 'hadir')->count();

        $chartPresensiHariIni = [
            'hadir' => Presence::whereDate('date', today())->where('status', 'hadir')->count(),
            'sakit' => Presence::whereDate('date', today())->where('status', 'sakit')->count(),
            'izin' => Presence::whereDate('date', today())->where('status', 'izin')->count(),
            'alpha' => Presence::whereDate('date', today())->where('status', 'alpha')->count(),
        ];

        $presensiPerBulan = Presence::select(
            DB::raw("COUNT(*) as total"),
            DB::raw("SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir"),
            DB::raw("SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit"),
            DB::raw("SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin"),
            DB::raw("SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as alpha"),
            DB::raw("MONTH(date) as bulan"),
            DB::raw("YEAR(date) as tahun")
        )
            ->whereYear('date', today()->year)
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        $jadwalUjian = Exam::with('class', 'teacher')
            ->where('date', '>=', today())
            ->orderBy('date')
            ->take(10)
            ->get();

        $today = today();
        $month = request('month', $today->month);
        $year = request('year', $today->year);
        $firstDay = \Carbon\Carbon::create($year, $month, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDayOfWeek = $firstDay->dayOfWeek;

        $examDates = Exam::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get()
            ->groupBy(fn($e) => $e->date->day);

        return view('admin.dashboard', compact(
            'totalSiswa', 'totalGuru', 'totalKelas',
            'presensiHariIni', 'totalHadir',
            'chartPresensiHariIni',
            'presensiPerBulan',
            'jadwalUjian',
            'month', 'year', 'daysInMonth', 'startDayOfWeek', 'examDates'
        ));
    }
}
