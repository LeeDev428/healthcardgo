<?php

declare(strict_types=1);

use App\Models\Barangay;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\seed;

beforeEach(function () {
    seed([
        \Database\Seeders\RoleSeeder::class,
        \Database\Seeders\BarangaySeeder::class,
    ]);
    Storage::fake('public');
});

test('patient can access profile page', function () {
    $patientRole = Role::where('name', 'patient')->first();
    $user = User::factory()->create([
        'role_id' => $patientRole->id,
        'status' => 'active',
    ]);

    $this->actingAs($user);

    $response = $this->get('/patient/profile');

    $response->assertStatus(200)
        ->assertSee('Complete Your Profile');
});

test('patient can create profile', function () {
    $patientRole = Role::where('name', 'patient')->first();
    $barangay = Barangay::first();

    $user = User::factory()->create([
        'role_id' => $patientRole->id,
        'status' => 'active',
    ]);

    $this->actingAs($user);

    $response = $this->get('/patient/profile');

    $response->assertStatus(200)
        ->assertSee('Complete Your Profile')
        ->assertSee('Date of Birth')
        ->assertSee('Gender')
        ->assertSee('Emergency Contact');
});

test('patient can update existing profile', function () {
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

    $response = $this->get('/patient/profile');

    $response->assertStatus(200)
        ->assertSee('Edit Profile')
        ->assertSee($patient->date_of_birth->format('Y-m-d'));
});

test('patient profile loads existing data correctly', function () {
    $patientRole = Role::where('name', 'patient')->first();
    $barangay = Barangay::first();

    $user = User::factory()->create([
        'role_id' => $patientRole->id,
        'status' => 'active',
    ]);

    $patient = Patient::factory()->create([
        'user_id' => $user->id,
        'barangay_id' => $barangay->id,
        'blood_type' => 'A+',
        'emergency_contact' => [
            'name' => 'Emergency Person',
            'number' => '09987654321',
            'relationship' => 'Parent',
        ],
    ]);

    $this->actingAs($user);

    $response = $this->get('/patient/profile');

    $response->assertStatus(200)
        ->assertSee('Emergency Person')
        ->assertSee('09987654321');
});

test('profile form validates required fields', function () {
    $patientRole = Role::where('name', 'patient')->first();

    $user = User::factory()->create([
        'role_id' => $patientRole->id,
        'status' => 'active',
    ]);

    $this->actingAs($user);

    $response = $this->get('/patient/profile');

    $response->assertStatus(200)
        ->assertSee('Date of Birth')
        ->assertSee('Gender')
        ->assertSee('Barangay')
        ->assertSee('Emergency Contact');
});
