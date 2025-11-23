<?php

declare(strict_types=1);

use App\Livewire\Admin\UsersManagement;
use App\Models\Doctor;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

it('creates a doctor user and profile', function () {
    // Ensure a doctor role exists (id or name based). Prefer name lookup.
    $doctorRole = Role::firstOrCreate(['name' => 'doctor'], [
        'description' => 'Doctor role',
        'permissions' => [],
        'is_active' => true,
    ]);

    $payload = [
        'name' => 'Dr. Test User',
        'email' => 'dr.test@example.com',
        'password' => 'Secret123!',
        'contact_number' => '09171234567',
        'role_id' => $doctorRole->id,
        'status' => 'active',
        'admin_category' => null,
        'doctor' => [
            'license_number' => 'PRC-1234567',
            'is_available' => true,
        ],
    ];

    Livewire::test(UsersManagement::class)
        ->set('form', $payload)
        ->call('storeUser')
        ->assertDispatched('notify');

    $user = User::where('email', 'dr.test@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->doctor)->not->toBeNull();
    expect($user->doctor->license_number)->toBe('PRC-1234567');
});

it('requires license number when role is doctor', function () {
    $doctorRole = Role::firstOrCreate(['name' => 'doctor'], [
        'description' => 'Doctor role',
        'permissions' => [],
        'is_active' => true,
    ]);

    $payload = [
        'name' => 'Dr. Missing License',
        'email' => 'dr.missing@example.com',
        'password' => 'Secret123!',
        'contact_number' => '09170000000',
        'role_id' => $doctorRole->id,
        'status' => 'active',
        'admin_category' => null,
        'doctor' => [
            'license_number' => '', // missing
            'is_available' => true,
        ],
    ];

    Livewire::test(UsersManagement::class)
        ->set('form', $payload)
        ->call('storeUser')
        ->assertHasErrors(['form.doctor.license_number']);

    expect(User::where('email', 'dr.missing@example.com')->exists())->toBeFalse();
    expect(Doctor::where('license_number', '')->exists())->toBeFalse();
});
