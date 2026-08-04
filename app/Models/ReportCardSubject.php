<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCardSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_card_id',
        'subject_id',
        'subject_name',
        'daily_test_avg',
        'midterm_score',
        'final_score',
        'final_grade',
        'teacher_notes',
    ];

    protected function casts(): array
    {
        return [
            'daily_test_avg' => 'decimal:2',
            'midterm_score' => 'decimal:2',
            'final_score' => 'decimal:2',
            'final_grade' => 'decimal:2',
        ];
    }

    public function reportCard()
    {
        return $this->belongsTo(ReportCard::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
