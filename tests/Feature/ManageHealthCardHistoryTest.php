<?php

declare(strict_types=1);

use App\Livewire\Admin\ManageHealthCardHistory;
use App\Models\HistoricalHealthCardData;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

it('renders successfully for admin', function () {
    $role = Role::create(['name' => 'super_admin', 'description' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => $role->id, 'status' => 'active']);

    Livewire::actingAs($admin)
        ->test(ManageHealthCardHistory::class)
        ->assertStatus(200)
        ->assertSee('Health Card Historical Data')
        ->assertSee('Prediction Readiness Status');
});

it('can add historical data', function () {
    $role = Role::create(['name' => 'super_admin', 'description' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => $role->id, 'status' => 'active']);

    Livewire::actingAs($admin)
        ->test(ManageHealthCardHistory::class)
        ->call('openForm')
        ->assertSet('showForm', true)
        ->set('recordDate', '2024-01-01')
        ->set('issuedCount', 25)
        ->set('notes', 'Test data for January 2024')
        ->call('save')
        ->assertSet('showForm', false);

    $this->assertDatabaseHas('historical_health_card_data', [
        'issued_count' => 25,
        'notes' => 'Test data for January 2024',
        'data_source' => 'manual',
    ]);
});

it('can edit historical data', function () {
    $role = Role::create(['name' => 'super_admin', 'description' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => $role->id, 'status' => 'active']);

    $data = HistoricalHealthCardData::factory()->create([
        'issued_count' => 10,
    ]);

    Livewire::actingAs($admin)
        ->test(ManageHealthCardHistory::class)
        ->call('edit', $data->id)
        ->assertSet('showForm', true)
        ->assertSet('editingId', $data->id)
        ->assertSet('issuedCount', 10)
        ->set('issuedCount', 15)
        ->call('save')
        ->assertSet('showForm', false);

    $this->assertDatabaseHas('historical_health_card_data', [
        'id' => $data->id,
        'issued_count' => 15,
    ]);
});

it('can delete historical data', function () {
    $role = Role::create(['name' => 'super_admin', 'description' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => $role->id, 'status' => 'active']);

    $data = HistoricalHealthCardData::factory()->create();

    Livewire::actingAs($admin)
        ->test(ManageHealthCardHistory::class)
        ->call('delete', $data->id);

    $this->assertDatabaseMissing('historical_health_card_data', [
        'id' => $data->id,
    ]);
});

it('shows data status correctly', function () {
    $role = Role::create(['name' => 'super_admin', 'description' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => $role->id, 'status' => 'active']);

    // Create 8 months of data with unique dates (insufficient)
    for ($i = 0; $i < 8; $i++) {
        HistoricalHealthCardData::factory()->create([
            'record_date' => now()->subMonths(8 - $i)->startOfMonth(),
        ]);
    }

    $component = Livewire::actingAs($admin)
        ->test(ManageHealthCardHistory::class);

    $dataStatus = $component->get('dataStatus');

    expect($dataStatus['sufficient'])->toBeFalse()
        ->and($dataStatus['data_points'])->toBe(8)
        ->and($dataStatus['required'])->toBe(12)
        ->and($dataStatus['missing'])->toBe(4);
});

it('filters data by date range', function () {
    $role = Role::create(['name' => 'super_admin', 'description' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => $role->id, 'status' => 'active']);

    // Create data for different months
    HistoricalHealthCardData::factory()->create(['record_date' => '2024-01-01']);
    HistoricalHealthCardData::factory()->create(['record_date' => '2024-06-01']);
    HistoricalHealthCardData::factory()->create(['record_date' => '2024-12-01']);

    $component = Livewire::actingAs($admin)
        ->test(ManageHealthCardHistory::class)
        ->set('filterStartDate', '2024-05-01')
        ->set('filterEndDate', '2024-12-31');

    $data = $component->get('historicalData');
    expect($data->count())->toBe(2); // Only June and December
});
