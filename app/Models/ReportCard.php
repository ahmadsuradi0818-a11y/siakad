<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'class_id',
        'homeroom_teacher_id',
        'attitude_religious',
        'attitude_social',
        'homeroom_notes',
        'attendance_sick',
        'attendance_permit',
        'attendance_absent',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'attendance_sick' => 'integer',
            'attendance_permit' => 'integer',
            'attendance_absent' => 'integer',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function homeroomTeacher()
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    public function subjects()
    {
        return $this->hasMany(ReportCardSubject::class);
    }

    public function extracurriculars()
    {
        return $this->hasMany(ReportCardExtracurricular::class);
    }
}
