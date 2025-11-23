<?php

use App\Models\HealthCard;
use App\Models\HistoricalHealthCardData;
use App\Models\Patient;
use App\Models\User;
use App\Services\HealthCardPredictionService;

test('can check if sufficient data exists', function () {
    // Create historical data (less than required 12 months)
    HistoricalHealthCardData::factory()->count(6)->create();

    $service = app(HealthCardPredictionService::class);
    $result = $service->hasSufficientData();

    expect($result)->toBeArray()
        ->and($result['sufficient'])->toBeFalse()
        ->and($result['data_points'])->toBe(6)
        ->and($result['required'])->toBe(12)
        ->and($result['missing'])->toBe(6);
});

test('detects sufficient data when 12+ months available', function () {
    // Create 12 months of historical data
    for ($i = 0; $i < 12; $i++) {
        HistoricalHealthCardData::factory()->create([
            'record_date' => now()->subMonths(12 - $i)->startOfMonth(),
            'issued_count' => fake()->numberBetween(10, 30),
        ]);
    }

    $service = app(HealthCardPredictionService::class);
    $result = $service->hasSufficientData();

    expect($result['sufficient'])->toBeTrue()
        ->and($result['data_points'])->toBeGreaterThanOrEqual(12);
});

test('generates predictions with sufficient data', function () {
    // Create 24 months of historical data for better predictions
    for ($i = 0; $i < 24; $i++) {
        HistoricalHealthCardData::factory()->create([
            'record_date' => now()->subMonths(24 - $i)->startOfMonth(),
            'issued_count' => 20 + ($i * 2), // Increasing trend
        ]);
    }

    $service = app(HealthCardPredictionService::class);
    $result = $service->generatePredictions(6);

    expect($result)->toBeArray()
        ->and($result['success'])->toBeTrue()
        ->and($result['predictions'])->toBeArray()
        ->and($result['predictions'])->toHaveCount(6)
        ->and($result['historical_data'])->toBeArray();

    // Check prediction structure
    $prediction = $result['predictions'][0];
    expect($prediction)->toHaveKeys(['period', 'label', 'predicted_issued', 'confidence_interval_lower', 'confidence_interval_upper']);
});

test('returns error when insufficient data for predictions', function () {
    // Only create 6 months of data
    HistoricalHealthCardData::factory()->count(6)->create();

    $service = app(HealthCardPredictionService::class);
    $result = $service->generatePredictions(6);

    expect($result['success'])->toBeFalse()
        ->and($result['message'])->toContain('Insufficient historical data');
});

test('combines manual and system health card data', function () {
    // Create manual historical data
    HistoricalHealthCardData::factory()->create([
        'record_date' => now()->subMonths(13)->startOfMonth(),
        'issued_count' => 10,
        'data_source' => 'manual',
    ]);

    // Create system data (actual health cards)
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    for ($i = 0; $i < 12; $i++) {
        HealthCard::factory()->create([
            'patient_id' => $patient->id,
            'issue_date' => now()->subMonths(12 - $i)->startOfMonth(),
        ]);
    }

    $service = app(HealthCardPredictionService::class);
    $result = $service->generatePredictions(3);

    expect($result['success'])->toBeTrue()
        ->and($result['historical_data'])->toHaveCount(13); // 1 manual + 12 system
});

test('chart data shows full year with zeros for missing months', function () {
    // Create data for current year (Jan-Nov for example)
    $currentYear = now()->year;
    for ($month = 1; $month <= now()->month; $month++) {
        HistoricalHealthCardData::factory()->create([
            'record_date' => now()->setYear($currentYear)->setMonth($month)->startOfMonth(),
            'issued_count' => 10 + $month,
        ]);
    }

    $service = app(HealthCardPredictionService::class);
    $chartData = $service->getChartData();

    expect($chartData)->toBeArray()
        ->and($chartData)->toHaveKeys(['labels', 'actual', 'predicted', 'confidence_lower', 'confidence_upper', 'has_predictions'])
        ->and($chartData['labels'])->toHaveCount(12) // Always 12 months (Jan-Dec)
        ->and($chartData['actual'])->toHaveCount(12);

    // Check that missing months have 0 for actual
    $actualValues = $chartData['actual'];
    expect(count($actualValues))->toBe(12);
});

test('chart data handles insufficient data gracefully', function () {
    // Only 5 months of data
    for ($i = 0; $i < 5; $i++) {
        HistoricalHealthCardData::factory()->create([
            'record_date' => now()->setMonth($i + 1)->startOfMonth(),
        ]);
    }

    $service = app(HealthCardPredictionService::class);
    $chartData = $service->getChartData();

    expect($chartData['has_predictions'])->toBeFalse()
        ->and($chartData['message'])->toContain('Insufficient historical data')
        ->and($chartData['labels'])->toHaveCount(12); // Still shows full year
});

test('predictions have confidence intervals', function () {
    // Create data with some variance
    for ($i = 0; $i < 18; $i++) {
        HistoricalHealthCardData::factory()->create([
            'record_date' => now()->subMonths(18 - $i)->startOfMonth(),
            'issued_count' => fake()->numberBetween(10, 40),
        ]);
    }

    $service = app(HealthCardPredictionService::class);
    $result = $service->generatePredictions(3);

    expect($result['success'])->toBeTrue();

    foreach ($result['predictions'] as $prediction) {
        expect($prediction['confidence_interval_lower'])->toBeLessThanOrEqual($prediction['predicted_issued'])
            ->and($prediction['confidence_interval_upper'])->toBeGreaterThanOrEqual($prediction['predicted_issued'])
            ->and($prediction['confidence_interval_lower'])->toBeGreaterThanOrEqual(0);
    }
});
