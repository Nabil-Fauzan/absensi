<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin Account
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'admin',
        ]);

        // Regular Employee Account
        User::factory()->create([
            'name' => 'Regular Employee',
            'email' => 'user@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'employee',
        ]);
    }
}
