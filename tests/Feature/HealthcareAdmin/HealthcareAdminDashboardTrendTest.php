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

test('healthcard admin can see appointment trends chart', function () {
    // Ensure roles exist
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $adminRoleId = \App\Models\Role::where('name', 'healthcare_admin')->first()->id;

    $healthcareAdmin = User::factory()->create([
        'role_id' => $adminRoleId,
        'admin_category' => AdminCategoryEnum::HealthCard,
        'status' => 'active',
    ]);

    // Create health card service
    $service = Service::factory()->create([
        'category' => 'health_card',
        'name' => 'Health Card Service',
    ]);

    // Create some historical appointments throughout the year (12 months)
    $patient = Patient::factory()->create();
    $currentYear = now()->year;

    // Create completed appointments for each month of the year
    for ($month = 1; $month <= 12; $month++) {
        Appointment::factory()->create([
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'status' => 'completed',
            'scheduled_at' => now()->setYear($currentYear)->setMonth($month)->startOfMonth(),
        ]);
    }

    // Create no_show appointments for some months
    for ($month = 1; $month <= 6; $month++) {
        Appointment::factory()->create([
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'status' => 'no_show',
            'scheduled_at' => now()->setYear($currentYear)->setMonth($month)->startOfMonth(),
        ]);
    }

    // Create cancelled appointments for some months
    for ($month = 1; $month <= 4; $month++) {
        Appointment::factory()->create([
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'status' => 'cancelled',
            'scheduled_at' => now()->setYear($currentYear)->setMonth($month)->startOfMonth(),
        ]);
    }

    actingAs($healthcareAdmin)
        ->get(route('healthcare_admin.dashboard'))
        ->assertOk()
        ->assertSee('Health Card Appointments', false) // Don't escape
        ->assertSee('Trend', false)
        ->assertSee('Prediction Analysis', false)
        ->assertSee('No Show')
        ->assertSee('Completed')
        ->assertSee('Cancelled')
        ->assertSee('appointmentTrendsChart');
});

test('non-healthcard admin does not see appointment trends chart', function () {
    // Ensure roles exist
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $adminRoleId = \App\Models\Role::where('name', 'healthcare_admin')->first()->id;

    $healthcareAdmin = User::factory()->create([
        'role_id' => $adminRoleId,
        'admin_category' => AdminCategoryEnum::MedicalRecords, // Different category without trends
        'status' => 'active',
    ]);

    actingAs($healthcareAdmin)
        ->get(route('healthcare_admin.dashboard'))
        ->assertOk()
        ->assertDontSee('Health Card Appointments: Trend & Prediction Analysis')
        ->assertDontSee('HIV Testing Appointments: Trend & Prediction Analysis')
        ->assertDontSee('Pregnancy Care Appointments: Trend & Prediction Analysis')
        ->assertDontSee('appointmentTrendsChart');
});

test('appointment trends include prediction for next month', function () {
    // Ensure roles exist
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $adminRoleId = \App\Models\Role::where('name', 'healthcare_admin')->first()->id;

    $healthcareAdmin = User::factory()->create([
        'role_id' => $adminRoleId,
        'admin_category' => AdminCategoryEnum::HealthCard,
        'status' => 'active',
    ]);

    $service = Service::factory()->create([
        'category' => 'health_card',
    ]);

    $patient = Patient::factory()->create();
    $currentYear = now()->year;

    // Create consistent trend of completed appointments for 12 months
    for ($month = 1; $month <= 12; $month++) {
        Appointment::factory()->create([
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'status' => 'completed',
            'scheduled_at' => now()->setYear($currentYear)->setMonth($month)->startOfMonth(),
        ]);
    }

    $response = actingAs($healthcareAdmin)
        ->get(route('healthcare_admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Predicted'); // Should show predicted label
});

test('appointment trends display correct data format', function () {
    // Ensure roles exist
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $adminRoleId = \App\Models\Role::where('name', 'healthcare_admin')->first()->id;

    $healthcareAdmin = User::factory()->create([
        'role_id' => $adminRoleId,
        'admin_category' => AdminCategoryEnum::HealthCard,
        'status' => 'active',
    ]);

    $service = Service::factory()->create([
        'category' => 'health_card',
    ]);

    $patient = Patient::factory()->create();

    // Create appointments
    Appointment::factory()->create([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'status' => 'completed',
        'scheduled_at' => now()->subMonths(1),
    ]);

    Appointment::factory()->create([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'status' => 'no_show',
        'scheduled_at' => now()->subMonths(1),
    ]);

    Appointment::factory()->create([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'status' => 'cancelled',
        'scheduled_at' => now()->subMonths(1),
    ]);

    $response = actingAs($healthcareAdmin)
        ->get(route('healthcare_admin.dashboard'));

    $response->assertOk();

    // Check for summary cards
    $response->assertSee('No Show');
    $response->assertSee('Completed');
    $response->assertSee('Cancelled');

    // Check for chart info
    $response->assertSee('Analysis Insight');
    $response->assertSee('historical trends');
});
