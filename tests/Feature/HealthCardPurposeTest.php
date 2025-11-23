<?php

use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Services\AppointmentService;
use Livewire\Livewire;
use App\Livewire\Patient\BookAppointment;

beforeEach(function () {
    // Seed barangays
    $this->artisan('db:seed', ['--class' => 'BarangaySeeder']);

    $this->healthCardSvc = Service::factory()->create([
        'name' => 'Health Card Services',
        'category' => 'health_card',
        'is_active' => true,
    ]);

    $this->otherService = Service::factory()->create([
        'name' => 'General Consultation',
        'category' => 'consultation',
        'is_active' => true,
    ]);

    $this->user = User::factory()->create([
        'role_id' => 4, // Patient role
        'status' => 'active',
    ]);

    $this->patient = Patient::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->appointmentService = app(AppointmentService::class);
});

test('health card appointment can be booked with food purpose', function () {
    $scheduledDate = now()->addDays(8)->setTime(10, 0);

    $appointment = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->healthCardSvc->id,
        'scheduled_at' => $scheduledDate,
        'health_card_purpose' => 'food',
        'notes' => 'Food handler health card',
        'fee' => 0,
    ]);

    expect($appointment->health_card_purpose)->toBe('food')
        ->and($appointment->service_id)->toBe($this->healthCardSvc->id);
});

test('health card appointment can be booked with non-food purpose', function () {
    $scheduledDate = now()->addDays(8)->setTime(10, 0);

    $appointment = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->healthCardSvc->id,
        'scheduled_at' => $scheduledDate,
        'health_card_purpose' => 'non_food',
        'notes' => 'Non-food handler health card',
        'fee' => 0,
    ]);

    expect($appointment->health_card_purpose)->toBe('non_food')
        ->and($appointment->service_id)->toBe($this->healthCardSvc->id);
});

test('non-health card appointment can be booked without purpose', function () {
    $scheduledDate = now()->addDays(8)->setTime(10, 0);

    $appointment = $this->appointmentService->bookAppointment([
        'patient_id' => $this->patient->id,
        'service_id' => $this->otherService->id,
        'scheduled_at' => $scheduledDate,
        'notes' => 'General consultation',
        'fee' => 0,
    ]);

    expect($appointment->health_card_purpose)->toBeNull()
        ->and($appointment->service_id)->toBe($this->otherService->id);
});

test('livewire component requires health card purpose for health card service', function () {
    $this->actingAs($this->user);

    $scheduledDate = now()->addDays(8);

    Livewire::test(BookAppointment::class)
        ->set('selectedService', $this->healthCardSvc->id)
        ->set('selectedDate', $scheduledDate->toDateString())
        ->set('selectedTime', '10:00')
        ->set('step', 3)
        ->call('bookAppointment')
        ->assertHasErrors(['healthCardPurpose']);
});

test('livewire component accepts food purpose for health card service', function () {
    $this->actingAs($this->user);

    $scheduledDate = now()->addDays(8);

    Livewire::test(BookAppointment::class)
        ->set('selectedService', $this->healthCardSvc->id)
        ->set('selectedDate', $scheduledDate->toDateString())
        ->set('selectedTime', '10:00')
        ->set('healthCardPurpose', 'food')
        ->set('notes', 'Test appointment')
        ->set('step', 3)
        ->call('bookAppointment')
        ->assertHasNoErrors()
        ->assertRedirect(route('patient.dashboard'));

    $this->assertDatabaseHas('appointments', [
        'patient_id' => $this->patient->id,
        'service_id' => $this->healthCardSvc->id,
        'health_card_purpose' => 'food',
    ]);
});

test('livewire component accepts non_food purpose for health card service', function () {
    $this->actingAs($this->user);

    $scheduledDate = now()->addDays(8);

    Livewire::test(BookAppointment::class)
        ->set('selectedService', $this->healthCardSvc->id)
        ->set('selectedDate', $scheduledDate->toDateString())
        ->set('selectedTime', '10:00')
        ->set('healthCardPurpose', 'non_food')
        ->set('notes', 'Test appointment')
        ->set('step', 3)
        ->call('bookAppointment')
        ->assertHasNoErrors()
        ->assertRedirect(route('patient.dashboard'));

    $this->assertDatabaseHas('appointments', [
        'patient_id' => $this->patient->id,
        'service_id' => $this->healthCardSvc->id,
        'health_card_purpose' => 'non_food',
    ]);
});

test('livewire component does not require purpose for non-health card service', function () {
    $this->actingAs($this->user);

    $scheduledDate = now()->addDays(8);

    Livewire::test(BookAppointment::class)
        ->set('selectedService', $this->otherService->id)
        ->set('selectedDate', $scheduledDate->toDateString())
        ->set('selectedTime', '10:00')
        ->set('notes', 'Test consultation')
        ->set('step', 3)
        ->call('bookAppointment')
        ->assertHasNoErrors()
        ->assertRedirect(route('patient.dashboard'));

    $this->assertDatabaseHas('appointments', [
        'patient_id' => $this->patient->id,
        'service_id' => $this->otherService->id,
        'health_card_purpose' => null,
    ]);
});

test('livewire component rejects invalid purpose values', function () {
    $this->actingAs($this->user);

    $scheduledDate = now()->addDays(8);

    Livewire::test(BookAppointment::class)
        ->set('selectedService', $this->healthCardSvc->id)
        ->set('selectedDate', $scheduledDate->toDateString())
        ->set('selectedTime', '10:00')
        ->set('healthCardPurpose', 'invalid_purpose')
        ->set('step', 3)
        ->call('bookAppointment')
        ->assertHasErrors(['healthCardPurpose']);
});
