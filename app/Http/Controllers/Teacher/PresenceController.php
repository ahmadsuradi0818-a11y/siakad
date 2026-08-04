<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Presence;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $scheduleClassIds = TeachingSchedule::where('teacher_id', $userId)
            ->distinct('class_id')
            ->pluck('class_id');
        $myClasses = ClassModel::whereIn('id', $scheduleClassIds)->get();

        $subjectIds = TeachingSchedule::where('teacher_id', $userId)
            ->distinct('subject_id')
            ->pluck('subject_id');
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        $query = Presence::with('student.user', 'student.class', 'subject')
            ->whereIn('student_id', function ($q) use ($scheduleClassIds) {
                $q->select('id')->from('students')->whereIn('class_id', $scheduleClassIds);
            });

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $presences = $query->paginate(10);

        return view('teacher.presences.index', compact('presences', 'myClasses', 'subjects'));
    }

    public function create(Request $request)
    {
        $userId = auth()->id();
        $scheduleClassIds = TeachingSchedule::where('teacher_id', $userId)
            ->distinct('class_id')
            ->pluck('class_id');
        $myClasses = ClassModel::whereIn('id', $scheduleClassIds)->get();

        $classId = $request->get('class_id', $myClasses->first()?->id);
        $subjectId = $request->get('subject_id');
        $date = $request->get('date', today()->toDateString());

        $subjects = collect();
        if ($classId && in_array($classId, $scheduleClassIds->toArray())) {
            $subjectIdsForClass = TeachingSchedule::where('teacher_id', $userId)
                ->where('class_id', $classId)
                ->distinct('subject_id')
                ->pluck('subject_id');
            $subjects = Subject::whereIn('id', $subjectIdsForClass)->get();
            $subjectId = $subjectId ?: $subjects->first()?->id;
        }

        $students = collect();
        if ($classId && in_array($classId, $scheduleClassIds->toArray())) {
            $students = Student::with('user')->where('class_id', $classId)->get();
        }

        return view('teacher.presences.create', compact('myClasses', 'students', 'classId', 'subjectId', 'date', 'subjects'));
    }

    public function store(Request $request, WhatsAppService $whatsapp)
    {
        $userId = auth()->id();
        $scheduleClassIds = TeachingSchedule::where('teacher_id', $userId)
            ->distinct('class_id')
            ->pluck('class_id');

        $validated = $request->validate([
            'presences' => 'required|array',
            'presences.*.student_id' => 'required|exists:students,id',
            'presences.*.status' => 'required|in:hadir,izin,sakit,alpha',
            'subject_id' => 'nullable|exists:subjects,id',
            'date' => 'required|date',
        ]);

        $allowedStudentIds = Student::whereIn('class_id', $scheduleClassIds)
            ->pluck('id')
            ->toArray();

        $statusLabels = [
            'hadir' => 'Hadir',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha (Tanpa Keterangan)',
        ];

        foreach ($validated['presences'] as $presence) {
            if (!in_array($presence['student_id'], $allowedStudentIds)) {
                continue;
            }

            Presence::updateOrCreate(
                [
                    'student_id' => $presence['student_id'],
                    'date' => $validated['date'],
                ],
                [
                    'status' => $presence['status'],
                    'subject_id' => $validated['subject_id'] ?? null,
                ]
            );

            $student = Student::with('user', 'class')->find($presence['student_id']);

            if ($student && $student->parent_phone) {
                $tanggal = \Carbon\Carbon::parse($validated['date'])->isoFormat('dddd, D MMMM YYYY');
                $statusLabel = $statusLabels[$presence['status']];
                $message = "Assalamu'alaikum Wr. Wb.\n\n";
                $message .= "Dengan ini kami informasikan bahwa:\n";
                $message .= "Nama: {$student->user->name}\n";
                $message .= "Kelas: {$student->class->name}\n";
                $message .= "Tanggal: {$tanggal}\n";
                $message .= "Status Presensi: *{$statusLabel}*\n\n";
                $message .= "Terima kasih.\n\n";
                $message .= "- SMK Satya Bhakti -";

                $whatsapp->send($student->parent_phone, $message);
            }
        }

        return redirect()->route('teacher.presences.index')
            ->with('success', 'Presensi berhasil dicatat.');
    }

    public function report(Request $request)
    {
        $userId = auth()->id();
        $scheduleClassIds = TeachingSchedule::where('teacher_id', $userId)
            ->distinct('class_id')
            ->pluck('class_id');
        $myClasses = ClassModel::whereIn('id', $scheduleClassIds)->get();

        $subjectIds = TeachingSchedule::where('teacher_id', $userId)
            ->distinct('subject_id')
            ->pluck('subject_id');
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        $academicYears = AcademicYear::orderBy('year', 'desc')->orderBy('semester', 'desc')->get();

        $filterPeriod = $request->input('filter_period', 'all');
        $classId = $request->input('class_id');
        $subjectId = $request->input('subject_id');

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

        $students = Student::with('user', 'class')
            ->whereIn('class_id', $scheduleClassIds)
            ->when($classId, fn($q) => $q->where('class_id', $classId))
            ->get();

        $report = [];
        foreach ($students as $student) {
            $query = Presence::where('student_id', $student->id)
                ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId));

            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }

            $presences = $query->get();

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

        return view('teacher.presences.report', compact(
            'myClasses', 'subjects', 'academicYears', 'report',
            'filterPeriod', 'classId', 'subjectId',
            'startDate', 'endDate', 'selectedYear'
        ));
    }
}
