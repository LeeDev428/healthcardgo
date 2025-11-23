<?php

declare(strict_types=1);

use App\Livewire\Doctor\DoctorDashboard;
use App\Models\Doctor;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

it('renders doctor sidebar with expected navigation items', function () {
    $doctorRole = Role::firstOrCreate(['name' => 'doctor'], [
        'description' => 'Doctor role',
        'permissions' => [],
        'is_active' => true,
    ]);

    /** @var User $user */
    $user = User::factory()->create([
        'role_id' => $doctorRole->id,
        'status' => 'active',
    ]);

    Doctor::factory()->create([
        'user_id' => $user->id,
        'license_number' => 'PRC-9999999',
    ]);

    $this->actingAs($user);

    Livewire::test(DoctorDashboard::class)
        ->assertSee('Dashboard')
        ->assertSee('Appointments')
        ->assertSee('Create Medical Record')
        ->assertSee('Patients')
        ->assertSee('Notifications');
});
