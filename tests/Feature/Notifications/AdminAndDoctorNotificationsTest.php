<?php

declare(strict_types=1);

use App\Models\Appointment;
use App\Models\Feedback;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Services\NotificationService;

it('creates admin notifications for new and cancelled appointments', function () {
    $service = Service::factory()->create([
        'name' => 'HIV Screening',
        'category' => 'hiv_testing',
    ]);

    $patient = Patient::factory()->create();

    $admin1 = User::factory()->create(['role_id' => 2, 'admin_category' => 'hiv']);
    $admin2 = User::factory()->create(['role_id' => 2, 'admin_category' => 'hiv']);
    $otherAdmin = User::factory()->create(['role_id' => 2, 'admin_category' => 'pregnancy']);

    $appointment = Appointment::factory()->create([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'scheduled_at' => now()->addDay(),
        'status' => 'pending',
    ]);

    $serviceLayer = app(NotificationService::class);
    $serviceLayer->sendNewAppointmentToHealthcareAdmins($appointment);

    expect($admin1->notifications()->where('type', 'admin_new_appointment')->exists())->toBeTrue();
    expect($admin2->notifications()->where('type', 'admin_new_appointment')->exists())->toBeTrue();
    expect($otherAdmin->notifications()->where('type', 'admin_new_appointment')->exists())->toBeFalse();

    $serviceLayer->sendAppointmentCancellationToHealthcareAdmins($appointment, 'Patient request');

    expect($admin1->notifications()->where('type', 'admin_appointment_cancellation')->exists())->toBeTrue();
    expect($admin2->notifications()->where('type', 'admin_appointment_cancellation')->exists())->toBeTrue();
});

it('creates feedback received notification for super admins', function () {
    $superAdmin = User::factory()->create(['role_id' => 1]);
    $otherSuperAdmin = User::factory()->create(['role_id' => 1]);

    $patient = Patient::factory()->create();
    $serviceModel = Service::factory()->create();
    $appointment = Appointment::factory()->create([
        'patient_id' => $patient->id,
        'service_id' => $serviceModel->id,
        'scheduled_at' => now()->addDay(),
        'status' => 'completed',
    ]);

    $feedback = Feedback::query()->create([
        'patient_id' => $patient->id,
        'appointment_id' => $appointment->id,
        'overall_rating' => 4,
        'doctor_rating' => 4,
        'facility_rating' => 5,
        'wait_time_rating' => 3,
        'would_recommend' => true,
        'comments' => 'Great service.',
    ]);

    $serviceLayer = app(NotificationService::class);
    $serviceLayer->sendFeedbackReceivedToSuperAdmin($feedback);

    expect($superAdmin->notifications()->where('type', 'feedback_received')->exists())->toBeTrue();
    expect($otherSuperAdmin->notifications()->where('type', 'feedback_received')->exists())->toBeTrue();
});

it('creates doctor schedule and patient checked in notifications', function () {
    $doctor = User::factory()->create(['role_id' => 3]);
    $patient = Patient::factory()->create();
    $serviceModel = Service::factory()->create();

    $appointment = Appointment::factory()->create([
        'doctor_id' => $doctor->id,
        'patient_id' => $patient->id,
        'service_id' => $serviceModel->id,
        'scheduled_at' => now()->addHour(),
        'status' => 'pending',
    ]);

    $serviceLayer = app(NotificationService::class);
    $serviceLayer->sendDoctorDailySchedule($doctor);
    $serviceLayer->sendPatientCheckedIn($appointment);

    expect($doctor->notifications()->where('type', 'doctor_schedule')->exists())->toBeTrue();
    expect($doctor->notifications()->where('type', 'patient_checked_in')->exists())->toBeTrue();
});

it('creates urgent patient note and medical record request notifications', function () {
    $doctor = User::factory()->create(['role_id' => 3]);
    $patient = Patient::factory()->create();
    $medicalRecord = MedicalRecord::query()->create([
        'patient_id' => $patient->id,
        'recorded_at' => now(),
        'created_by' => $doctor->id,
        'doctor_id' => $doctor->id,
        'service_id' => Service::factory()->create()->id,
        'record_type' => 'note',
        'category' => 'general',
        'template_type' => 'default',
        'title' => 'Visit Note',
        'description' => 'Initial visit',
        'diagnosis' => 'N/A',
        'is_encrypted' => false,
    ]);

    $serviceLayer = app(NotificationService::class);
    $serviceLayer->sendUrgentPatientNote($doctor, $patient, 'Severe symptoms reported');
    $serviceLayer->sendMedicalRecordRequest($doctor, $medicalRecord);

    expect($doctor->notifications()->where('type', 'urgent_patient_note')->exists())->toBeTrue();
    expect($doctor->notifications()->where('type', 'medical_record_request')->exists())->toBeTrue();
});
