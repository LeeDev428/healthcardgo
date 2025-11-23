<?php

declare(strict_types=1);

use App\Models\Barangay;
use App\Models\Disease;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('creates a walk-in patient without a user account and records disease', function () {
    // Ensure prerequisite role exists for healthcare admin
    $healthcareRole = Role::firstOrCreate(['name' => 'healthcare_admin'], [
        'description' => 'Healthcare Admin',
    ]);

    // Acting user (medical records admin category simulated)
    $admin = User::factory()->create([
        'role_id' => $healthcareRole->id,
        'admin_category' => 'medical_records',
        'status' => 'active',
    ]);

    // Dependency barangay
    $barangay = Barangay::factory()->create();

    /** @var User $admin */
    actingAs($admin);

    // Simulate walk-in patient creation directly (component logic already exercised elsewhere)
    $patient = Patient::create([
        'user_id' => null,
        'full_name' => 'Walk In Tester',
        'contact_number' => '09171234567',
        'date_of_birth' => now()->subYears(25)->format('Y-m-d'),
        'gender' => 'male',
        'barangay_id' => $barangay->id,
        'blood_type' => 'O+',
        'emergency_contact' => [
            'name' => 'Jane Tester',
            'number' => '09170000000',
        ],
        'allergies' => null,
        'current_medications' => null,
        'medical_history' => null,
    ]);

    expect($patient->id)->not()->toBeNull();
    expect($patient->user_id)->toBeNull();
    expect($patient->full_name)->toBe('Walk In Tester');

    // Create disease record for patient
    $disease = Disease::create([
        'patient_id' => $patient->id,
        'medical_record_id' => null,
        'disease_type' => 'dengue',
        'case_number' => 'TEST-'.uniqid(),
        'status' => 'suspected',
        'reported_date' => now()->toDateString(),
        'diagnosis_date' => now()->toDateString(),
        'reported_by' => $admin->id,
    ]);

    expect($disease->exists)->toBeTrue();
    expect($disease->patient_id)->toBe($patient->id);
    expect($disease->disease_type)->toBe('dengue');
});
