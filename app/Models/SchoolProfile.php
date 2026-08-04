<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'npsn',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'headmaster',
        'nip_headmaster',
        'vision',
        'mission',
        'presence_latitude',
        'presence_longitude',
        'presence_radius',
    ];

    protected function casts(): array
    {
        return [
            'presence_latitude' => 'decimal:7',
            'presence_longitude' => 'decimal:7',
            'presence_radius' => 'integer',
        ];
    }
}
