<?php

declare(strict_types=1);

use App\Models\Appointment;
use App\Models\Barangay;
use App\Models\Disease;
use App\Models\Patient;
use App\Models\Service;
use App\Services\ReportService;

it('includes legacy hiv category when filtering hiv_testing', function () {
    // Create a service with legacy category value 'hiv'
    $service = Service::query()->create([
        'name' => 'HIV Testing',
        'description' => 'Legacy hiv category service',
        'duration_minutes' => 30,
        'fee' => 0,
        'category' => 'hiv',
        'is_active' => true,
    ]);

    // Create an appointment tied to that service within range
    $appointment = Appointment::factory()->create([
        'service_id' => $service->id,
        'scheduled_at' => now()->setTime(9, 0),
        'status' => 'confirmed',
    ]);

    $svc = app(ReportService::class);

    $dataset = $svc->getAppointmentsReport([
        'from' => now()->startOfMonth()->toDateString(),
        'to' => now()->toDateString(),
        'service_category' => 'hiv_testing', // canonical filter
    ], null);

    expect($dataset['meta']['total'])->toBeGreaterThanOrEqual(1);
    $items = is_object($dataset['list']) ? $dataset['list']->items() : ($dataset['list']['data'] ?? []);
    $numbers = collect($items)->pluck('number');
    expect($numbers)->toContain($appointment->appointment_number);
});

it('filters diseases report by barangay', function () {
    // Create two barangays
    $barangay1 = Barangay::factory()->create(['name' => 'Test Barangay 1']);
    $barangay2 = Barangay::factory()->create(['name' => 'Test Barangay 2']);

    // Create patients in different barangays
    $patient1 = Patient::factory()->create(['barangay_id' => $barangay1->id]);
    $patient2 = Patient::factory()->create(['barangay_id' => $barangay2->id]);

    // Create diseases in different barangays
    $disease1 = Disease::factory()->create([
        'patient_id' => $patient1->id,
        'barangay_id' => $barangay1->id,
        'disease_type' => 'dengue',
        'diagnosis_date' => now(),
    ]);

    $disease2 = Disease::factory()->create([
        'patient_id' => $patient2->id,
        'barangay_id' => $barangay2->id,
        'disease_type' => 'dengue',
        'diagnosis_date' => now(),
    ]);

    $svc = app(ReportService::class);

    // Filter by barangay1
    $dataset = $svc->getDiseasesReport([
        'from' => now()->startOfMonth()->toDateString(),
        'to' => now()->toDateString(),
        'barangay_id' => $barangay1->id,
    ], null);

    expect($dataset['meta']['total'])->toBe(1);
    $items = is_object($dataset['list']) ? $dataset['list']->items() : ($dataset['list']['data'] ?? []);
    $diseaseIds = collect($items)->pluck('id');
    expect($diseaseIds)->toContain($disease1->id);
    expect($diseaseIds)->not->toContain($disease2->id);
});
