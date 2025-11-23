<?php

declare(strict_types=1);

use App\Livewire\Doctor\DoctorDashboard;
use App\Models\Doctor;
use App\Models\User;
use Livewire\Livewire;

it('renders dashboard for doctor with profile', function () {
    $user = User::factory()->create([
        'role_id' => 3, // assuming role_id 3 = doctor based on route logic
        'status' => 'active',
    ]);

    // Create linked doctor profile
    Doctor::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    Livewire::test(DoctorDashboard::class)
        ->assertStatus(200)
        ->assertSee('Welcome, Dr.')
        ->assertSet('doctorMissing', false);
});

it('shows missing profile callout when doctor record absent', function () {
    $user = User::factory()->create([
        'role_id' => 3,
        'status' => 'active',
    ]);

    // Intentionally NOT creating Doctor profile
    $this->actingAs($user);

    Livewire::test(DoctorDashboard::class)
        ->assertStatus(200)
        ->assertSet('doctorMissing', true)
        ->assertSee('Doctor Profile Missing');
});
