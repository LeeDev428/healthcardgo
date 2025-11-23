<?php

declare(strict_types=1);

use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Services\AppointmentService;
use Illuminate\Support\Facades\Storage;

it('auto-confirms non health card service appointments', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $patient = Patient::factory()->for($user)->create();

    $service = Service::factory()->create([
        'category' => 'consultation',
        'requires_appointment' => true,
        'is_active' => true,
    ]);

    $serviceUnderTest = app(AppointmentService::class);

    $appointment = $serviceUnderTest->bookAppointment([
        'patient_id' => $patient->id,
        'doctor_id' => null,
        'service_id' => $service->id,
        'scheduled_at' => now()->addDays(8)->setTime(9, 0)->toDateTimeString(),
        'notes' => null,
        'fee' => 0,
    ]);

    expect($appointment->status)->toBe('confirmed');
});

it('keeps health card service appointments pending for admin approval', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $patient = Patient::factory()->for($user)->create();

    $service = Service::factory()->create([
        'category' => 'health_card',
        'requires_appointment' => true,
        'is_active' => true,
    ]);

    $serviceUnderTest = app(AppointmentService::class);

    $appointment = $serviceUnderTest->bookAppointment([
        'patient_id' => $patient->id,
        'doctor_id' => null,
        'service_id' => $service->id,
        'scheduled_at' => now()->addDays(8)->setTime(10, 0)->toDateTimeString(),
        'notes' => null,
        'fee' => 0,
    ]);

    expect($appointment->status)->toBe('pending');
});
