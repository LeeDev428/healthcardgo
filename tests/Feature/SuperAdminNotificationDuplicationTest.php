<?php

declare(strict_types=1);

use App\Models\Appointment;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;

it('duplicates non-patient notifications to super admins', function (): void {
    // Ensure at least one super admin exists
    $superAdmin = User::factory()->create([
        'role_id' => 1,
        'status' => 'active',
    ]);
    /** @var \App\Models\User $superAdmin */

    // Create a doctor user (non-patient)
    $doctor = User::factory()->create([
        'role_id' => 3,
        'status' => 'active',
    ]);
    /** @var \App\Models\User $doctor */

    // Create an appointment with a doctor and patient
    $appointment = Appointment::factory()->create([
        'doctor_id' => $doctor->id,
    ]);

    // When notifying the doctor about a check-in, super admin should also receive a copy
    app(NotificationService::class)->sendPatientCheckedIn($appointment);

    expect(Notification::where('user_id', $doctor->id)
        ->where('type', 'patient_checked_in')
        ->exists())->toBeTrue();

    expect(Notification::where('user_id', $superAdmin->id)
        ->where('type', 'patient_checked_in')
        ->exists())->toBeTrue();
});

it('does not duplicate patient notifications to super admins', function (): void {
    $superAdmin = User::factory()->create([
        'role_id' => 1,
        'status' => 'active',
    ]);
    /** @var \App\Models\User $superAdmin */

    // Create appointment (has patient & service by factory)
    $appointment = Appointment::factory()->create();

    // Send a patient-targeted notification (confirmation)
    // This should create only for patient user_id, no copy to super admin
    $appointment->loadMissing(['service', 'patient']);
    app(NotificationService::class)->sendAppointmentConfirmation($appointment);

    $patientUserId = $appointment->patient->user_id;

    expect(Notification::where('user_id', $patientUserId)
        ->where('type', 'appointment_confirmation')
        ->exists())->toBeTrue();

    expect(Notification::where('user_id', $superAdmin->id)
        ->where('type', 'appointment_confirmation')
        ->exists())->toBeFalse();
});
