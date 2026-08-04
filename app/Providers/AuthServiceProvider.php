<?php

namespace App\Providers;

use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\Lesson;
use App\Models\Presence;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        Gate::define('admin-only', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('guru-only', function (User $user) {
            return $user->role === 'guru';
        });

        Gate::define('siswa-only', function (User $user) {
            return $user->role === 'siswa';
        });

        Gate::define('manage-class', function (User $user, ClassModel $class) {
            return $user->role === 'admin' || $user->id === $class->homeroom_teacher_id;
        });

        Gate::define('manage-exam', function (User $user, Exam $exam) {
            return $user->role === 'admin' || $user->id === $exam->teacher_id;
        });

        Gate::define('manage-lesson', function (User $user, Lesson $lesson) {
            return $user->role === 'admin' || $user->id === $lesson->teacher_id;
        });

        Gate::define('view-presence', function (User $user, Presence $presence) {
            if ($user->role === 'admin') return true;
            if ($user->role === 'guru') {
                return $presence->student->class->homeroom_teacher_id === $user->id;
            }
            return $presence->student->user_id === $user->id;
        });

        Gate::define('view-student', function (User $user, Student $student) {
            if ($user->role === 'admin') return true;
            if ($user->role === 'guru') {
                return $student->class->homeroom_teacher_id === $user->id;
            }
            return $student->user_id === $user->id;
        });
    }
}
