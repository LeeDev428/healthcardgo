<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('renders weekly appointment trend chart canvas', function () {
    // Create required role & user
    $role = Role::create([
        'name' => 'super_admin',
        'description' => 'Super Admin',
        'permissions' => [],
        'is_active' => true,
    ]);

    $user = User::factory()->create([
        'role_id' => $role->id,
        'status' => 'active',
    ]);

    actingAs($user);

    $response = get(route('admin.dashboard'));

    $response->assertSuccessful();
    $response->assertSee('weekly-appointments-chart');
    $response->assertSee('Weekly Appointment Trend');
});
