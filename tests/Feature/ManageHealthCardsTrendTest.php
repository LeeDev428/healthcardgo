<?php

declare(strict_types=1);

use App\Livewire\Admin\ManageHealthCards;
use App\Models\Barangay;
use App\Models\HealthCard;
use App\Models\HistoricalHealthCardData;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

it('renders issuance trend chart with predictions', function () {
    // Create role for admin
    $role = Role::create(['name' => 'super_admin', 'description' => 'Super Admin']);

    // Create an admin user
    $admin = User::factory()->create(['role_id' => $role->id, 'status' => 'active']);

    // Ensure a Barangay exists for Patient factory FK
    Barangay::factory()->create();

    // Create sufficient historical data for predictions (15 months)
    for ($i = 0; $i < 15; $i++) {
        HistoricalHealthCardData::factory()->create([
            'record_date' => now()->subMonths(15 - $i)->startOfMonth(),
            'issued_count' => 20 + $i,
        ]);
    }

    // Create some recent health cards
    $patient = Patient::factory()->create();
    HealthCard::create([
        'patient_id' => $patient->id,
        'card_number' => 'HCTEST0001',
        'issue_date' => now()->subMonth()->startOfMonth(),
        'expiry_date' => now()->addYear()->startOfMonth(),
        'status' => 'active',
    ]);

    Livewire::actingAs($admin)
        ->test(ManageHealthCards::class)
        ->assertStatus(200)
        ->assertSee('Health Card Issuance Trend')
        ->assertViewHas('chartData', fn ($data) => is_array($data) && isset($data['labels']) && isset($data['actual']) && isset($data['predicted']))
        ->assertViewHas('chartData', fn ($data) => $data['has_predictions'] === true)
        ->assertViewHas('chartData', fn ($data) => count($data['labels']) > 0);
});

it('handles insufficient data for predictions gracefully', function () {
    $role = Role::create(['name' => 'super_admin', 'description' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => $role->id, 'status' => 'active']);
    Barangay::factory()->create();

    // Create only 5 months of data (insufficient)
    HistoricalHealthCardData::factory()->count(5)->create();

    Livewire::actingAs($admin)
        ->test(ManageHealthCards::class)
        ->assertStatus(200)
        ->assertViewHas('chartData', fn ($data) => $data['has_predictions'] === false)
        ->assertViewHas('chartData', fn ($data) => isset($data['message']) && str_contains($data['message'], 'Insufficient'));
});
