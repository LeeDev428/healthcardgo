<?php

declare(strict_types=1);

use App\Models\Appointment;
use App\Models\User;
use App\Services\NotificationService;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('renders notifications.index and shows super admin copied notifications', function (): void {
    // Create a super admin and a doctor
    $superAdmin = User::factory()->create(['role_id' => 1, 'status' => 'active']);
    /** @var \App\Models\User $superAdmin */
    $doctor = User::factory()->create(['role_id' => 3, 'status' => 'active']);
    /** @var \App\Models\User $doctor */

    // Create an appointment assigned to doctor
    $appointment = Appointment::factory()->create(['doctor_id' => $doctor->id]);

    // Trigger a doctor notification which should be duplicated to super admin
    app(NotificationService::class)->sendPatientCheckedIn($appointment);

    // Visit notifications as super admin
    actingAs($superAdmin);
    $response = get(route('notifications.index'));

    $response->assertOk();
    $response->assertSee('Notifications');
    $response->assertSee('Patient Checked In');
});
