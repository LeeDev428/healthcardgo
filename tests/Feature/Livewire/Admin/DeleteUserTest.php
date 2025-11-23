<?php

declare(strict_types=1);

use App\Livewire\Admin\UsersManagement;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('deletes a user without related records', function () {
    // Ensure an admin role exists for acting user
    $adminRole = Role::firstOrCreate(['name' => 'super_admin'], [
        'description' => 'Super Admin',
        'permissions' => [],
        'is_active' => true,
    ]);

    $admin = User::factory()->create([
        'role_id' => $adminRole->id,
        'status' => 'active',
    ]);

    $target = User::factory()->create();

    /** @var \App\Models\User $admin */
    actingAs($admin);

    Livewire::test(UsersManagement::class)
        ->call('deleteUser', $target->id)
        ->assertDispatched('notify');

    expect(User::find($target->id))->toBeNull();
});
