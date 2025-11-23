<?php

declare(strict_types=1);

use App\Models\Barangay;
use App\Models\Disease;
use App\Services\DiseaseSurveillanceService;

it('returns available years including past years', function () {
    $barangay = Barangay::factory()->create();

    // Create confirmed diseases across different years
    Disease::factory()->confirmed()->create([
        'barangay_id' => $barangay->id,
        'diagnosis_date' => '2022-05-10',
    ]);
    Disease::factory()->confirmed()->create([
        'barangay_id' => $barangay->id,
        'diagnosis_date' => '2023-03-15',
    ]);

    $service = app(DiseaseSurveillanceService::class);
    $years = $service->getAvailableYears();

    expect($years)->toContain(2022, 2023);
});

it('filters statistics by specific year period', function () {
    $barangay = Barangay::factory()->create();

    // Two cases in 2022, one in 2023
    Disease::factory()->confirmed()->count(2)->create([
        'barangay_id' => $barangay->id,
        'diagnosis_date' => '2022-01-20',
    ]);
    Disease::factory()->confirmed()->create([
        'barangay_id' => $barangay->id,
        'diagnosis_date' => '2023-07-11',
    ]);

    $service = app(DiseaseSurveillanceService::class);
    $stats2022 = $service->getStatistics(null, 'year:2022');

    expect($stats2022['total_cases'])->toBe(2);
});
