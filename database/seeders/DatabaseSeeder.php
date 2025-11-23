<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            BarangaySeeder::class,
            ServiceSeeder::class,
            AdminSeeder::class,
        ]);

        // Create test users
        User::factory()->create([
            'name' => 'Test Super Admin',
            'email' => 'admin@test.com',
            'role_id' => 1,
            'password' => bcrypt('qwerty123'),
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $doctor = User::factory()->create([
            'name' => 'Dr. Juan Dela Cruz',
            'email' => 'doctor@test.com',
            'role_id' => 3,
            'password' => bcrypt('qwerty123'),
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $patient = User::factory()->create([
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'role_id' => 4,
            'password' => bcrypt('qwerty123'),
            'status' => 'pending', // Patients start as pending
        ]);

        // Create 50 patients for disease seeding
        Patient::factory()->count(50)->create();

        // Seed historical disease data and current diseases
        $this->call([
            HistoricalDiseaseDataSeeder::class,
            DiseaseSeeder::class,
        ]);
    }
}
