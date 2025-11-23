<?php

declare(strict_types=1);

use App\Livewire\Notifications\NotificationBell;
use App\Models\Notification;
use App\Models\User;
use Livewire\Livewire;

it('shows unread count and marks notification as read', function () {
    $user = User::factory()->create();

    // seed a notification
    $notification = Notification::create([
        'user_id' => $user->id,
        'type' => 'test',
        'title' => 'Test Title',
        'message' => 'Test Message',
        'data' => [],
    ]);

    expect($notification->isRead())->toBeFalse();

    Livewire::actingAs($user)
        ->test(NotificationBell::class)
        ->assertSet('unreadCount', 1)
        ->call('markAsRead', $notification->id)
        ->assertSet('unreadCount', 0);

    $notification->refresh();
    expect($notification->isRead())->toBeTrue();
});

it('can mark all notifications as read', function () {
    $user = User::factory()->create();

    foreach (range(1, 3) as $i) {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'test',
            'title' => 'Title '.$i,
            'message' => 'Message '.$i,
            'data' => [],
        ]);
    }

    expect(Notification::where('user_id', $user->id)->count())->toBe(3);

    Livewire::actingAs($user)
        ->test(NotificationBell::class)
        ->assertSet('unreadCount', 3)
        ->call('markAllAsRead')
        ->assertSet('unreadCount', 0);
});
