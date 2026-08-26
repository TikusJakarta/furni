<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    // Akun Admin
    User::create([
        'name' => 'Administrator',
        'email' => 'admin@gmail.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
    ]);

    // Akun Customer Biasa
    User::create([
        'name' => 'Budi Customer',
        'email' => 'customer@gmail.com',
        'password' => Hash::make('password123'),
        'role' => 'customer',
    ]);
}
}