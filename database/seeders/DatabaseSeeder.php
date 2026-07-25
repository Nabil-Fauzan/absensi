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
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('123456789'),
                'role' => 'admin',
            ]
        );

        // Regular Employee Account
        User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Regular Employee',
                'password' => Hash::make('123456789'),
                'role' => 'employee',
            ]
        );

        // Default Office settings
        \App\Models\Setting::set('office_latitude', '-6.873218738309585');
        \App\Models\Setting::set('office_longitude', '107.5609385222725');
        \App\Models\Setting::set('office_radius_meters', '100');
        \App\Models\Setting::set('office_check_in_time', '08:00:00');
    }
}
