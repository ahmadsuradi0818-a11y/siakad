<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'date',
        'status',
        'presence_at',
        'photo',
        'latitude',
        'longitude',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'presence_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
