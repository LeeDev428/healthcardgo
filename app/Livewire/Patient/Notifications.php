<?php

namespace App\Livewire\Patient;

use App\Models\Notification as NotificationModel;
use App\Services\NotificationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.patient')]
#[Title('Notifications')]
class Notifications extends Component
{
    use WithPagination;

    public string $filter = 'all'; // all|unread|read

    public int $unreadCount = 0;

    protected $queryString = [
        'filter' => ['except' => 'all'],
    ];

    public function mount(): void
    {
        $this->loadUnreadCount();
    }

    public function setFilter(string $filter): void
    {
        $this->filter = in_array($filter, ['all', 'unread', 'read'], true) ? $filter : 'all';
        $this->resetPage();
    }

    public function markAsRead(int $id): void
    {
        app(NotificationService::class)->markAsRead($id);
        $this->loadUnreadCount();
        // Keep the current page consistent after action
    }

    public function markAllAsRead(): void
    {
        app(NotificationService::class)->markAllAsRead((int) Auth::id());
        $this->loadUnreadCount();
        $this->resetPage();
    }

    public function loadUnreadCount(): void
    {
        $this->unreadCount = app(NotificationService::class)->getUnreadCount((int) Auth::id());
    }

    protected function notificationsQuery()
    {
        $query = NotificationModel::query()
            ->where('user_id', (int) Auth::id())
            ->orderByDesc('created_at');

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        return $query;
    }

    public function render(): ViewContract
    {
        /** @var LengthAwarePaginator $notifications */
        $notifications = $this->notificationsQuery()->paginate(10);

        return view('livewire.patient.notifications', [
            'notifications' => $notifications,
        ]);
    }
}
