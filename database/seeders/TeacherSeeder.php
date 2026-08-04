<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        Teacher::create([
            'user_id' => 2,
            'nip' => '12345678',
        ]);
    }
}
