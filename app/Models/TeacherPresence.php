<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherPresence extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'date',
        'status',
        'presence_at',
        'photo',
        'latitude',
        'longitude',
    ];

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

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
