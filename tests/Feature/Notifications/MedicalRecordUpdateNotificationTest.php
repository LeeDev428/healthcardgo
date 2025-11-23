<?php

declare(strict_types=1);

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Services\NotificationService;

it('sends medical_record_update notification to patient', function () {
    $user = User::factory()->create(['role_id' => 4]);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $service = Service::factory()->create();

    $record = MedicalRecord::create([
        'patient_id' => $patient->id,
        'doctor_id' => $user->id, // placeholder doctor, not used
        'service_id' => $service->id,
        'record_type' => 'general',
        'category' => 'general',
        'title' => 'Visit Notes',
        'description' => 'Updated notes',
        'record_data' => [],
        'recorded_at' => now(),
        'created_by' => $user->id,
    ]);

    app(NotificationService::class)->sendMedicalRecordUpdated($record);

    expect(\App\Models\Notification::where('user_id', $user->id)
        ->where('type', 'medical_record_update')
        ->where('title', 'Medical Record Updated')
        ->exists())->toBeTrue();
});
