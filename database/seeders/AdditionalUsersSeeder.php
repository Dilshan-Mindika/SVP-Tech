<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Technician;
use Illuminate\Support\Facades\Hash;

class AdditionalUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Second Admin User
        User::firstOrCreate(
            ['email' => 'admin2@svp.tech'],
            [
                'name' => 'Second Admin',
                'password' => Hash::make('Dila1011@'),
                'role' => 'admin',
            ]
        );

        // 2. Technician 2 (Michael)
        $tech2 = User::firstOrCreate(
            ['email' => 'tech2@svp.tech'],
            [
                'name' => 'Michael Tech',
                'password' => Hash::make('Dila1011@'),
                'role' => 'technician',
            ]
        );

        // Ensure Technician Profile for Michael
        if (!Technician::where('user_id', $tech2->id)->exists()) {
            Technician::create([
                'user_id' => $tech2->id,
                'specialty' => 'Laptop Repairs',
                'total_jobs' => 0,
                'performance_score' => 4.8,
            ]);
        }

        // 3. Technician 3 (Sarah)
        $tech3 = User::firstOrCreate(
            ['email' => 'tech3@svp.tech'],
            [
                'name' => 'Sarah Tech',
                'password' => Hash::make('Dila1011@'),
                'role' => 'technician',
            ]
        );

        // Ensure Technician Profile for Sarah
        if (!Technician::where('user_id', $tech3->id)->exists()) {
            Technician::create([
                'user_id' => $tech3->id,
                'specialty' => 'Chip Level Service',
                'total_jobs' => 0,
                'performance_score' => 4.9,
            ]);
        }
    }
}
