<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamResult;
use App\Models\Lesson;
use App\Models\Presence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('profile.edit')->with('error', 'Data siswa tidak ditemukan. Hubungi admin.');
        }

        $totalPresensi = Presence::where('student_id', $student->id)->count();
        $totalHadir = Presence::where('student_id', $student->id)->where('status', 'hadir')->count();
        $totalUjian = Exam::whereHas('classes', fn($q) => $q->where('class_id', $student->class_id))->count();

        $chartPresensi = [
            'hadir' => Presence::where('student_id', $student->id)->where('status', 'hadir')->count(),
            'sakit' => Presence::where('student_id', $student->id)->where('status', 'sakit')->count(),
            'izin' => Presence::where('student_id', $student->id)->where('status', 'izin')->count(),
            'alpha' => Presence::where('student_id', $student->id)->where('status', 'alpha')->count(),
        ];

        $hariBulanIni = range(1, now()->daysInMonth);
        $chartPresensiBulan = [];
        foreach ($hariBulanIni as $h) {
            $tgl = now()->startOfMonth()->addDays($h - 1)->toDateString();
            $status = Presence::where('student_id', $student->id)
                ->whereDate('date', $tgl)
                ->value('status');
            $chartPresensiBulan[] = $status ?? '-';
        }

        $presensiBulanIni = Presence::where('student_id', $student->id)
            ->whereMonth('date', today()->month)
            ->whereYear('date', today()->year)
            ->get();

        $jadwalUjian = Exam::with('teacher')
            ->whereHas('classes', fn($q) => $q->where('class_id', $student->class_id))
            ->where('date', '>=', today())
            ->orderBy('date')
            ->get();

        $nilaiUjian = ExamResult::with('exam')
            ->where('student_id', $student->id)
            ->get();

        $chartNilaiLabel = $nilaiUjian->pluck('exam.name')->map(fn($n) => \Illuminate\Support\Str::limit($n, 15));
        $chartNilaiData = $nilaiUjian->pluck('score');

        $materiTerbaru = Lesson::with('teacher')
            ->where('class_id', $student->class_id)
            ->latest()
            ->take(5)
            ->get();

        $today = today();
        $month = request('month', $today->month);
        $year = request('year', $today->year);
        $firstDay = \Carbon\Carbon::create($year, $month, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDayOfWeek = $firstDay->dayOfWeek;

        $examDates = Exam::whereHas('classes', fn($q) => $q->where('class_id', $student->class_id))
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get()
            ->groupBy(fn($e) => $e->date->day);

        return view('student.dashboard', compact(
            'student',
            'totalPresensi', 'totalHadir', 'totalUjian',
            'chartPresensi', 'chartPresensiBulan', 'hariBulanIni',
            'presensiBulanIni', 'jadwalUjian', 'nilaiUjian',
            'chartNilaiLabel', 'chartNilaiData',
            'materiTerbaru',
            'month', 'year', 'daysInMonth', 'startDayOfWeek', 'examDates'
        ));
    }

    public function presensi(Request $request)
    {
        $student = auth()->user()->student;

        $query = Presence::where('student_id', $student->id);

        if ($request->filled('bulan')) {
            $query->whereMonth('date', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('date', $request->tahun);
        }

        $presences = $query->orderBy('date', 'desc')->paginate(10);

        return view('student.presences', compact('presences'));
    }

    public function exams()
    {
        $student = auth()->user()->student;

        $exams = Exam::with(['teacher', 'results', 'subject', 'questions'])
            ->whereHas('classes', fn($q) => $q->where('class_id', $student->class_id))
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('student.exams', compact('exams', 'student'));
    }

    public function examDetail(Exam $exam)
    {
        $student = auth()->user()->student;

        if (!$exam->classes->contains('id', $student->class_id)) {
            abort(403);
        }

        if ($exam->date->isFuture()) {
            return redirect()->route('student.exams')
                ->with('error', 'Ujian "' . $exam->name . '" belum dapat diakses. Tanggal: ' . $exam->date->format('d/m/Y') . '.');
        }

        if ($exam->start_time && $exam->date->isToday() && now()->format('H:i') < $exam->start_time) {
            return redirect()->route('student.exams')
                ->with('error', 'Ujian "' . $exam->name . '" belum dimulai. Jadwal: ' . substr($exam->start_time, 0, 5) . ' WIB.');
        }

        $exam->load('classes', 'subject', 'teacher');
        $questions = $exam->questions()->orderBy('id')->get();

        $existingResult = ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        $isExpired = $exam->date->isPast()
            || ($exam->end_time && $exam->date->isToday() && now()->format('H:i') > $exam->end_time);

        if ($isExpired && (!$existingResult || $existingResult->score === null)) {
            return redirect()->route('student.exams')
                ->with('error', 'Waktu ujian "' . $exam->name . '" sudah berakhir.');
        }

        if (!$existingResult) {
            $existingResult = ExamResult::create([
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'score' => null,
                'started_at' => now(),
            ]);
        }

        if (!$existingResult->score && $existingResult->is_blocked) {
            return view('student.exam-detail', compact('exam', 'questions', 'existingResult'))
                ->with('blocked', true);
        }

        $answers = ExamAnswer::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('question_id');

        return view('student.exam-detail', compact('exam', 'questions', 'existingResult', 'answers'));
    }

    public function saveAnswer(Request $request, Exam $exam)
    {
        $student = auth()->user()->student;

        if (!$exam->classes->contains('id', $student->class_id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $isExpired = $exam->date->isPast()
            || ($exam->end_time && $exam->date->isToday() && now()->format('H:i') > $exam->end_time);

        if ($isExpired) {
            return response()->json(['error' => 'Waktu ujian sudah berakhir.'], 403);
        }

        $questions = $exam->questions;

        foreach ($questions as $question) {
            $raw = $request->input('question_' . $question->id);
            if ($raw === null || $raw === '') continue;

            $answer = is_array($raw) ? json_encode($raw) : $raw;

            ExamAnswer::updateOrCreate(
                [
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'question_id' => $question->id,
                ],
                ['answer' => $answer]
            );
        }

        return response()->json(['success' => true]);
    }

    public function submitExam(Request $request, Exam $exam)
    {
        $student = auth()->user()->student;

        if (!$exam->classes->contains('id', $student->class_id)) {
            abort(403);
        }

        if ($exam->date->isFuture()) {
            return redirect()->route('student.exams')
                ->with('error', 'Ujian "' . $exam->name . '" belum dapat diakses.');
        }

        if ($exam->start_time && $exam->date->isToday() && now()->format('H:i') < $exam->start_time) {
            return redirect()->route('student.exams')
                ->with('error', 'Ujian "' . $exam->name . '" belum dimulai.');
        }

        $result = ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        $isExpired = $exam->date->isPast()
            || ($exam->end_time && $exam->date->isToday() && now()->format('H:i') > $exam->end_time);

        if ($isExpired) {
            return redirect()->route('student.exams')
                ->with('error', 'Waktu ujian "' . $exam->name . '" sudah berakhir.');
        }

        if ($result && $result->score !== null) {
            return redirect()->route('student.exams.detail', $exam)
                ->with('error', 'Kamu sudah mengerjakan ujian ini.');
        }

        if ($result && $result->is_blocked) {
            return redirect()->route('student.exams.detail', $exam)
                ->with('error', 'Akses ujian diblokir oleh pengawas.');
        }

        $questions = $exam->questions;
        $totalPoints = 0;
        $earnedPoints = 0;

        $letters = range('A', 'Z');

        foreach ($questions as $question) {
            $raw = $request->input('question_' . $question->id);
            $answer = is_array($raw) ? json_encode($raw) : ($raw ?? '');
            $isCorrect = null;
            $pointsEarned = null;

            if ($question->type === 'pilihan_ganda') {
                $options = $question->options ?? [];
                $letterIdx = array_search($question->correct_answer, $letters);
                $correctText = $options[$letterIdx] ?? null;
                $isCorrect = $correctText !== null && $answer === $correctText;
                $pointsEarned = $isCorrect ? $question->points : 0;
            } elseif ($question->type === 'benar_salah') {
                $isCorrect = strtolower($answer) === strtolower($question->correct_answer);
                $pointsEarned = $isCorrect ? $question->points : 0;
            } elseif ($question->type === 'pilihan_ganda_kompleks') {
                $options = $question->options ?? [];
                $selected = is_array($raw) ? $raw : [];
                $correctLetters = explode(',', $question->correct_answer);
                $correctTexts = [];
                foreach ($correctLetters as $letter) {
                    $letter = trim($letter);
                    $idx = array_search($letter, $letters);
                    if ($idx !== false && isset($options[$idx])) {
                        $correctTexts[] = $options[$idx];
                    }
                }
                $isCorrect = empty(array_diff($selected, $correctTexts)) && empty(array_diff($correctTexts, $selected));
                $pointsEarned = $isCorrect ? $question->points : 0;
            }

            ExamAnswer::updateOrCreate(
                [
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'question_id' => $question->id,
                ],
                [
                    'answer' => $answer,
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                ]
            );

            if ($pointsEarned !== null) {
                $earnedPoints += $pointsEarned;
            }
            $totalPoints += $question->points;
        }

        $score = $totalPoints > 0 ? $earnedPoints : 0;

        if ($result) {
            $result->update(['score' => $score, 'started_at' => $result->started_at]);
        } else {
            ExamResult::create([
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'score' => $score,
            ]);
        }

        return redirect()->route('student.exams.detail', $exam)
            ->with('success', 'Ujian berhasil dikumpulkan. Nilai: ' . $score . ' / ' . $totalPoints . ' poin');
    }

    public function lessons()
    {
        $student = auth()->user()->student;

        $lessons = Lesson::with('teacher')
            ->where('class_id', $student->class_id)
            ->latest()
            ->paginate(10);

        return view('student.lessons', compact('lessons'));
    }
}
