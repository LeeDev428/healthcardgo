<?php

declare(strict_types=1);

use App\Models\Notification;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Services\AppointmentService;
use Carbon\Carbon;

use function Pest\Laravel\actingAs;

it('notifies super admins when a new appointment is booked', function (): void {
    /** @var User $superAdmin */
    $superAdmin = User::factory()->create(['role_id' => 1, 'status' => 'active']);

    /** @var Patient $patient */
    $patient = Patient::factory()->create();

    /** @var Service $service */
    $service = Service::factory()->create(['category' => 'consultation']);

    actingAs($patient->user);

    $scheduledAt = Carbon::now()->addDays(8)->setTime(10, 0); // within booking window

    $serviceLayer = app(AppointmentService::class);
    $appointment = $serviceLayer->bookAppointment([
        'patient_id' => $patient->id,
        'doctor_id' => null,
        'service_id' => $service->id,
        'scheduled_at' => $scheduledAt,
        'notes' => 'Test notes',
        'fee' => 0,
    ]);

    expect($appointment->exists())->toBeTrue();

    // Super admin should have a new_appointment notification
    $exists = Notification::where('user_id', $superAdmin->id)
        ->where('type', 'new_appointment')
        ->exists();

    expect($exists)->toBeTrue();
});
