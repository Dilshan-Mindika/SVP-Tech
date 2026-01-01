<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Technician;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        User::firstOrCreate(
            ['email' => 'admin@svp.tech'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('Dila1011@'),
                'role' => 'admin',
            ]
        );
        
        // 2. Technician User
        $techUser = User::firstOrCreate(
            ['email' => 'tech@svp.tech'],
            [
                'name' => 'John Technician',
                'password' => bcrypt('Dila1011@'),
                'role' => 'technician',
            ]
        );

        // 3. Ensure Technician Profile Exists
        if (!Technician::where('user_id', $techUser->id)->exists()) {
            Technician::create([
                'user_id' => $techUser->id,
                'specialty' => 'General Repairs',
                'total_jobs' => 0,
                'performance_score' => 5.0,
            ]);
        }
    }
}
