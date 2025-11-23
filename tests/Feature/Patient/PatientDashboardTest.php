<?php

declare(strict_types=1);

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

test('patient with incomplete profile can access dashboard', function () {
    $patientRole = Role::where('name', 'patient')->first();

    $user = User::factory()->create([
        'role_id' => $patientRole->id,
        'status' => 'active',
    ]);

    $this->actingAs($user);

    $response = $this->get('/patient/dashboard');

    $response->assertStatus(200)
        ->assertSee('Complete Your Profile')
        ->assertSee('Your patient profile is incomplete');
});

test('patient with complete profile can access dashboard', function () {
    $patientRole = Role::where('name', 'patient')->first();

    $user = User::factory()->create([
        'role_id' => $patientRole->id,
        'status' => 'active',
    ]);

    Patient::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->get('/patient/dashboard');

    $response->assertStatus(200)
        ->assertDontSee('Complete Your Profile')
        ->assertSee('Welcome back');
});

test('patient dashboard shows statistics correctly', function () {
    $patientRole = Role::where('name', 'patient')->first();

    $user = User::factory()->create([
        'role_id' => $patientRole->id,
        'status' => 'active',
    ]);

    $patient = Patient::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->get('/patient/dashboard');

    $response->assertStatus(200)
        ->assertSee('Total Appointments')
        ->assertSee('Upcoming');
});
