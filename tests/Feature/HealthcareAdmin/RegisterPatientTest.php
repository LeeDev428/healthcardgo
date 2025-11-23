<?php

declare(strict_types=1);

use App\Enums\AdminCategoryEnum;
use App\Livewire\HealthcareAdmin\RegisterPatient;
use App\Models\Barangay;
use App\Models\Notification;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

it('allows medical records admin to register a patient', function () {
    // Ensure roles exist
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $adminRoleId = \App\Models\Role::where('name', 'healthcare_admin')->first()->id;
    $patientRoleId = \App\Models\Role::where('name', 'patient')->first()->id;

    $admin = User::factory()->create([
        'role_id' => $adminRoleId,
        'admin_category' => AdminCategoryEnum::MedicalRecords,
        'status' => 'active',
    ]);

    $barangay = Barangay::first();
    if (! $barangay) {
        $barangay = Barangay::create(['name' => 'Test Barangay']);
    }

    Livewire::actingAs($admin)
        ->test(RegisterPatient::class)
        ->set('create_user_account', true)
        ->set('name', 'Test Patient')
        ->set('email', 'test.patient@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('contact_number', '09171234567')
        ->set('date_of_birth', '1990-01-01')
        ->set('gender', 'male')
        ->set('barangay_id', (string) $barangay->id)
        ->set('blood_type', 'A+')
        ->set('emergency_contact_name', 'Jane Doe')
        ->set('emergency_contact_number', '09170000000')
        ->call('submit')
        ->assertDispatched('success');

    $user = User::where('email', 'test.patient@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->status)->toBe('active')
        ->and($user->patient)->not->toBeNull();

    // Verify notifications were created (one for patient approval and admins)
    expect(Notification::where('user_id', $user->id)->where('type', 'account_approved')->exists())->toBeTrue();
});

it('rejects unauthorized healthcare admin categories', function () {
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);
    $adminRoleId = \App\Models\Role::where('name', 'healthcare_admin')->first()->id;
    $admin = User::factory()->create([
        'role_id' => $adminRoleId,
        'admin_category' => AdminCategoryEnum::HealthCard,
        'status' => 'active',
    ]);

    Livewire::actingAs($admin)
        ->test(RegisterPatient::class)
        ->assertStatus(403);
});

it('validates required fields', function () {
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);
    $adminRoleId = \App\Models\Role::where('name', 'healthcare_admin')->first()->id;
    $admin = User::factory()->create([
        'role_id' => $adminRoleId,
        'admin_category' => AdminCategoryEnum::MedicalRecords,
        'status' => 'active',
    ]);

    Livewire::actingAs($admin)
        ->test(RegisterPatient::class)
        ->set('create_user_account', true)
        ->call('submit')
        ->assertHasErrors([
            'name', 'email', 'password', 'contact_number', 'date_of_birth', 'gender', 'barangay_id', 'emergency_contact_name', 'emergency_contact_number',
        ]);
});

it('allows registration of walk-in patients without user account', function () {
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $adminRoleId = \App\Models\Role::where('name', 'healthcare_admin')->first()->id;

    $admin = User::factory()->create([
        'role_id' => $adminRoleId,
        'admin_category' => AdminCategoryEnum::MedicalRecords,
        'status' => 'active',
    ]);

    $barangay = Barangay::first();
    if (! $barangay) {
        $barangay = Barangay::create(['name' => 'Test Barangay']);
    }

    Livewire::actingAs($admin)
        ->test(RegisterPatient::class)
        ->set('create_user_account', false)
        ->set('name', 'Walk-in Patient')
        ->set('contact_number', '09171234567')
        ->set('date_of_birth', '1990-01-01')
        ->set('gender', 'female')
        ->set('barangay_id', (string) $barangay->id)
        ->set('blood_type', 'B+')
        ->set('emergency_contact_name', 'Emergency Contact')
        ->set('emergency_contact_number', '09170000000')
        ->set('disease_type', 'dengue')
        ->call('submit')
        ->assertDispatched('success');

    // Verify patient was created without a user account
    $patient = \App\Models\Patient::where('full_name', 'Walk-in Patient')->first();
    expect($patient)->not->toBeNull()
        ->and($patient->user_id)->toBeNull()
        ->and($patient->full_name)->toBe('Walk-in Patient')
        ->and($patient->contact_number)->toBe('09171234567');
});

it('displays walk-in patients correctly in patient list', function () {
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $adminRoleId = \App\Models\Role::where('name', 'healthcare_admin')->first()->id;

    $admin = User::factory()->create([
        'role_id' => $adminRoleId,
        'admin_category' => AdminCategoryEnum::MedicalRecords,
        'status' => 'active',
    ]);

    $barangay = Barangay::first();
    if (! $barangay) {
        $barangay = Barangay::create(['name' => 'Test Barangay']);
    }

    // Create a walk-in patient without user account
    $patient = \App\Models\Patient::create([
        'user_id' => null,
        'full_name' => 'Walk-in Test Patient',
        'contact_number' => '09171234567',
        'date_of_birth' => '1990-01-01',
        'gender' => 'male',
        'barangay_id' => $barangay->id,
        'blood_type' => 'O+',
        'emergency_contact' => [
            'name' => 'Emergency Contact',
            'number' => '09170000000',
        ],
    ]);

    // Test that PatientList component can display the walk-in patient without errors
    Livewire::actingAs($admin)
        ->test(\App\Livewire\HealthcareAdmin\PatientList::class)
        ->assertSee('Walk-in Test Patient')
        ->assertSee($patient->patient_number)
        ->call('viewDetails', $patient->id)
        ->assertSet('showDetailsModal', true)
        ->assertSee('Walk-in Test Patient')
        ->assertSee('N/A'); // Email should show N/A for walk-in patients
});
