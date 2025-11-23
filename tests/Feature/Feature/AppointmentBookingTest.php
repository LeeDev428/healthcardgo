<?php

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Services\AppointmentService;

beforeEach(function () {
    // Seed barangays
    $this->artisan('db:seed', ['--class' => 'BarangaySeeder']);

    $this->service = Service::factory()->create([
        'name' => 'General Consultation',
        'category' => 'health_card', // Use health_card category so it stays pending
        'is_active' => true,
    ]);

    $this->user = User::factory()->create([
        'role_id' => 4, // Patient role
        'status' => 'active',
    ]);

    $this->patient = Patient::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->appointmentService = app(AppointmentService::class);
});

test('patient can book appointment beyond 7-day lead time', function () {
    $scheduledDate = now()->addDays(8)->setTime(10, 0);

    $appointment = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->service->id,
        'scheduled_at' => $scheduledDate,
        'notes' => 'Test appointment',
        'fee' => 0,
    ]);

    expect($appointment)->toBeInstanceOf(Appointment::class)
        ->and($appointment->queue_number)->toBe(1)
        ->and($appointment->status)->toBe('pending');
});

test('cannot book appointment within 7-day lead time', function () {
    $scheduledDate = now()->addDays(3)->setTime(10, 0);

    $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->service->id,
        'scheduled_at' => $scheduledDate,
        'notes' => 'Test appointment',
        'fee' => 0,
    ]);
})->throws(\Exception::class, '7 days');

test('queue numbers increment correctly for same date and service', function () {
    $scheduledDate = now()->addDays(8)->setTime(10, 0);

    $appointment1 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->service->id,
        'scheduled_at' => $scheduledDate,
        'fee' => 0,
    ]);

    $patient2 = Patient::factory()->create();
    $appointment2 = $this->appointmentService->bookAppointment([
        'patient_id' => $patient2->id,
        'service_id' => $this->service->id,
        'scheduled_at' => $scheduledDate,
        'fee' => 0,
    ]);

    expect($appointment1->queue_number)->toBe(1)
        ->and($appointment2->queue_number)->toBe(2);
});

test('cannot book more than 100 appointments per day per service', function () {
    $scheduledDate = now()->addDays(8)->setTime(10, 0);

    // Create 100 appointments
    for ($i = 0; $i < 100; $i++) {
        $patient = Patient::factory()->create();
        Appointment::create([
            'patient_id' => $patient->id,
            'service_id' => $this->service->id,
            'scheduled_at' => $scheduledDate,
            'queue_number' => $i + 1,
            'status' => 'pending',
        ]);
    }

    // Try to book 101st appointment
    $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->service->id,
        'scheduled_at' => $scheduledDate,
        'fee' => 0,
    ]);
})->throws(\Exception::class, 'Queue is full');

test('patient can cancel appointment with 24+ hours notice', function () {
    $scheduledDate = now()->addDays(8)->setTime(10, 0);

    $appointment = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->service->id,
        'scheduled_at' => $scheduledDate,
        'fee' => 0,
    ]);

    $result = $this->appointmentService->cancelAppointment($appointment, 'Change of plans');

    expect($result)->toBeTrue()
        ->and($appointment->fresh()->status)->toBe('cancelled')
        ->and($appointment->fresh()->cancellation_reason)->toBe('Change of plans');
});

test('cannot cancel appointment with less than 24 hours notice', function () {
    $scheduledDate = now()->addHours(20)->setTime(10, 0);

    $appointment = Appointment::create([
        'patient_id' => $this->patient->id,
        'service_id' => $this->service->id,
        'scheduled_at' => $scheduledDate,
        'queue_number' => 1,
        'status' => 'confirmed',
    ]);

    $this->appointmentService->cancelAppointment($appointment, 'Emergency');
})->throws(\Exception::class, '24+ hours');

test('queue numbers recalculate after cancellation', function () {
    $scheduledDate = now()->addDays(8)->setTime(10, 0);

    // Create 3 appointments
    $appointment1 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->service->id,
        'scheduled_at' => $scheduledDate,
        'fee' => 0,
    ]);

    $patient2 = Patient::factory()->create();
    $appointment2 = $this->appointmentService->bookAppointment([
        'patient_id' => $patient2->id,
        'service_id' => $this->service->id,
        'scheduled_at' => $scheduledDate,
        'fee' => 0,
    ]);

    $patient3 = Patient::factory()->create();
    $appointment3 = $this->appointmentService->bookAppointment([
        'patient_id' => $patient3->id,
        'service_id' => $this->service->id,
        'scheduled_at' => $scheduledDate,
        'fee' => 0,
    ]);

    expect($appointment1->queue_number)->toBe(1)
        ->and($appointment2->queue_number)->toBe(2)
        ->and($appointment3->queue_number)->toBe(3);

    // Cancel middle appointment
    $this->appointmentService->cancelAppointment($appointment2, 'Test cancellation');

    // Check queue numbers recalculated
    expect($appointment1->fresh()->queue_number)->toBe(1)
        ->and($appointment3->fresh()->queue_number)->toBe(2);
});

test('get available slots returns correct counts', function () {
    $scheduledDate = now()->addDays(8)->startOfDay();

    // Book 3 appointments
    for ($i = 0; $i < 3; $i++) {
        $patient = Patient::factory()->create();
        Appointment::create([
            'patient_id' => $patient->id,
            'service_id' => $this->service->id,
            'scheduled_at' => $scheduledDate->copy()->setTime(10, 0),
            'queue_number' => $i + 1,
            'status' => 'pending',
        ]);
    }

    $slots = $this->appointmentService->getAvailableSlots($scheduledDate, $this->service->id);

    expect($slots['available_slots'])->toBe(97)
        ->and($slots['booked_slots'])->toBe(3)
        ->and($slots['is_full'])->toBeFalse();
});
