<?php

declare(strict_types=1);

use App\Models\Barangay;
use App\Models\HealthCard;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;

use function Pest\Laravel\seed;

beforeEach(function () {
    seed([
        \Database\Seeders\RoleSeeder::class,
        \Database\Seeders\BarangaySeeder::class,
    ]);
});

test('patient can access health card page', function () {
    $patientRole = Role::where('name', 'patient')->first();
    $barangay = Barangay::first();

    $user = User::factory()->create([
        'role_id' => $patientRole->id,
        'status' => 'active',
    ]);

    $patient = Patient::factory()->create([
        'user_id' => $user->id,
        'barangay_id' => $barangay->id,
    ]);

    $this->actingAs($user);

    $response = $this->get('/patient/health-card');

    $response->assertStatus(200)
        ->assertSee('My Health Card');
});

test('patient without profile sees complete profile message', function () {
    $patientRole = Role::where('name', 'patient')->first();

    $user = User::factory()->create([
        'role_id' => $patientRole->id,
        'status' => 'active',
    ]);

    $this->actingAs($user);

    $response = $this->get('/patient/health-card');

    $response->assertStatus(200)
        ->assertSee('Complete Your Patient Profile')
        ->assertSee('Complete Profile');
});

test('patient without health card sees no health card message', function () {
    $patientRole = Role::where('name', 'patient')->first();
    $barangay = Barangay::first();

    $user = User::factory()->create([
        'role_id' => $patientRole->id,
        'status' => 'active',
    ]);

    $patient = Patient::factory()->create([
        'user_id' => $user->id,
        'barangay_id' => $barangay->id,
    ]);

    $this->actingAs($user);

    $response = $this->get('/patient/health-card');

    $response->assertStatus(200)
        ->assertSee('No Health Card Issued Yet')
        ->assertSee('To get your health card');
});

test('patient with health card can view it', function () {
    $patientRole = Role::where('name', 'patient')->first();
    $barangay = Barangay::first();

    $user = User::factory()->create([
        'role_id' => $patientRole->id,
        'status' => 'active',
    ]);

    $patient = Patient::factory()->create([
        'user_id' => $user->id,
        'barangay_id' => $barangay->id,
        'blood_type' => 'O+',
    ]);

    $healthCard = HealthCard::create([
        'patient_id' => $patient->id,
        'card_number' => HealthCard::generateCardNumber(),
        'issue_date' => now(),
        'expiry_date' => now()->addYear(),
        'qr_code' => 'data:image/png;base64,test',
        'status' => 'active',
        'medical_data' => [
            'blood_type' => $patient->blood_type,
            'barangay' => $barangay->name,
            'allergies' => ['Peanuts'],
            'emergency_contact' => [
                'name' => 'John Doe',
                'phone' => '09123456789',
            ],
        ],
    ]);

    $this->actingAs($user);

    $response = $this->get('/patient/health-card');

    $response->assertStatus(200)
        ->assertSee($healthCard->card_number)
        ->assertSee('O+')
        ->assertSee('Download PDF')
        ->assertSee('Download Image');
});

test('patient with expired health card sees expired warning', function () {
    $patientRole = Role::where('name', 'patient')->first();
    $barangay = Barangay::first();

    $user = User::factory()->create([
        'role_id' => $patientRole->id,
        'status' => 'active',
    ]);

    $patient = Patient::factory()->create([
        'user_id' => $user->id,
        'barangay_id' => $barangay->id,
    ]);

    $healthCard = HealthCard::create([
        'patient_id' => $patient->id,
        'card_number' => HealthCard::generateCardNumber(),
        'issue_date' => now()->subYears(2),
        'expiry_date' => now()->subYear(),
        'qr_code' => 'data:image/png;base64,test',
        'status' => 'active',
        'medical_data' => [
            'blood_type' => 'A+',
            'barangay' => $barangay->name,
        ],
    ]);

    $this->actingAs($user);

    $response = $this->get('/patient/health-card');

    $response->assertStatus(200)
        ->assertSee('Your health card has expired');
});

test('health card displays patient information correctly', function () {
    $patientRole = Role::where('name', 'patient')->first();
    $barangay = Barangay::first();

    $user = User::factory()->create([
        'name' => 'Jane Doe',
        'role_id' => $patientRole->id,
        'status' => 'active',
    ]);

    $patient = Patient::factory()->create([
        'user_id' => $user->id,
        'barangay_id' => $barangay->id,
        'patient_number' => 'P2025000001',
    ]);

    $healthCard = HealthCard::create([
        'patient_id' => $patient->id,
        'card_number' => 'HC2025123456',
        'issue_date' => now(),
        'expiry_date' => now()->addYear(),
        'qr_code' => 'data:image/png;base64,test',
        'status' => 'active',
        'medical_data' => [
            'blood_type' => 'AB+',
            'barangay' => $barangay->name,
        ],
    ]);

    $this->actingAs($user);

    $response = $this->get('/patient/health-card');

    $response->assertStatus(200)
        ->assertSee('Jane Doe')
        ->assertSee('P2025000001')
        ->assertSee('HC2025123456')
        ->assertSee('AB+')
        ->assertSee($barangay->name);
});
