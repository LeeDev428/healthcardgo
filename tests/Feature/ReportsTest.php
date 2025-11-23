<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

function createRole(string $name): Role
{
    return Role::query()->create([
        'name' => $name,
        'description' => ucfirst(str_replace('_', ' ', $name)),
        'permissions' => [],
        'is_active' => true,
    ]);
}

it('super admin can view reports page', function () {
    $role = createRole('super_admin');

    /** @var User $user */
    $user = User::factory()->create([
        'role_id' => $role->id,
        'status' => 'active',
    ]);

    actingAs($user);

    get(route('admin.reports'))
        ->assertSuccessful()
        ->assertSee('Reports');
});

it('healthcare admin can view reports page', function () {
    $role = createRole('healthcare_admin');

    /** @var User $user */
    $user = User::factory()->create([
        'role_id' => $role->id,
        'status' => 'active',
    ]);

    actingAs($user);

    get(route('healthcare_admin.reports'))
        ->assertSuccessful()
        ->assertSee('Reports');
});

it('super admin can print PDF', function () {
    $role = createRole('super_admin');

    /** @var User $user */
    $user = User::factory()->create([
        'role_id' => $role->id,
        'status' => 'active',
    ]);

    actingAs($user);

    $response = get(route('admin.reports.print', [
        'type' => 'appointments',
        'from' => now()->startOfMonth()->toDateString(),
        'to' => now()->toDateString(),
    ]));

    $response->assertSuccessful();
    expect($response->headers->get('content-type'))
        ->toContain('application/pdf');
});
