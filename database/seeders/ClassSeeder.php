<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        ClassModel::create([
            'name' => 'XII RPL',
            'homeroom_teacher_id' => 2,
        ]);
    }
}
