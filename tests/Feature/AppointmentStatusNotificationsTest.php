<?php

declare(strict_types=1);

use App\Livewire\HealthcareAdmin\AppointmentManagement;
use App\Models\Appointment;
use App\Models\Notification;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('sends confirmation notification to patient when status updated to confirmed', function (): void {
    // Create healthcare admin user
    $admin = User::factory()->create([
        'role_id' => 2,
        'admin_category' => 'medical_records',
        'status' => 'active',
    ]);
    /** @var \App\Models\User $admin */

    // Create a pending appointment with patient & service
    $appointment = Appointment::factory()->create();
    $patientUserId = $appointment->patient->user_id;

    actingAs($admin);

    // Open status modal for the appointment and confirm it
    Livewire::test(AppointmentManagement::class)
        ->call('openStatusModal', $appointment->id)
        ->set('statusForm.to', 'confirmed')
        ->call('updateStatus');

    expect(Notification::where('user_id', $patientUserId)
        ->where('type', 'appointment_confirmation')
        ->exists())->toBeTrue();
});

it('sends feedback request notification to patient when appointment completed', function (): void {
    $admin = User::factory()->create([
        'role_id' => 2,
        'admin_category' => 'medical_records',
        'status' => 'active',
    ]);
    /** @var \App\Models\User $admin */
    $appointment = Appointment::factory()->create();
    $patientUserId = $appointment->patient->user_id;

    actingAs($admin);

    // Confirm -> Checked In -> In Progress -> Completed sequence
    $component = Livewire::test(AppointmentManagement::class);

    // Confirm
    $component->call('openStatusModal', $appointment->id)
        ->set('statusForm.to', 'confirmed')
        ->call('updateStatus');

    // Refresh model
    $appointment->refresh();

    // Move to checked_in directly to avoid double-check-in logic in component
    $appointment->update(['status' => 'checked_in', 'check_in_at' => now()]);

    // In progress
    $component->call('openStatusModal', $appointment->id)
        ->set('statusForm.to', 'in_progress')
        ->call('updateStatus');
    $appointment->refresh();

    // Completed
    $component->call('openStatusModal', $appointment->id)
        ->set('statusForm.to', 'completed')
        ->call('updateStatus');

    expect(Notification::where('user_id', $patientUserId)
        ->where('type', 'feedback_request')
        ->exists())->toBeTrue();
});
