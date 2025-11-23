<?php

declare(strict_types=1);

use App\Enums\AdminCategoryEnum;
use App\Livewire\HealthcareAdmin\AppointmentManagement;
use App\Models\Appointment;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Livewire\Livewire;

it('allows healthcare admin to change status with valid transitions', function () {
    // Create roles
    $adminRole = Role::firstOrCreate(['name' => 'healthcare_admin'], ['description' => 'Healthcare Admin']);

    // Create a healthcare admin user
    $admin = User::factory()->create([
        'role_id' => $adminRole->id,
        'admin_category' => AdminCategoryEnum::MedicalRecords,
        'status' => 'active',
    ]);

    // Create a service and a pending appointment
    $service = Service::factory()->create(['category' => 'health_card']);
    $appointment = Appointment::factory()->create([
        'service_id' => $service->id,
        'status' => 'pending',
        'scheduled_at' => now()->addDay(),
    ]);

    Livewire::actingAs($admin)
        ->test(AppointmentManagement::class)
        ->call('openStatusModal', $appointment->id)
        ->set('statusForm.to', 'confirmed')
        ->call('updateStatus')
        ->assertHasNoErrors();

    expect($appointment->fresh()->status)->toBe('confirmed');
});

it('requires cancellation reason when cancelling', function () {
    $adminRole = Role::firstOrCreate(['name' => 'healthcare_admin'], ['description' => 'Healthcare Admin']);
    $admin = User::factory()->create([
        'role_id' => $adminRole->id,
        'admin_category' => AdminCategoryEnum::MedicalRecords,
        'status' => 'active',
    ]);

    $service = Service::factory()->create(['category' => 'health_card']);
    $appointment = Appointment::factory()->confirmed()->create([
        'service_id' => $service->id,
        'scheduled_at' => now()->addDays(2),
    ]);

    Livewire::actingAs($admin)
        ->test(AppointmentManagement::class)
        ->call('openStatusModal', $appointment->id)
        ->set('statusForm.to', 'cancelled')
        ->call('updateStatus')
        ->assertHasErrors(['statusForm.reason' => 'required']);

    // Provide reason and succeed
    Livewire::actingAs($admin)
        ->test(AppointmentManagement::class)
        ->call('openStatusModal', $appointment->id)
        ->set('statusForm.to', 'cancelled')
        ->set('statusForm.reason', 'Patient requested cancellation')
        ->call('updateStatus')
        ->assertHasNoErrors();

    $appointment->refresh();
    expect($appointment->status)->toBe('cancelled')
        ->and($appointment->cancellation_reason)->toBe('Patient requested cancellation');
});

it('allows marking appointment as completed from confirmed status', function () {
    $adminRole = Role::firstOrCreate(['name' => 'healthcare_admin'], ['description' => 'Healthcare Admin']);
    $admin = User::factory()->create([
        'role_id' => $adminRole->id,
        'admin_category' => AdminCategoryEnum::MedicalRecords,
        'status' => 'active',
    ]);

    $service = Service::factory()->create(['category' => 'health_card']);
    $appointment = Appointment::factory()->confirmed()->create([
        'service_id' => $service->id,
        'scheduled_at' => now()->addDay(),
    ]);

    Livewire::actingAs($admin)
        ->test(AppointmentManagement::class)
        ->call('openStatusModal', $appointment->id)
        ->set('statusForm.to', 'completed')
        ->call('updateStatus')
        ->assertHasNoErrors();

    expect($appointment->fresh()->status)->toBe('completed');
});

it('allows marking appointment as completed from checked_in status', function () {
    $adminRole = Role::firstOrCreate(['name' => 'healthcare_admin'], ['description' => 'Healthcare Admin']);
    $admin = User::factory()->create([
        'role_id' => $adminRole->id,
        'admin_category' => AdminCategoryEnum::MedicalRecords,
        'status' => 'active',
    ]);

    $service = Service::factory()->create(['category' => 'health_card']);
    $appointment = Appointment::factory()->create([
        'service_id' => $service->id,
        'status' => 'checked_in',
        'scheduled_at' => now()->addDay(),
    ]);

    Livewire::actingAs($admin)
        ->test(AppointmentManagement::class)
        ->call('openStatusModal', $appointment->id)
        ->set('statusForm.to', 'completed')
        ->call('updateStatus')
        ->assertHasNoErrors();

    expect($appointment->fresh()->status)->toBe('completed');
});
