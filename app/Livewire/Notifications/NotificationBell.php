<?php

namespace App\Livewire\Notifications;

use App\Services\NotificationService;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    public string $notificationRoute = 'notifications.index';

    public $unreadCount = 0;

    public $recentNotifications = [];

    public $showDropdown = false;

    public function mount(?string $notificationRoute = null): void
    {
        if ($notificationRoute !== null) {
            $this->notificationRoute = $notificationRoute;
        }
        $this->loadNotifications();
    }

    #[On('notification-created')]
    #[On('notification-read')]
    #[On('notifications-cleared')]
    public function loadNotifications(): void
    {
        $notificationService = app(NotificationService::class);
        $this->unreadCount = $notificationService->getUnreadCount(Auth::id());
        $this->recentNotifications = $notificationService->getRecentNotifications(Auth::id(), 5);
    }

    public function toggleDropdown(): void
    {
        $this->showDropdown = ! $this->showDropdown;
    }

    public function markAsRead($notificationId): void
    {
        $id = (int) $notificationId;
        $notificationService = app(NotificationService::class);
        $notificationService->markAsRead($id);

        $this->loadNotifications();
        $this->dispatch('notification-read');
    }

    public function markAllAsRead(): void
    {
        $notificationService = app(NotificationService::class);
        $notificationService->markAllAsRead((int) Auth::id());

        $this->loadNotifications();
        $this->dispatch('notifications-cleared');
    }

    public function render(): ViewContract
    {
        return view('livewire.notifications.notification-bell');
    }
}
