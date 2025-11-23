<?php

declare(strict_types=1);

use App\Models\Barangay;
use App\Models\Disease;
use App\Models\Role;
use App\Models\User;
use App\Services\DiseaseSurveillanceService;
use Livewire\Livewire;

it('shows available years in the Time Period dropdown and filters by year', function () {
    // Setup roles and an admin user
    Role::create(['id' => 1, 'name' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => 1]);

    $barangay = Barangay::factory()->create();

    // Seed confirmed diseases across different years
    Disease::factory()->confirmed()->count(2)->create([
        'barangay_id' => $barangay->id,
        'diagnosis_date' => '2022-05-10',
    ]);

    Disease::factory()->confirmed()->create([
        'barangay_id' => $barangay->id,
        'diagnosis_date' => '2023-07-11',
    ]);

    // Service-level assertions
    $service = app(DiseaseSurveillanceService::class);
    $years = $service->getAvailableYears();

    expect($years)->toContain(2022, 2023);

    $stats2022 = $service->getStatistics(null, 'year:2022');
    expect($stats2022['total_cases'])->toBe(2);

    // Component renders and can set the period to a specific year
    Livewire::actingAs($admin)
        ->test(\App\Livewire\Admin\DiseaseSurveillance::class)
        ->set('selectedPeriod', 'year:2022')
        ->assertSet('selectedPeriod', 'year:2022');
});
