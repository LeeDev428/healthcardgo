<?php

declare(strict_types=1);

use App\Livewire\Notifications\NotificationCenter;
use App\Models\Notification;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('filters notifications by appointment type group', function (): void {
    /** @var \App\Models\User $user */
    $user = User::factory()->create(['role_id' => 1, 'status' => 'active']);
    actingAs($user);

    // Create mixed notifications
    Notification::create([
        'user_id' => $user->id,
        'type' => 'appointment_confirmation',
        'title' => 'Appointment Confirmed',
        'message' => 'Your appointment is confirmed.',
        'data' => [],
    ]);

    Notification::create([
        'user_id' => $user->id,
        'type' => 'feedback_request',
        'title' => 'Feedback Request',
        'message' => 'Please provide feedback.',
        'data' => [],
    ]);

    Livewire::test(NotificationCenter::class)
        ->set('typeFilter', 'appointments')
        ->assertSee('Appointment Confirmed')
        ->assertDontSee('Feedback Request');
});
