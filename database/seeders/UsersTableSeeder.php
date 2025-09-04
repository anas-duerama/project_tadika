<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::truncate();

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@example.test',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.test',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
}
