<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create all 4 Healthcare Admin accounts with different categories
        
        // 1. Health Card Admin
        User::factory()->create([
            'name' => 'Health Card Admin',
            'email' => 'healthcardadmin@test.com',
            'role_id' => 2,
            'password' => bcrypt('qwerty123'),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'status' => 'active',
            'approved_at' => now(),
            'admin_category' => 'healthcard',
        ]);

        // 2. HIV Admin
        User::factory()->create([
            'name' => 'HIV Admin',
            'email' => 'hivadmin@test.com',
            'role_id' => 2,
            'password' => bcrypt('qwerty123'),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'status' => 'active',
            'approved_at' => now(),
            'admin_category' => 'hiv',
        ]);

        // 3. Pregnancy Admin
        User::factory()->create([
            'name' => 'Pregnancy Admin',
            'email' => 'pregadmin@test.com',
            'role_id' => 2,
            'password' => bcrypt('qwerty123'),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'status' => 'active',
            'approved_at' => now(),
            'admin_category' => 'pregnancy',
        ]);

        // 4. Medical Records Admin
        User::factory()->create([
            'name' => 'Medical Records Admin',
            'email' => 'medrecords@test.com',
            'role_id' => 2,
            'password' => bcrypt('qwerty123'),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'status' => 'active',
            'approved_at' => now(),
            'admin_category' => 'medical_records',
        ]);
    }
}
