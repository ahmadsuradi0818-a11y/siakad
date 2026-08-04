<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\AssignmentController as AdminAssignmentController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\PresenceController as AdminPresenceController;
use App\Http\Controllers\Admin\ReportCardController as AdminReportCardController;
use App\Http\Controllers\Admin\GradeController as AdminGradeController;
use App\Http\Controllers\Admin\IdCardSettingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SchoolProfileController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TeacherSubjectController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\BankSoalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\AnnouncementController as StudentAnnouncementController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\GradeController as StudentGradeController;
use App\Http\Controllers\Student\PresenceController as StudentPresenceController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\ReportCardController as StudentReportCardController;
use App\Http\Controllers\Student\StudentController as SiswaStudentController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\ScheduleController as TeacherScheduleController;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController;
use App\Http\Controllers\Teacher\AnnouncementController as TeacherAnnouncementController;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignmentController;
use App\Http\Controllers\Teacher\LessonController as TeacherLessonController;
use App\Http\Controllers\Teacher\PresenceController as TeacherPresenceController;
use App\Http\Controllers\Teacher\ReportCardController as TeacherReportCardController;
use App\Http\Controllers\Teacher\GradeController as TeacherGradeController;
use App\Http\Controllers\Teacher\StudentController as TeacherStudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    $demoAdmin = \App\Models\User::where('role', 'admin')->first();
    $demoGuru  = \App\Models\User::where('role', 'guru')->first();
    $demoSiswa = \App\Models\User::where('role', 'siswa')->first();
    return view('landing', compact('demoAdmin', 'demoGuru', 'demoSiswa'));
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        return match ($role) {
            'admin' => app(\App\Http\Controllers\Admin\DashboardController::class)->index(),
            'guru' => app(\App\Http\Controllers\Teacher\DashboardController::class)->index(),
            'siswa' => app(\App\Http\Controllers\Student\DashboardController::class)->index(),
            default => redirect()->route('login'),
        };
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('classes', ClassController::class);
    Route::post('teachers/import', [TeacherController::class, 'import'])->name('teachers.import');
    Route::get('teachers/download-template', [TeacherController::class, 'downloadTemplate'])->name('teachers.download-template');
    Route::resource('teachers', TeacherController::class);
    Route::post('students/import', [AdminStudentController::class, 'import'])->name('students.import');
    Route::get('students/download-template', [AdminStudentController::class, 'downloadTemplate'])->name('students.download-template');
    Route::get('students/id-card', [AdminStudentController::class, 'idCardIndex'])->name('students.id-card');
    Route::get('students/id-card/print', [AdminStudentController::class, 'idCardPrint'])->name('students.id-card-print');
    Route::get('id-card-settings', [IdCardSettingController::class, 'edit'])->name('id-card-settings.edit');
    Route::post('id-card-settings', [IdCardSettingController::class, 'update'])->name('id-card-settings.update');
    Route::post('id-card-settings/delete-template', [IdCardSettingController::class, 'destroyTemplate'])->name('id-card-settings.delete-template');
    Route::resource('students', AdminStudentController::class)->except(['show']);
    Route::get('exams/soal', [AdminExamController::class, 'soal'])->name('exams.soal');
    Route::post('exams/{exam}/duplicate', [AdminExamController::class, 'duplicate'])->name('exams.duplicate');
    Route::get('exams/monitoring', [AdminExamController::class, 'monitoring'])->name('exams.monitoring');
    Route::get('exams/{exam}/monitoring', [AdminExamController::class, 'monitoringDetail'])->name('exams.monitoring-detail');
    Route::post('exams/{exam}/block/{student}', [AdminExamController::class, 'toggleBlock'])->name('exams.toggle-block');
    Route::get('exams/nilai', [AdminExamController::class, 'nilai'])->name('exams.nilai');
    Route::get('exams/nilai/download', [AdminExamController::class, 'downloadNilai'])->name('exams.nilai-download');
    Route::resource('exams', AdminExamController::class);
    Route::get('exams/id-card/select', [AdminExamController::class, 'idCardSelectExam'])->name('exams.id-card-select');
    Route::get('exams/{exam}/id-card', [AdminExamController::class, 'idCardIndex'])->name('exams.id-card');
    Route::get('exams/{exam}/id-card/print', [AdminExamController::class, 'idCardPrint'])->name('exams.id-card-print');
    Route::get('exams/{exam}/nilai', [AdminExamController::class, 'nilaiDetail'])->name('exams.nilai-detail');
    Route::get('exams/{exam}/nilai/{student}', [AdminExamController::class, 'koreksi'])->name('exams.koreksi');
    Route::post('exams/{exam}/nilai/{student}', [AdminExamController::class, 'storeKoreksi'])->name('exams.store-koreksi');
    Route::prefix('exams/{exam}/questions')->name('exams.questions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\QuestionController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\QuestionController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\QuestionController::class, 'store'])->name('store');
        Route::post('/import', [\App\Http\Controllers\Admin\QuestionController::class, 'import'])->name('import');
        Route::get('/download-template', [\App\Http\Controllers\Admin\QuestionController::class, 'downloadTemplate'])->name('download-template');
        Route::get('/{question}/edit', [\App\Http\Controllers\Admin\QuestionController::class, 'edit'])->name('edit');
        Route::put('/{question}', [\App\Http\Controllers\Admin\QuestionController::class, 'update'])->name('update');
        Route::delete('/{question}', [\App\Http\Controllers\Admin\QuestionController::class, 'destroy'])->name('destroy');
        Route::post('/upload-image', [\App\Http\Controllers\Admin\QuestionController::class, 'uploadImage'])->name('upload-image');
    });
    Route::resource('subjects', SubjectController::class);
    Route::get('teacher-subjects', [TeacherSubjectController::class, 'index'])->name('teacher-subjects.index');
    Route::get('teacher-subjects/{teacher}/edit', [TeacherSubjectController::class, 'edit'])->name('teacher-subjects.edit');
    Route::put('teacher-subjects/{teacher}', [TeacherSubjectController::class, 'update'])->name('teacher-subjects.update');
    Route::resource('schedules', ScheduleController::class);
    Route::resource('presences', AdminPresenceController::class)->except(['edit', 'update']);
    Route::resource('lessons', AdminLessonController::class);
    Route::get('assignments/nilai', [AdminAssignmentController::class, 'nilai'])->name('assignments.nilai');
    Route::get('assignments/{assignment}/nilai', [AdminAssignmentController::class, 'nilaiDetail'])->name('assignments.nilai-detail');
    Route::post('assignments/{assignment}/nilai', [AdminAssignmentController::class, 'storeNilai'])->name('assignments.store-nilai');
    Route::resource('assignments', AdminAssignmentController::class);

    Route::prefix('bank-soal')->name('bank-soal.')->group(function () {
        Route::get('/', [BankSoalController::class, 'index'])->name('index');
        Route::post('/', [BankSoalController::class, 'store'])->name('store');
        Route::post('/update/{questionBank}', [BankSoalController::class, 'update'])->name('update');
        Route::delete('/{questionBank}', [BankSoalController::class, 'destroy'])->name('destroy');
        Route::post('/import', [BankSoalController::class, 'import'])->name('import');
        Route::post('/ambil', [BankSoalController::class, 'ambil'])->name('ambil');
        Route::post('/upload-image', [BankSoalController::class, 'uploadImage'])->name('upload-image');
    });

    Route::get('school-profile', [SchoolProfileController::class, 'index'])->name('school-profile.index');
    Route::post('school-profile', [SchoolProfileController::class, 'update'])->name('school-profile.update');

    Route::get('academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::post('academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::get('academic-years/{academicYear}/edit', [AcademicYearController::class, 'edit'])->name('academic-years.edit');
    Route::put('academic-years/{academicYear}', [AcademicYearController::class, 'update'])->name('academic-years.update');
    Route::delete('academic-years/{academicYear}', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');
    Route::post('academic-years/{academicYear}/set-active', [AcademicYearController::class, 'setActive'])->name('academic-years.set-active');

    Route::get('report-cards', [AdminReportCardController::class, 'index'])->name('report-cards.index');
    Route::post('report-cards/generate', [AdminReportCardController::class, 'generate'])->name('report-cards.generate');
    Route::get('report-cards/get-students', [AdminReportCardController::class, 'getStudentsByClass'])->name('report-cards.get-students');
    Route::get('report-cards/leger', [AdminReportCardController::class, 'leger'])->name('report-cards.leger');
    Route::post('report-cards/leger', [AdminReportCardController::class, 'legerUpdate'])->name('report-cards.leger-update');
    Route::post('report-cards/sync-grades', [AdminReportCardController::class, 'syncFromGrades'])->name('report-cards.sync-grades');
    Route::get('report-cards/{reportCard}', [AdminReportCardController::class, 'show'])->name('report-cards.show');
    Route::delete('report-cards/{reportCard}', [AdminReportCardController::class, 'destroy'])->name('report-cards.destroy');

    Route::get('grades', [AdminGradeController::class, 'index'])->name('grades.index');
    Route::get('grades/{class}/{subject}', [AdminGradeController::class, 'show'])->name('grades.show');
    Route::delete('grades/{grade}', [AdminGradeController::class, 'destroy'])->name('grades.destroy');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/presensi', [ReportController::class, 'presensi'])->name('presensi');
        Route::get('/presensi-guru', [ReportController::class, 'presensiGuru'])->name('presensi-guru');
        Route::get('/siswa', [ReportController::class, 'siswa'])->name('siswa');
        Route::get('/guru', [ReportController::class, 'guru'])->name('guru');
    });

    Route::resource('announcements', AdminAnnouncementController::class);

    Route::prefix('verification')->name('verification.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\VerificationController::class, 'index'])->name('index');
        Route::get('{student}', [\App\Http\Controllers\Admin\VerificationController::class, 'show'])->name('show');
        Route::post('{student}/verify', [\App\Http\Controllers\Admin\VerificationController::class, 'verify'])->name('verify');
        Route::post('{student}/accept', [\App\Http\Controllers\Admin\VerificationController::class, 'accept'])->name('accept');
        Route::post('{student}/reject', [\App\Http\Controllers\Admin\VerificationController::class, 'reject'])->name('reject');
    });
});

Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');

    Route::get('/students', [TeacherStudentController::class, 'index'])->name('students.index');

    Route::get('/presences/create', [TeacherPresenceController::class, 'create'])->name('presences.create');
    Route::post('/presences', [TeacherPresenceController::class, 'store'])->name('presences.store');
    Route::get('/presences', [TeacherPresenceController::class, 'index'])->name('presences.index');
    Route::get('/presences/report', [TeacherPresenceController::class, 'report'])->name('presences.report');

    Route::get('/exams/soal', [TeacherExamController::class, 'soal'])->name('exams.soal');
    Route::post('/exams/{exam}/duplicate', [TeacherExamController::class, 'duplicate'])->name('exams.duplicate');
    Route::get('/exams/monitoring', [TeacherExamController::class, 'monitoring'])->name('exams.monitoring');
    Route::get('/exams/{exam}/monitoring', [TeacherExamController::class, 'monitoringDetail'])->name('exams.monitoring-detail');
    Route::post('/exams/{exam}/block/{student}', [TeacherExamController::class, 'toggleBlock'])->name('exams.toggle-block');
    Route::get('/exams/nilai', [TeacherExamController::class, 'nilai'])->name('exams.nilai');
    Route::get('/exams/nilai/download', [TeacherExamController::class, 'downloadNilai'])->name('exams.nilai-download');
    Route::get('/exams/{exam}/nilai', [TeacherExamController::class, 'nilaiDetail'])->name('exams.nilai-detail');
    Route::get('/exams/{exam}/nilai/{student}', [TeacherExamController::class, 'koreksi'])->name('exams.koreksi');
    Route::post('/exams/{exam}/nilai/{student}', [TeacherExamController::class, 'storeKoreksi'])->name('exams.store-koreksi');
    Route::get('/exams', [TeacherExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/create', [TeacherExamController::class, 'create'])->name('exams.create');
    Route::post('/exams', [TeacherExamController::class, 'store'])->name('exams.store');
    Route::get('/exams/{exam}/input-nilai', [TeacherExamController::class, 'inputNilai'])->name('exams.input-nilai');
    Route::post('/exams/{exam}/nilai', [TeacherExamController::class, 'storeNilai'])->name('exams.store-nilai');
    Route::prefix('/exams/{exam}/questions')->name('exams.questions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\QuestionController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Teacher\QuestionController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Teacher\QuestionController::class, 'store'])->name('store');
        Route::post('/import', [\App\Http\Controllers\Teacher\QuestionController::class, 'import'])->name('import');
        Route::get('/download-template', [\App\Http\Controllers\Teacher\QuestionController::class, 'downloadTemplate'])->name('download-template');
        Route::get('/{question}/edit', [\App\Http\Controllers\Teacher\QuestionController::class, 'edit'])->name('edit');
        Route::put('/{question}', [\App\Http\Controllers\Teacher\QuestionController::class, 'update'])->name('update');
        Route::delete('/{question}', [\App\Http\Controllers\Teacher\QuestionController::class, 'destroy'])->name('destroy');
        Route::post('/upload-image', [\App\Http\Controllers\Teacher\QuestionController::class, 'uploadImage'])->name('upload-image');
    });

    Route::get('/lessons', [TeacherLessonController::class, 'index'])->name('lessons.index');
    Route::get('/lessons/create', [TeacherLessonController::class, 'create'])->name('lessons.create');
    Route::post('/lessons', [TeacherLessonController::class, 'store'])->name('lessons.store');
    Route::delete('/lessons/{lesson}', [TeacherLessonController::class, 'destroy'])->name('lessons.destroy');
    Route::get('/assignments/nilai', [TeacherAssignmentController::class, 'nilai'])->name('assignments.nilai');
    Route::get('/assignments/{assignment}/nilai', [TeacherAssignmentController::class, 'nilaiDetail'])->name('assignments.nilai-detail');
    Route::post('/assignments/{assignment}/nilai', [TeacherAssignmentController::class, 'storeNilai'])->name('assignments.store-nilai');
    Route::get('/assignments', [TeacherAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/create', [TeacherAssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/assignments', [TeacherAssignmentController::class, 'store'])->name('assignments.store');
    Route::get('/assignments/{assignment}/edit', [TeacherAssignmentController::class, 'edit'])->name('assignments.edit');
    Route::put('/assignments/{assignment}', [TeacherAssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('/assignments/{assignment}', [TeacherAssignmentController::class, 'destroy'])->name('assignments.destroy');

    Route::prefix('bank-soal')->name('bank-soal.')->group(function () {
        Route::get('/', [BankSoalController::class, 'index'])->name('index');
        Route::post('/', [BankSoalController::class, 'store'])->name('store');
        Route::post('/update/{questionBank}', [BankSoalController::class, 'update'])->name('update');
        Route::delete('/{questionBank}', [BankSoalController::class, 'destroy'])->name('destroy');
        Route::post('/import', [BankSoalController::class, 'import'])->name('import');
        Route::post('/ambil', [BankSoalController::class, 'ambil'])->name('ambil');
        Route::post('/upload-image', [BankSoalController::class, 'uploadImage'])->name('upload-image');
    });

    Route::get('grades', [TeacherGradeController::class, 'index'])->name('grades.index');
    Route::get('grades/{class}/{subject}', [TeacherGradeController::class, 'show'])->name('grades.show');
    Route::post('grades/{class}/{subject}', [TeacherGradeController::class, 'store'])->name('grades.store');

    Route::get('report-cards', [TeacherReportCardController::class, 'index'])->name('report-cards.index');
    Route::get('report-cards/leger', [TeacherReportCardController::class, 'leger'])->name('report-cards.leger');
    Route::post('report-cards/leger', [TeacherReportCardController::class, 'legerUpdate'])->name('report-cards.leger-update');
    Route::post('report-cards/sync-grades', [TeacherReportCardController::class, 'syncFromGrades'])->name('report-cards.sync-grades');
    Route::get('report-cards/{reportCard}/edit', [TeacherReportCardController::class, 'edit'])->name('report-cards.edit');
    Route::put('report-cards/{reportCard}', [TeacherReportCardController::class, 'update'])->name('report-cards.update');
    Route::get('report-cards/{reportCard}', [TeacherReportCardController::class, 'show'])->name('report-cards.show');

    Route::resource('announcements', TeacherAnnouncementController::class);

    Route::get('/schedule', [TeacherScheduleController::class, 'index'])->name('schedule');

    Route::get('/teacher-presences', [\App\Http\Controllers\Teacher\TeacherPresenceController::class, 'index'])->name('teacher-presences.index');
    Route::post('/teacher-presences', [\App\Http\Controllers\Teacher\TeacherPresenceController::class, 'store'])->name('teacher-presences.store');
});

Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/presensi', [StudentPresenceController::class, 'index'])->name('presensi');
    Route::post('/presensi', [StudentPresenceController::class, 'store'])->name('presensi.store');
    Route::get('/exams', [StudentDashboardController::class, 'exams'])->name('exams');
    Route::get('/exams/{exam}', [StudentDashboardController::class, 'examDetail'])->name('exams.detail');
    Route::post('/exams/{exam}/submit', [StudentDashboardController::class, 'submitExam'])->name('exams.submit');
    Route::post('/exams/{exam}/save-answer', [StudentDashboardController::class, 'saveAnswer'])->name('exams.save-answer');
    Route::get('/lessons', [StudentDashboardController::class, 'lessons'])->name('lessons');
    Route::get('/nilai', [StudentGradeController::class, 'index'])->name('grades');
    Route::get('/students', [SiswaStudentController::class, 'index'])->name('students');

    Route::get('/assignments', [StudentAssignmentController::class, 'index'])->name('assignments');
    Route::get('/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('assignments.show');
    Route::post('/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('assignments.submit');
    Route::get('/announcements', [StudentAnnouncementController::class, 'index'])->name('announcements');
    Route::get('/announcements/{announcement}', [StudentAnnouncementController::class, 'show'])->name('announcements.show');
    Route::get('/profile', [StudentProfileController::class, 'index'])->name('profile');
    Route::post('/profile/photo', [StudentProfileController::class, 'updatePhoto'])->name('profile.photo');
});

require __DIR__.'/auth.php';
