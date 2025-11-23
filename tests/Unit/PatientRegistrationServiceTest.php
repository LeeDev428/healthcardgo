<?php

declare(strict_types=1);

use App\Enums\AdminCategoryEnum;
use App\Models\User;
use App\Services\PatientRegistrationService;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Artisan;

/** @noinspection PhpUndefinedFunctionInspection */
uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class)->group('registration');

it('registers a public patient as pending', function () {
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);
    $service = new PatientRegistrationService;
    $barangay = \App\Models\Barangay::first() ?? \App\Models\Barangay::create(['name' => 'Test Barangay']);

    $user = $service->register([
        'name' => 'Public User',
        'email' => 'public@example.com',
        'password' => 'password123',
        'contact_number' => '09171234567',
        'date_of_birth' => '1990-01-01',
        'gender' => 'male',
        'barangay_id' => $barangay->id,
        'blood_type' => 'A+',
        'emergency_contact_name' => 'Jane Doe',
        'emergency_contact_number' => '09170000000',
        'allergies' => 'Peanuts',
        'current_medications' => 'Med1',
        'medical_history' => 'History',
    ], null, internal: false);

    expect($user->status)->toBe('pending')
        ->and($user->patient)->not->toBeNull();
});

it('registers an internal patient as active', function () {
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);
    $service = new PatientRegistrationService;
    $barangay = \App\Models\Barangay::first() ?? \App\Models\Barangay::create(['name' => 'Test Barangay']);
    $creator = User::factory()->create([
        'role_id' => \App\Models\Role::where('name', 'healthcare_admin')->first()->id,
        'admin_category' => AdminCategoryEnum::MedicalRecords,
        'status' => 'active',
    ]);

    $user = $service->register([
        'name' => 'Internal User',
        'email' => 'internal@example.com',
        'password' => 'password123',
        'contact_number' => '09171234568',
        'date_of_birth' => '1985-05-05',
        'gender' => 'female',
        'barangay_id' => $barangay->id,
        'blood_type' => 'B+',
        'emergency_contact_name' => 'John Doe',
        'emergency_contact_number' => '09171111111',
        'allergies' => '',
        'current_medications' => '',
        'medical_history' => '',
    ], $creator, internal: true);

    expect($user->status)->toBe('active')
        ->and($user->approved_by)->toBe($creator->id)
        ->and($user->patient)->not->toBeNull();
});
