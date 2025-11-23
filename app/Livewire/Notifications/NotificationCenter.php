<?php

namespace App\Livewire\Notifications;

use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Notifications')]
class NotificationCenter extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, unread, read

    public string $typeFilter = 'all'; // all, appointments, admin, doctor, registration, healthcard, feedback, records, other

    public function markAsRead($notificationId)
    {
        $notificationService = app(NotificationService::class);
        $notificationService->markAsRead($notificationId);

        $this->dispatch('notification-read');
    }

    public function markAllAsRead()
    {
        $notificationService = app(NotificationService::class);
        $notificationService->markAllAsRead(Auth::id());

        session()->flash('success', 'All notifications marked as read.');
        $this->dispatch('notifications-cleared');
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function setTypeFilter($value): void
    {
        $this->typeFilter = $value;
        $this->resetPage();
    }

    #[On('notification-created')]
    public function refreshNotifications()
    {
        // This will trigger a re-render
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $query = $user->notifications()->orderBy('created_at', 'desc');

        if ($this->filter === 'unread') {
            $query->unread();
        } elseif ($this->filter === 'read') {
            $query->read();
        }

        // Map type filter to underlying notification types
        $map = [
            'appointments' => ['appointment_confirmation', 'appointment_reminder', 'appointment_cancellation'],
            'admin' => ['admin_new_appointment', 'admin_appointment_cancellation', 'new_appointment'],
            'doctor' => ['patient_checked_in', 'doctor_schedule', 'urgent_patient_note'],
            'registration' => ['registration_approval', 'registration_rejection', 'new_patient_registration'],
            'healthcard' => ['announcement', 'urgent_note'],
            'feedback' => ['feedback_request', 'feedback_received'],
            'records' => ['medical_record_update', 'medical_record_request'],
        ];

        if ($this->typeFilter !== 'all' && isset($map[$this->typeFilter])) {
            $query->whereIn('type', $map[$this->typeFilter]);
        }

        $notifications = $query->paginate(20);
        $unreadCount = $user->notifications()->unread()->count();

        return view('livewire.notifications.notification-center', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'typeFilter' => $this->typeFilter,
        ]);
    }
}
