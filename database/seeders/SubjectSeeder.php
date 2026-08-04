<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            'Matematika',
            'Bahasa Indonesia',
            'Bahasa Inggris',
            'IPA',
            'IPS',
            'Pendidikan Agama',
            'Pendidikan Kewarganegaraan',
            'Seni Budaya',
            'Penjaskes',
            'Prakarya',
        ];

        foreach ($subjects as $name) {
            Subject::create(compact('name'));
        }
    }
}
