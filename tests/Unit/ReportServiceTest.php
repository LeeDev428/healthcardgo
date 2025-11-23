<?php

declare(strict_types=1);

use App\Models\Appointment;
use App\Models\Service;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('includes legacy hiv category when filtering hiv_testing', function () {
    // Create a service with legacy category value 'hiv'
    $service = Service::query()->create([
        'name' => 'HIV Testing',
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
    $numbers = collect($dataset['list']->toArray())->pluck('number');
    expect($numbers)->toContain($appointment->appointment_number);
});
