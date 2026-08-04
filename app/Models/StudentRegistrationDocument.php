<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentRegistrationDocument extends Model
{
    protected $fillable = [
        'student_id',
        'document_type',
        'file_path',
        'original_name',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
