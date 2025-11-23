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
        $userId = Auth::id();
        
        $this->unreadCount = $notificationService->getUnreadCount($userId);
        $this->recentNotifications = $notificationService->getRecentNotifications($userId, 10);
    }

    public function markAsRead($notificationId): void
    {
        $notificationService = app(NotificationService::class);
        $notificationService->markAsRead((int) $notificationId);
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

    public function viewNotification($notificationId): void
    {
        $notification = \App\Models\Notification::find($notificationId);
        
        if (!$notification) {
            return;
        }

        if (!$notification->isRead()) {
            $this->markAsRead($notificationId);
        }

        // Redirect to notification page or appointment details
        $appointmentId = $notification->data['appointment_id'] ?? null;
        if ($appointmentId) {
            $this->redirect(route($this->notificationRoute));
        } else {
            $this->redirect(route($this->notificationRoute));
        }
    }

    public function render(): ViewContract
    {
        return view('livewire.notifications.notification-bell');
    }
}
