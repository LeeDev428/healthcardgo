<?php

use App\Models\Patient;
use App\Models\Service;
use App\Services\AppointmentService;
use Illuminate\Support\Facades\Storage;

it('generates a digital copy and qr when booking', function () {
    Storage::fake('public');

    // Create minimal models required
    $service = Service::factory()->create();
    $patient = Patient::factory()->create();

    $serviceData = [
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'scheduled_at' => now()->addDays(8)->toDateTimeString(),
    ];

    $appointment = app(AppointmentService::class)->bookAppointment($serviceData);

    expect($appointment->digital_copy_path)->not->toBeNull();
    expect($appointment->qr_code_path)->not->toBeNull();

    Storage::disk('public')->assertExists($appointment->digital_copy_path);
    Storage::disk('public')->assertExists($appointment->qr_code_path);
});
