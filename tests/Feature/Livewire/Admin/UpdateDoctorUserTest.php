<?php

declare(strict_types=1);

use App\Livewire\Admin\UsersManagement;
use App\Models\Doctor;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

it('updates doctor user details', function () {
    $doctorRole = Role::firstOrCreate(['name' => 'doctor'], [
        'description' => 'Doctor role',
        'permissions' => [],
        'is_active' => true,
    ]);

    /** @var User $user */
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'role_id' => $doctorRole->id,
        'status' => 'active',
    ]);

    Doctor::factory()->create([
        'user_id' => $user->id,
        'license_number' => 'PRC-7654321',
    ]);

    Livewire::test(UsersManagement::class)
        ->call('editUser', $user->id)
        ->set('form.name', 'Updated Name')
        ->call('updateUser')
        ->assertDispatched('notify');

    $user->refresh();
    expect($user->name)->toBe('Updated Name');
});

it('updates doctor profile license number and availability', function () {
    $doctorRole = Role::firstOrCreate(['name' => 'doctor'], [
        'description' => 'Doctor role',
        'permissions' => [],
        'is_active' => true,
    ]);

    $user = User::factory()->create([
        'role_id' => $doctorRole->id,
        'status' => 'active',
    ]);

    Doctor::factory()->create([
        'user_id' => $user->id,
        'license_number' => 'PRC-1111111',
        'is_available' => true,
    ]);

    Livewire::test(UsersManagement::class)
        ->call('editUser', $user->id)
        ->set('form.doctor.license_number', 'PRC-2222222')
        ->set('form.doctor.is_available', false)
        ->call('updateUser')
        ->assertDispatched('notify');

    $user->refresh();
    expect($user->doctor->license_number)->toBe('PRC-2222222');
    expect($user->doctor->is_available)->toBeFalse();
});
