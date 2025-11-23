<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'description' => 'Super Administrator with full system access',
                'permissions' => json_encode([
                    'manage_users', 'manage_appointments', 'manage_services',
                    'manage_providers', 'manage_facilities', 'view_reports',
                    'system_settings',
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'healthcare_admin',
                'description' => 'Healthcare Administrator',
                'permissions' => json_encode([
                    'manage_appointments', 'manage_services', 'manage_providers',
                    'view_reports', 'view_patient_records',
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'doctor',
                'description' => 'Healthcare Provider/Doctor',
                'permissions' => json_encode([
                    'manage_own_appointments', 'view_patient_records',
                    'create_health_records',
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'patient',
                'description' => 'Patient/Citizen',
                'permissions' => json_encode([
                    'book_appointments', 'view_own_records', 'manage_profile',
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
