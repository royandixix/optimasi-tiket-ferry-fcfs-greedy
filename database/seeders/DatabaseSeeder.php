<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@gmail.com',
            ],
            [
                'name' => 'Admin',
                'password' => Hash::make('password1234'),
                'role' => 'admin',
                'status' => 'aktif',
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'petugas@gmail.com',
            ],
            [
                'name' => 'Petugas Validasi',
                'password' => Hash::make('password1234'),
                'role' => 'petugas',
                'status' => 'aktif',
            ]
        );
    }
}