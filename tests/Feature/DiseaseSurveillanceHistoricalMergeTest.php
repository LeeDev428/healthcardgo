<?php

declare(strict_types=1);

use App\Models\Barangay;
use App\Models\Disease;
use App\Models\HistoricalDiseaseData;
use App\Models\Role;
use App\Models\User;
use App\Services\DiseaseSurveillanceService;

it('merges historical data into statistics and heatmap', function () {
    Role::create(['id' => 1, 'name' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => 1]);

    $barangay = Barangay::factory()->create(['name' => 'Merged Barangay']);

    // Live cases: 3 confirmed dengue this month
    Disease::factory()->confirmed()->count(3)->create([
        'barangay_id' => $barangay->id,
        'diagnosis_date' => now()->subDays(2),
        'disease_type' => 'dengue',
    ]);

    // Historical cases: 5 dengue in same period window
    HistoricalDiseaseData::factory()->count(1)->create([
        'barangay_id' => $barangay->id,
        'record_date' => now()->subDays(10),
        'disease_type' => 'dengue',
        'case_count' => 5,
    ]);

    $service = app(DiseaseSurveillanceService::class);
    $stats = $service->getStatistics('dengue', '30days');
    expect($stats['total_cases'])->toBe(8); // 3 live + 5 historical

    $heatmap = $service->getHeatmapData('dengue', '30days');
    $entry = collect($heatmap)->firstWhere('barangay_name', 'Merged Barangay');
    expect($entry['cases_count'])->toBe(8);
});

it('includes historical data in monthly trend analysis', function () {
    Role::create(['id' => 1, 'name' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => 1]);

    $barangay = Barangay::factory()->create();

    // Live case last month
    Disease::factory()->confirmed()->create([
        'barangay_id' => $barangay->id,
        'diagnosis_date' => now()->subMonth()->startOfMonth()->addDays(5),
        'disease_type' => 'measles',
    ]);

    // Historical cases same month
    HistoricalDiseaseData::factory()->create([
        'barangay_id' => $barangay->id,
        'record_date' => now()->subMonth()->startOfMonth()->addDays(10),
        'disease_type' => 'measles',
        'case_count' => 4,
    ]);

    $service = app(DiseaseSurveillanceService::class);
    $trend = $service->getTrendAnalysis('measles', 2);

    $lastMonth = collect($trend['historical'])->firstWhere('date', now()->subMonth()->startOfMonth()->format('Y-m'));
    expect($lastMonth['cases'])->toBe(5); // 1 live + 4 historical
});
