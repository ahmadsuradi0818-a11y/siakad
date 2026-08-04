<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'subject_name',
        'class_id',
        'academic_year_id',
        'teacher_id',
        'attendance_score',
        'assignment_score',
        'daily_test_avg',
        'midterm_score',
        'final_score',
        'final_grade',
        'teacher_notes',
    ];

    protected function casts(): array
    {
        return [
            'attendance_score' => 'decimal:2',
            'assignment_score' => 'decimal:2',
            'daily_test_avg' => 'decimal:2',
            'midterm_score' => 'decimal:2',
            'final_score' => 'decimal:2',
            'final_grade' => 'decimal:2',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
