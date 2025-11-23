<?php

declare(strict_types=1);

use App\Livewire\Admin\DiseaseSurveillance;
use App\Models\Barangay;
use App\Models\Disease;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use App\Services\DiseaseSurveillanceService;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('admin can access disease surveillance dashboard', function () {
    Role::create(['id' => 1, 'name' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => 1]);

    actingAs($admin)
        ->get(route('admin.disease-surveillance'))
        ->assertSuccessful();
});

test('healthcare admin cannot access disease surveillance dashboard', function () {
    Role::create(['id' => 1, 'name' => 'Super Admin']);
    Role::create(['id' => 2, 'name' => 'Healthcare Admin']);
    $healthcareAdmin = User::factory()->create(['role_id' => 2]);

    actingAs($healthcareAdmin)
        ->get(route('admin.disease-surveillance'))
        ->assertForbidden();
});

test('doctor cannot access disease surveillance dashboard', function () {
    Role::create(['id' => 1, 'name' => 'Super Admin']);
    Role::create(['id' => 3, 'name' => 'Doctor']);
    $doctor = User::factory()->create(['role_id' => 3]);

    actingAs($doctor)
        ->get(route('admin.disease-surveillance'))
        ->assertForbidden();
});

test('disease surveillance component renders correctly', function () {
    Role::create(['id' => 1, 'name' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => 1]);

    Livewire::actingAs($admin)
        ->test(DiseaseSurveillance::class)
        ->assertSuccessful()
        ->assertSee('Disease Surveillance Dashboard');
});

test('filters are working correctly', function () {
    Role::create(['id' => 1, 'name' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => 1]);

    Livewire::actingAs($admin)
        ->test(DiseaseSurveillance::class)
        ->set('selectedDiseaseType', 'dengue')
        ->set('selectedPeriod', '30days')
        ->assertSet('selectedDiseaseType', 'dengue')
        ->assertSet('selectedPeriod', '30days');
});

test('disease surveillance displays statistics', function () {
    Role::create(['id' => 1, 'name' => 'Super Admin']);
    Role::create(['id' => 3, 'name' => 'Doctor']);
    $admin = User::factory()->create(['role_id' => 1]);
    $doctor = User::factory()->create(['role_id' => 3]);
    $barangay = Barangay::factory()->create();
    $patient = Patient::factory()->create();

    Disease::factory()->create([
        'patient_id' => $patient->id,
        'disease_type' => 'dengue',
        'status' => 'confirmed',
        'barangay_id' => $barangay->id,
        'diagnosis_date' => now(),
        'reported_by' => $doctor->id,
    ]);

    Livewire::actingAs($admin)
        ->test(DiseaseSurveillance::class)
        ->assertSee('Total Cases')
        ->assertSee('New Cases (7 Days)');
});

test('heatmap data is generated correctly', function () {
    Role::create(['id' => 3, 'name' => 'Doctor']);
    $doctor = User::factory()->create(['role_id' => 3]);
    $barangay = Barangay::factory()->create(['name' => 'Test Barangay']);
    $patient = Patient::factory()->create();

    Disease::factory()->count(3)->create([
        'patient_id' => $patient->id,
        'disease_type' => 'dengue',
        'status' => 'confirmed',
        'barangay_id' => $barangay->id,
        'diagnosis_date' => now(),
        'reported_by' => $doctor->id,
    ]);

    $service = app(DiseaseSurveillanceService::class);
    $heatmapData = $service->getHeatmapData('dengue', '30days');

    expect($heatmapData)->toBeArray()
        ->and($heatmapData)->not->toBeEmpty()
        ->and($heatmapData[0])->toHaveKey('barangay_name')
        ->and($heatmapData[0])->toHaveKey('cases_count')
        ->and($heatmapData[0]['cases_count'])->toBe(3);
});

test('trend analysis returns historical data', function () {
    Role::create(['id' => 3, 'name' => 'Doctor']);
    $doctor = User::factory()->create(['role_id' => 3]);
    $barangay = Barangay::factory()->create();
    $patient = Patient::factory()->create();

    Disease::factory()->create([
        'patient_id' => $patient->id,
        'disease_type' => 'dengue',
        'status' => 'confirmed',
        'barangay_id' => $barangay->id,
        'diagnosis_date' => now()->subMonth(),
        'reported_by' => $doctor->id,
    ]);

    $service = app(DiseaseSurveillanceService::class);
    $trendData = $service->getTrendAnalysis('dengue', 12);

    expect($trendData)->toHaveKeys(['historical', 'predicted', 'disease_type'])
        ->and($trendData['historical'])->toBeArray()
        ->and($trendData['disease_type'])->toBe('dengue');
});

test('high risk barangays are detected correctly', function () {
    Role::create(['id' => 3, 'name' => 'Doctor']);
    $doctor = User::factory()->create(['role_id' => 3]);
    $barangay = Barangay::factory()->create();
    $patient = Patient::factory()->create();

    Disease::factory()->count(6)->create([
        'patient_id' => $patient->id,
        'disease_type' => 'dengue',
        'status' => 'confirmed',
        'barangay_id' => $barangay->id,
        'diagnosis_date' => now(),
        'reported_by' => $doctor->id,
    ]);

    $service = app(DiseaseSurveillanceService::class);
    $highRisk = $service->getHighRiskBarangays('dengue', '30days', 5);

    expect($highRisk)->toBeArray()
        ->and($highRisk)->not->toBeEmpty()
        ->and($highRisk[0])->toHaveKey('barangay_name')
        ->and($highRisk[0])->toHaveKey('cases_count')
        ->and($highRisk[0]['cases_count'])->toBe(6);
});

test('outbreak detection works correctly', function () {
    Role::create(['id' => 3, 'name' => 'Doctor']);
    $doctor = User::factory()->create(['role_id' => 3]);
    $barangay = Barangay::factory()->create();
    $patient = Patient::factory()->create();

    Disease::factory()->count(5)->create([
        'patient_id' => $patient->id,
        'disease_type' => 'dengue',
        'status' => 'confirmed',
        'barangay_id' => $barangay->id,
        'diagnosis_date' => now(),
        'reported_date' => now(),
        'reported_by' => $doctor->id,
    ]);

    $service = app(DiseaseSurveillanceService::class);
    $outbreaks = $service->detectOutbreaks('dengue');

    expect($outbreaks)->toBeArray()
        ->and($outbreaks)->not->toBeEmpty()
        ->and($outbreaks[0])->toHaveKey('barangay_name')
        ->and($outbreaks[0])->toHaveKey('recent_cases')
        ->and($outbreaks[0]['recent_cases'])->toBe(5);
});

test('tab switching works correctly', function () {
    Role::create(['id' => 1, 'name' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => 1]);

    Livewire::actingAs($admin)
        ->test(DiseaseSurveillance::class)
        ->assertSet('activeTab', 'overview')
        ->call('setTab', 'heatmap')
        ->assertSet('activeTab', 'heatmap')
        ->call('setTab', 'trends')
        ->assertSet('activeTab', 'trends')
        ->call('setTab', 'highrisk')
        ->assertSet('activeTab', 'highrisk');
});

test('reset filters clears all selections', function () {
    Role::create(['id' => 1, 'name' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => 1]);

    Livewire::actingAs($admin)
        ->test(DiseaseSurveillance::class)
        ->set('selectedDiseaseType', 'dengue')
        ->set('selectedPeriod', '90days')
        ->call('resetFilters')
        ->assertSet('selectedDiseaseType', null)
        ->assertSet('selectedPeriod', '30days');
});

test('disease statistics calculates correctly', function () {
    Role::create(['id' => 3, 'name' => 'Doctor']);
    $doctor = User::factory()->create(['role_id' => 3]);
    $barangay = Barangay::factory()->create();
    $patient = Patient::factory()->create();

    Disease::factory()->count(10)->create([
        'patient_id' => $patient->id,
        'disease_type' => 'dengue',
        'status' => 'confirmed',
        'barangay_id' => $barangay->id,
        'diagnosis_date' => now()->subDays(5),
        'reported_by' => $doctor->id,
    ]);

    $service = app(DiseaseSurveillanceService::class);
    $stats = $service->getStatistics('dengue', '30days');

    expect($stats)->toHaveKeys([
        'total_cases',
        'new_cases_7days',
        'high_risk_barangays',
        'trend_direction',
        'period',
    ])
        ->and($stats['total_cases'])->toBe(10)
        ->and($stats['period'])->toBe('30days');
});
