<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'class_id',
        'nis',
        'place_of_birth',
        'date_of_birth',
        'address',
        'parent_name',
        'parent_phone',
        'nisn',
        'gender',
        'religion',
        'registration_status',
        'registration_note',
        'registered_online',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth'      => 'date',
            'registered_online'  => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function reportCards()
    {
        return $this->hasMany(ReportCard::class);
    }

    public function registrationDocuments()
    {
        return $this->hasMany(StudentRegistrationDocument::class);
    }
}
