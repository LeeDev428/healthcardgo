<?php

declare(strict_types=1);

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('shows appointments list for patient', function () {
    $service = Service::factory()->create(['is_active' => true]);
    $role = Role::firstOrCreate(['name' => 'patient'], [
        'description' => 'Patient/Citizen',
        'permissions' => ['book_appointments', 'view_own_records', 'manage_profile'],
        'is_active' => true,
    ]);
    $user = User::factory()->create(['role_id' => $role->id, 'status' => 'active']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    // Ensure role relationship exists by seeding roles if missing
    \Database\Seeders\RoleSeeder::class; // hint for clarity

    // Create a few appointments for the patient
    Appointment::factory()->count(3)->create([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'status' => 'pending',
        'scheduled_at' => now()->addDays(8)->setTime(10, 0),
    ]);

    actingAs($user);
    $response = get(route('patient.appointments.list'));
    $response->assertStatus(200)->assertSee('My Appointments');
});

it('shows appointment details and restricts access to owner', function () {
    $service = Service::factory()->create(['is_active' => true]);
    $role = Role::firstOrCreate(['name' => 'patient'], [
        'description' => 'Patient/Citizen',
        'permissions' => ['book_appointments', 'view_own_records', 'manage_profile'],
        'is_active' => true,
    ]);
    $user = User::factory()->create(['role_id' => $role->id, 'status' => 'active']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    $appointment = Appointment::factory()->create([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'status' => 'pending',
        'scheduled_at' => now()->addDays(8)->setTime(11, 0),
    ]);

    actingAs($user);
    get(route('patient.appointments.details', ['appointment' => $appointment->id]))
        ->assertStatus(200)
        ->assertSee('Appointment Details')
        ->assertSee($appointment->appointment_number);

    // Another active patient should be forbidden
    $otherUser = User::factory()->create(['role_id' => $role->id, 'status' => 'active']);
    Patient::factory()->create(['user_id' => $otherUser->id]);
    actingAs($otherUser);
    get(route('patient.appointments.details', ['appointment' => $appointment->id]))
        ->assertStatus(403);
});
