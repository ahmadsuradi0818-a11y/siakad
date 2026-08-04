<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        Student::create([
            'user_id' => 3,
            'class_id' => 1,
            'nis' => '12345',
        ]);

        Student::create([
            'user_id' => 4,
            'class_id' => 1,
            'nis' => '67890',
        ]);
    }
}
