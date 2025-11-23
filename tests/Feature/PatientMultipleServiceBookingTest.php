<?php

declare(strict_types=1);

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Services\AppointmentService;

beforeEach(function () {
    // Seed barangays
    $this->artisan('db:seed', ['--class' => 'BarangaySeeder']);

    // Create multiple services
    $this->consultationService = Service::factory()->create([
        'name' => 'General Consultation',
        'category' => 'general',
        'is_active' => true,
    ]);

    $this->labService = Service::factory()->create([
        'name' => 'Laboratory Test',
        'category' => 'laboratory',
        'is_active' => true,
    ]);

    $this->healthCardService = Service::factory()->create([
        'name' => 'Health Card Application',
        'category' => 'health_card',
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

test('patient can book multiple appointments for different services', function () {
    $scheduledDate1 = now()->addDays(8)->setTime(10, 0);
    $scheduledDate2 = now()->addDays(9)->setTime(11, 0);
    $scheduledDate3 = now()->addDays(10)->setTime(14, 0);

    // Book first appointment for consultation
    $appointment1 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate1,
        'notes' => 'Consultation appointment',
        'fee' => 0,
    ]);

    // Book second appointment for lab test
    $appointment2 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->labService->id,
        'scheduled_at' => $scheduledDate2,
        'notes' => 'Lab test appointment',
        'fee' => 0,
    ]);

    // Book third appointment for health card
    $appointment3 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->healthCardService->id,
        'scheduled_at' => $scheduledDate3,
        'notes' => 'Health card appointment',
        'fee' => 0,
    ]);

    expect($appointment1)->toBeInstanceOf(Appointment::class)
        ->and($appointment1->service_id)->toBe($this->consultationService->id)
        ->and($appointment2)->toBeInstanceOf(Appointment::class)
        ->and($appointment2->service_id)->toBe($this->labService->id)
        ->and($appointment3)->toBeInstanceOf(Appointment::class)
        ->and($appointment3->service_id)->toBe($this->healthCardService->id);

    // Verify all appointments exist
    expect(Appointment::where('patient_id', $this->patient->id)->count())->toBe(3);
});

test('patient cannot book multiple appointments for the same service when one is active', function () {
    $scheduledDate1 = now()->addDays(8)->setTime(10, 0);
    $scheduledDate2 = now()->addDays(9)->setTime(11, 0);

    // Book first appointment for consultation
    $appointment1 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate1,
        'notes' => 'First consultation',
        'fee' => 0,
    ]);

    expect($appointment1)->toBeInstanceOf(Appointment::class);

    // Try to book second appointment for the same service (should fail)
    $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate2,
        'notes' => 'Second consultation',
        'fee' => 0,
    ]);
})->throws(\Exception::class, 'already have an active appointment for this service');

test('patient can book same service after completing previous appointment', function () {
    $scheduledDate1 = now()->addDays(8)->setTime(10, 0);
    $scheduledDate2 = now()->addDays(9)->setTime(11, 0);

    // Book first appointment
    $appointment1 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate1,
        'fee' => 0,
    ]);

    expect($appointment1)->toBeInstanceOf(Appointment::class);

    // Complete the first appointment
    $appointment1->update(['status' => 'completed']);

    // Now book another appointment for the same service (should succeed)
    $appointment2 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate2,
        'fee' => 0,
    ]);

    expect($appointment2)->toBeInstanceOf(Appointment::class)
        ->and($appointment2->service_id)->toBe($this->consultationService->id);

    // Verify both appointments exist
    expect(Appointment::where('patient_id', $this->patient->id)->count())->toBe(2);
});

test('patient can book same service after cancelling previous appointment', function () {
    $scheduledDate1 = now()->addDays(8)->setTime(10, 0);
    $scheduledDate2 = now()->addDays(9)->setTime(11, 0);

    // Book first appointment
    $appointment1 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate1,
        'fee' => 0,
    ]);

    expect($appointment1)->toBeInstanceOf(Appointment::class);

    // Cancel the first appointment
    $this->appointmentService->cancelAppointment($appointment1, 'Change of plans');

    expect($appointment1->fresh()->status)->toBe('cancelled');

    // Now book another appointment for the same service (should succeed)
    $appointment2 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate2,
        'fee' => 0,
    ]);

    expect($appointment2)->toBeInstanceOf(Appointment::class)
        ->and($appointment2->service_id)->toBe($this->consultationService->id);
});

test('patient cannot book same service when appointment is pending', function () {
    $scheduledDate1 = now()->addDays(8)->setTime(10, 0);
    $scheduledDate2 = now()->addDays(9)->setTime(11, 0);

    // Book appointment (status will be pending for health card service)
    $appointment1 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->healthCardService->id,
        'scheduled_at' => $scheduledDate1,
        'fee' => 0,
    ]);

    expect($appointment1->status)->toBe('pending');

    // Try to book another appointment for same service
    $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->healthCardService->id,
        'scheduled_at' => $scheduledDate2,
        'fee' => 0,
    ]);
})->throws(\Exception::class, 'already have an active appointment for this service');

test('patient cannot book same service when appointment is confirmed', function () {
    $scheduledDate1 = now()->addDays(8)->setTime(10, 0);
    $scheduledDate2 = now()->addDays(9)->setTime(11, 0);

    // Book appointment (will auto-confirm for non-health-card services)
    $appointment1 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate1,
        'fee' => 0,
    ]);

    expect($appointment1->fresh()->status)->toBe('confirmed');

    // Try to book another appointment for same service
    $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate2,
        'fee' => 0,
    ]);
})->throws(\Exception::class, 'already have an active appointment for this service');

test('patient cannot book same service when appointment is checked in', function () {
    $scheduledDate1 = now()->addDays(8)->setTime(10, 0);
    $scheduledDate2 = now()->addDays(9)->setTime(11, 0);

    // Book and manually set to checked_in
    $appointment1 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate1,
        'fee' => 0,
    ]);

    $appointment1->update(['status' => 'checked_in']);

    // Try to book another appointment for same service
    $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate2,
        'fee' => 0,
    ]);
})->throws(\Exception::class, 'already have an active appointment for this service');

test('patient cannot book same service when appointment is in progress', function () {
    $scheduledDate1 = now()->addDays(8)->setTime(10, 0);
    $scheduledDate2 = now()->addDays(9)->setTime(11, 0);

    // Book and manually set to in_progress
    $appointment1 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate1,
        'fee' => 0,
    ]);

    $appointment1->update(['status' => 'in_progress']);

    // Try to book another appointment for same service
    $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate2,
        'fee' => 0,
    ]);
})->throws(\Exception::class, 'already have an active appointment for this service');

test('different patients can book same service simultaneously', function () {
    $scheduledDate = now()->addDays(8)->setTime(10, 0);

    // Create second patient
    $user2 = User::factory()->create([
        'role_id' => 4,
        'status' => 'active',
    ]);
    $patient2 = Patient::factory()->create([
        'user_id' => $user2->id,
    ]);

    // Both patients book the same service
    $appointment1 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate,
        'fee' => 0,
    ]);

    $appointment2 = $this->appointmentService->bookAppointment([
        'patient_id' => $patient2->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate,
        'fee' => 0,
    ]);

    expect($appointment1)->toBeInstanceOf(Appointment::class)
        ->and($appointment2)->toBeInstanceOf(Appointment::class)
        ->and($appointment1->patient_id)->toBe($this->patient->id)
        ->and($appointment2->patient_id)->toBe($patient2->id)
        ->and($appointment1->service_id)->toBe($this->consultationService->id)
        ->and($appointment2->service_id)->toBe($this->consultationService->id);
});

test('patient can have multiple active appointments across different services', function () {
    $scheduledDate = now()->addDays(8)->setTime(10, 0);

    // Book appointments for all three services
    $appointment1 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->consultationService->id,
        'scheduled_at' => $scheduledDate,
        'fee' => 0,
    ]);

    $appointment2 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->labService->id,
        'scheduled_at' => $scheduledDate->copy()->addHours(1),
        'fee' => 0,
    ]);

    $appointment3 = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->healthCardService->id,
        'scheduled_at' => $scheduledDate->copy()->addHours(2),
        'fee' => 0,
    ]);

    // Verify all have active statuses
    $activeStatuses = ['pending', 'confirmed', 'checked_in', 'in_progress'];

    expect($appointment1->fresh()->status)->toBeIn($activeStatuses)
        ->and($appointment2->fresh()->status)->toBeIn($activeStatuses)
        ->and($appointment3->fresh()->status)->toBeIn($activeStatuses);

    // Count active appointments
    $activeCount = Appointment::where('patient_id', $this->patient->id)
        ->whereIn('status', $activeStatuses)
        ->count();

    expect($activeCount)->toBe(3);
});
