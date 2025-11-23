<?php

declare(strict_types=1);

use App\Livewire\Patient\Notifications as PatientNotifications;
use App\Models\Notification;
use App\Models\User;
use Livewire\Livewire;

it('renders patient notifications list and filters unread', function () {
    $user = User::factory()->create(['role_id' => 4]);

    // Create notifications for this user: 2 unread, 1 read
    $n1 = Notification::create([
        'user_id' => $user->id,
        'type' => 'appointment_reminder',
        'title' => 'Reminder',
        'message' => 'Upcoming appointment',
        'data' => [],
    ]);

    $n2 = Notification::create([
        'user_id' => $user->id,
        'type' => 'announcement',
        'title' => 'News',
        'message' => 'Important announcement',
        'data' => [],
    ]);

    $n3 = Notification::create([
        'user_id' => $user->id,
        'type' => 'feedback_request',
        'title' => 'Feedback',
        'message' => 'Tell us what you think',
        'data' => [],
        'read_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(PatientNotifications::class)
        ->assertSee('Notifications')
        ->assertSee('Reminder')
        ->assertSee('News')
        ->assertSee('Feedback')
        ->call('setFilter', 'unread')
        ->assertSee('Reminder')
        ->assertSee('News')
        ->assertDontSee('Feedback');
});

it('marks a notification as read and updates unread count', function () {
    $user = User::factory()->create(['role_id' => 4]);

    $n = Notification::create([
        'user_id' => $user->id,
        'type' => 'appointment_reminder',
        'title' => 'Reminder',
        'message' => 'Upcoming appointment',
        'data' => [],
    ]);

    Livewire::actingAs($user)
        ->test(PatientNotifications::class)
        ->assertSet('unreadCount', 1)
        ->call('markAsRead', $n->id)
        ->assertSet('unreadCount', 0);
});

it('marks all as read and resets unread count', function () {
    $user = User::factory()->create(['role_id' => 4]);

    foreach (range(1, 2) as $i) {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'announcement',
            'title' => 'T'.$i,
            'message' => 'M'.$i,
            'data' => [],
        ]);
    }

    Livewire::actingAs($user)
        ->test(PatientNotifications::class)
        ->assertSet('unreadCount', 2)
        ->call('markAllAsRead')
        ->assertSet('unreadCount', 0);
});
