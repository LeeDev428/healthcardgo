<?php

declare(strict_types=1);

use App\Livewire\Patient\MedicalRecordDetails;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Livewire\Livewire;

it('allows patient to view own medical record', function () {
    $user = User::factory()->create(['role_id' => 4]);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $service = Service::factory()->create();

    $record = MedicalRecord::create([
        'patient_id' => $patient->id,
        'doctor_id' => $user->id,
        'service_id' => $service->id,
        'record_type' => 'general',
        'category' => 'general',
        'title' => 'Visit Notes',
        'description' => 'Updated notes',
        'record_data' => [],
        'recorded_at' => now(),
        'created_by' => $user->id,
    ]);

    Livewire::actingAs($user)
        ->test(MedicalRecordDetails::class, ['record' => $record->id])
        ->assertOk()
        ->assertSee('Medical Record')
        ->assertSee('Visit Notes');
});

it('prevents viewing other patient record', function () {
    $user = User::factory()->create(['role_id' => 4]);
    $other = User::factory()->create(['role_id' => 4]);
    $patient = Patient::factory()->create(['user_id' => $other->id]);

    $record = MedicalRecord::create([
        'patient_id' => $patient->id,
        'record_type' => 'general',
        'category' => 'general',
        'title' => 'Private',
        'recorded_at' => now(),
        'created_by' => $other->id,
    ]);

    Livewire::actingAs($user)
        ->test(MedicalRecordDetails::class, ['record' => $record->id])
        ->assertStatus(404);
});
