<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Presence;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherPresence;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function presensi(Request $request)
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->orderBy('semester', 'desc')->get();
        $classes = ClassModel::all();

        $classId = $request->input('class_id');
        $filterPeriod = $request->input('filter_period', 'all');

        $startDate = null;
        $endDate = null;

        if ($filterPeriod === 'minggu') {
            $weekStart = $request->input('week_start');
            $startDate = $weekStart ? \Carbon\Carbon::parse($weekStart)->startOfWeek()->toDateString() : now()->startOfWeek()->toDateString();
            $endDate = \Carbon\Carbon::parse($startDate)->endOfWeek()->toDateString();
        } elseif ($filterPeriod === 'bulan') {
            $month = $request->input('month', now()->month);
            $year = $request->input('year', now()->year);
            $startDate = \Carbon\Carbon::create($year, $month, 1)->toDateString();
            $endDate = \Carbon\Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        } else {
            $academicYearId = $request->input('academic_year_id', AcademicYear::where('is_active', true)->first()?->id);
            $selectedYear = $academicYearId ? AcademicYear::find($academicYearId) : null;
            if ($selectedYear) {
                $startDate = $selectedYear->start_date;
                $endDate = $selectedYear->end_date;
            }
        }

        $query = Student::with('user', 'class');

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $students = $query->get();

        $report = [];
        foreach ($students as $student) {
            $presencesQuery = Presence::where('student_id', $student->id);

            if ($startDate && $endDate) {
                $presencesQuery->whereBetween('date', [$startDate, $endDate]);
            }

            $presences = $presencesQuery->get();

            $report[] = [
                'student' => $student,
                'hadir' => $presences->where('status', 'hadir')->count(),
                'izin' => $presences->where('status', 'izin')->count(),
                'sakit' => $presences->where('status', 'sakit')->count(),
                'alpha' => $presences->where('status', 'alpha')->count(),
                'total' => $presences->count(),
            ];
        }

        $selectedYear = (isset($selectedYear) && $filterPeriod === 'all') ? $selectedYear : null;

        return view('admin.reports.presensi', compact(
            'academicYears', 'classes', 'report', 'classId', 'filterPeriod',
            'startDate', 'endDate', 'selectedYear'
        ));
    }

    public function siswa(Request $request)
    {
        $classes = ClassModel::all();
        $classId = $request->input('class_id');

        $query = Student::with('user', 'class');

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $students = $query->orderBy('nis')->get();

        return view('admin.reports.siswa', compact('classes', 'students', 'classId'));
    }

    public function guru(Request $request)
    {
        $role = $request->input('role');

        $query = Teacher::with('user');

        if ($role) {
            $query->whereHas('user', fn($q) => $q->where('role', $role));
        }

        $teachers = $query->orderBy('nip')->get();

        return view('admin.reports.guru', compact('teachers', 'role'));
    }

    public function presensiGuru(Request $request)
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->orderBy('semester', 'desc')->get();

        $filterPeriod = $request->input('filter_period', 'all');

        $startDate = null;
        $endDate = null;

        if ($filterPeriod === 'minggu') {
            $weekStart = $request->input('week_start');
            $startDate = $weekStart ? \Carbon\Carbon::parse($weekStart)->startOfWeek()->toDateString() : now()->startOfWeek()->toDateString();
            $endDate = \Carbon\Carbon::parse($startDate)->endOfWeek()->toDateString();
        } elseif ($filterPeriod === 'bulan') {
            $month = $request->input('month', now()->month);
            $year = $request->input('year', now()->year);
            $startDate = \Carbon\Carbon::create($year, $month, 1)->toDateString();
            $endDate = \Carbon\Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        } else {
            $academicYearId = $request->input('academic_year_id');
            $selectedYear = $academicYearId ? AcademicYear::find($academicYearId) : null;
            if ($selectedYear) {
                $startDate = $selectedYear->start_date;
                $endDate = $selectedYear->end_date;
            }
        }

        $teachers = Teacher::with('user')->orderBy('nip')->get();

        $report = [];
        foreach ($teachers as $teacher) {
            $query = TeacherPresence::where('teacher_id', $teacher->user_id);

            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }

            $presences = $query->get();

            $report[] = [
                'teacher' => $teacher,
                'hadir' => $presences->where('status', 'hadir')->count(),
                'izin' => $presences->where('status', 'izin')->count(),
                'sakit' => $presences->where('status', 'sakit')->count(),
                'alpha' => $presences->where('status', 'alpha')->count(),
                'total' => $presences->count(),
            ];
        }

        $selectedYear = (isset($selectedYear) && $filterPeriod === 'all') ? $selectedYear : null;

        return view('admin.reports.presensi-guru', compact(
            'academicYears', 'teachers', 'report', 'filterPeriod',
            'startDate', 'endDate', 'selectedYear'
        ));
    }
}
