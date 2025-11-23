<?php

declare(strict_types=1);

use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

it('shows submitted ID preview in pending approvals', function () {
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    // Create super admin
    $admin = User::factory()->create([
        'role_id' => Role::where('name', 'super_admin')->first()->id,
        'status' => 'active',
    ]);

    // Fake storage and create a file
    Storage::fake('public');
    $path = 'patient-photos/test-id.jpg';
    Storage::disk('public')->put($path, 'fake image content');

    // Create pending patient user with photo_path as uploaded ID
    $patientUser = User::factory()->create([
        'role_id' => Role::where('name', 'patient')->first()->id,
        'status' => 'pending',
        'name' => 'John Pending',
        'email' => 'pending@example.com',
    ]);

    $barangay = \App\Models\Barangay::first() ?? \App\Models\Barangay::create(['name' => 'Test Barangay']);

    Patient::create([
        'user_id' => $patientUser->id,
        'date_of_birth' => '1990-01-01',
        'gender' => 'male',
        'barangay_id' => $barangay->id,
        'photo_path' => $path,
    ]);

    // Visit the pending approvals page
    $this->actingAs($admin)
        ->get(route('admin.approvals'))
        ->assertOk()
        ->assertSee('Submitted ID')
        ->assertSee(Storage::url($path));
});
