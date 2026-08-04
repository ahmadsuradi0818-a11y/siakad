<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => '',
            'email' => '',
            'password' => Hash::make(''),
            'role' => '',
        ]);

        User::create([
            'name' => 'Ahmad',
            'email' => 'ahmad@smksatya.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Budi',
            'email' => 'budi@smksatya.id',
            'password' => Hash::make('password'),
            'role' => 'siswa',
        ]);

        User::create([
            'name' => 'Andi',
            'email' => 'andi@smksatya.id',
            'password' => Hash::make('password'),
            'role' => 'siswa',
        ]);
    }
}
