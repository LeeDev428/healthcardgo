<?php

declare(strict_types=1);

use App\Enums\AdminCategoryEnum;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Artisan;

use function Pest\Laravel\actingAs;

test('hiv admin can see appointment trends chart', function () {
    // Ensure roles exist
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $adminRoleId = \App\Models\Role::where('name', 'healthcare_admin')->first()->id;

    $hivAdmin = User::factory()->create([
        'role_id' => $adminRoleId,
        'admin_category' => AdminCategoryEnum::HIV,
        'status' => 'active',
    ]);

    // Create HIV testing service
    $service = Service::factory()->create([
        'category' => 'hiv_testing',
        'name' => 'HIV Testing Service',
    ]);

    // Create some historical appointments throughout the year
    $patient = Patient::factory()->create();
    $currentYear = now()->year;

    // Create completed appointments for each month
    for ($month = 1; $month <= 12; $month++) {
        Appointment::factory()->create([
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'status' => 'completed',
            'scheduled_at' => now()->setYear($currentYear)->setMonth($month)->startOfMonth(),
        ]);
    }

    // Create no_show appointments
    for ($month = 1; $month <= 6; $month++) {
        Appointment::factory()->create([
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'status' => 'no_show',
            'scheduled_at' => now()->setYear($currentYear)->setMonth($month)->startOfMonth(),
        ]);
    }

    // Create cancelled appointments
    for ($month = 1; $month <= 4; $month++) {
        Appointment::factory()->create([
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'status' => 'cancelled',
            'scheduled_at' => now()->setYear($currentYear)->setMonth($month)->startOfMonth(),
        ]);
    }

    actingAs($hivAdmin)
        ->get(route('healthcare_admin.dashboard'))
        ->assertOk()
        ->assertSee('HIV Testing Appointments', false)
        ->assertSee('Trend', false)
        ->assertSee('Prediction Analysis', false)
        ->assertSee('No Show')
        ->assertSee('Completed')
        ->assertSee('Cancelled')
        ->assertSee('appointmentTrendsChart');
});

test('non-hiv admin does not see hiv appointment trends chart', function () {
    // Ensure roles exist
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $adminRoleId = \App\Models\Role::where('name', 'healthcare_admin')->first()->id;

    $healthcareAdmin = User::factory()->create([
        'role_id' => $adminRoleId,
        'admin_category' => AdminCategoryEnum::HealthCard, // Different category
        'status' => 'active',
    ]);

    actingAs($healthcareAdmin)
        ->get(route('healthcare_admin.dashboard'))
        ->assertOk()
        ->assertDontSee('HIV Testing Appointments: Trend & Prediction Analysis')
        ->assertSee('Health Card Appointments'); // Should see health card instead
});

test('hiv appointment trends include predictions for next two months', function () {
    // Ensure roles exist
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $adminRoleId = \App\Models\Role::where('name', 'healthcare_admin')->first()->id;

    $hivAdmin = User::factory()->create([
        'role_id' => $adminRoleId,
        'admin_category' => AdminCategoryEnum::HIV,
        'status' => 'active',
    ]);

    // Create HIV testing service
    $service = Service::factory()->create([
        'category' => 'hiv_testing',
        'name' => 'HIV Testing Service',
    ]);

    $patient = Patient::factory()->create();

    // Create appointments for the last 3 months
    for ($i = 3; $i >= 1; $i--) {
        Appointment::factory()->create([
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'status' => 'completed',
            'scheduled_at' => now()->subMonths($i)->startOfMonth(),
        ]);
    }

    $response = actingAs($hivAdmin)->get(route('healthcare_admin.dashboard'));

    // Get the chart data from the response
    $chartDataPattern = '/data-chart="([^"]+)"/';
    preg_match($chartDataPattern, $response->content(), $matches);

    expect($matches)->toHaveCount(2);

    $chartData = json_decode(html_entity_decode($matches[1]), true);

    expect($chartData)->toHaveKey('labels')
        ->and($chartData)->toHaveKey('datasets')
        ->and($chartData['labels'])->toHaveCount(14) // 12 historical + 2 predictions
        ->and($chartData['datasets'])->toHaveCount(3); // No Show, Completed, Cancelled

    // Check that last 2 labels contain "(Predicted)"
    $labels = $chartData['labels'];
    expect(end($labels))->toContain('(Predicted)')
        ->and($labels[count($labels) - 2])->toContain('(Predicted)');
});

test('hiv appointment trends display correct data format', function () {
    // Ensure roles exist
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $adminRoleId = \App\Models\Role::where('name', 'healthcare_admin')->first()->id;

    $hivAdmin = User::factory()->create([
        'role_id' => $adminRoleId,
        'admin_category' => AdminCategoryEnum::HIV,
        'status' => 'active',
    ]);

    // Create HIV testing service
    $service = Service::factory()->create([
        'category' => 'hiv_testing',
        'name' => 'HIV Testing Service',
    ]);

    $patient = Patient::factory()->create();
    $currentMonth = now()->startOfMonth();

    // Create specific appointments
    Appointment::factory()->create([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'status' => 'no_show',
        'scheduled_at' => $currentMonth->copy()->subMonth(),
    ]);

    Appointment::factory()->count(2)->create([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'status' => 'completed',
        'scheduled_at' => $currentMonth->copy()->subMonth(),
    ]);

    $response = actingAs($hivAdmin)->get(route('healthcare_admin.dashboard'));

    // Get chart data
    $chartDataPattern = '/data-chart="([^"]+)"/';
    preg_match($chartDataPattern, $response->content(), $matches);

    $chartData = json_decode(html_entity_decode($matches[1]), true);

    // Verify datasets structure
    expect($chartData['datasets'][0])->toHaveKey('label')
        ->and($chartData['datasets'][0])->toHaveKey('data')
        ->and($chartData['datasets'][0])->toHaveKey('borderColor')
        ->and($chartData['datasets'][0]['label'])->toBe('No Show')
        ->and($chartData['datasets'][1]['label'])->toBe('Completed')
        ->and($chartData['datasets'][2]['label'])->toBe('Cancelled');
});
