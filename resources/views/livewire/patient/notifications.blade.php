<div class="space-y-6" wire:init="loadUnreadCount">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Notifications</flux:heading>
            <flux:text class="mt-2">Stay updated with your appointments, health card, and system alerts.</flux:text>
        </div>

        @if($unreadCount > 0)
            <flux:button icon="check-circle" wire:click="markAllAsRead" variant="ghost" size="sm">
                Mark All as Read
            </flux:button>
        @endif
    </div>

    <!-- Flash Messages -->
    @if(session()->has('success'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('success') }}
        </flux:callout>
    @endif

    <!-- Filter Tabs -->
    <div class="flex gap-2 border-b border-zinc-200 dark:border-zinc-700">
        <button
            wire:click="setFilter('all')"
            class="px-4 py-2 text-sm font-medium transition-colors {{ $filter === 'all' ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
            All
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-zinc-100 dark:bg-zinc-800">
                {{ $notifications->total() }}
            </span>
        </button>

        <button
            wire:click="setFilter('unread')"
            class="px-4 py-2 text-sm font-medium transition-colors {{ $filter === 'unread' ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
            Unread
            @if($unreadCount > 0)
                <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400">
                    {{ $unreadCount }}
                </span>
            @endif
        </button>

        <button
            wire:click="setFilter('read')"
            class="px-4 py-2 text-sm font-medium transition-colors {{ $filter === 'read' ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
            Read
        </button>
    </div>

    <!-- Notifications List -->
    @if($notifications->isEmpty())
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-zinc-100 dark:bg-zinc-800 mb-4">
                <flux:icon name="bell-slash" size="lg" class="text-zinc-400" />
            </div>

            <flux:heading size="lg" class="mb-2">No Notifications</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                @if($filter === 'unread')
                    You're all caught up! No unread notifications.
                @elseif($filter === 'read')
                    No read notifications found.
                @else
                    You don't have any notifications yet.
                @endif
            </flux:text>
        </div>
    @else
        <div class="space-y-2">
            @foreach($notifications as $notification)
                <div
                    wire:key="notification-{{ $notification->id }}"
                    class="bg-white dark:bg-zinc-800 rounded-lg border {{ $notification->isRead() ? 'border-zinc-200 dark:border-zinc-700' : 'border-blue-200 dark:border-blue-800 bg-blue-50/50 dark:bg-blue-900/10' }} p-4 hover:shadow-md transition-shadow">

                    <div class="flex gap-4">
                        <!-- Icon -->
                        <div class="shrink-0">
                            <div class="w-10 h-10 rounded-full {{ $notification->isRead() ? 'bg-zinc-100 dark:bg-zinc-700' : 'bg-blue-100 dark:bg-blue-900' }} flex items-center justify-center">
                                @switch($notification->type)
                                    @case('appointment_confirmation')
                                    @case('appointment_reminder')
                                        <flux:icon name="calendar" class="{{ $notification->isRead() ? 'text-zinc-600 dark:text-zinc-400' : 'text-blue-600 dark:text-blue-400' }}" />
                                        @break
                                    @case('appointment_cancelled')
                                        <flux:icon name="x-circle" class="text-red-600 dark:text-red-400" />
                                        @break
                                    @case('admin_new_appointment')
                                        <flux:icon name="calendar" class="text-blue-600 dark:text-blue-400" />
                                        @break
                                    @case('admin_appointment_cancellation')
                                        <flux:icon name="x-circle" class="text-red-600 dark:text-red-400" />
                                        @break
                                    @case('feedback_received')
                                        <flux:icon name="chat-bubble-left-right" class="text-purple-600 dark:text-purple-400" />
                                        @break
                                    @case('doctor_schedule')
                                        <flux:icon name="calendar" class="text-emerald-600 dark:text-emerald-400" />
                                        @break
                                    @case('patient_checked_in')
                                        <flux:icon name="user" class="text-amber-600 dark:text-amber-400" />
                                        @break
                                    @case('urgent_patient_note')
                                        <flux:icon name="exclamation-triangle" class="text-amber-600 dark:text-amber-400" />
                                        @break
                                    @case('medical_record_request')
                                        <flux:icon name="clipboard-document" class="text-sky-600 dark:text-sky-400" />
                                        @break
                                    @case('account_approved')
                                        <flux:icon name="check-circle" class="text-green-600 dark:text-green-400" />
                                        @break
                                    @case('account_rejected')
                                        <flux:icon name="x-circle" class="text-red-600 dark:text-red-400" />
                                        @break
                                    @case('feedback_request')
                                        <flux:icon name="chat-bubble-left-right" class="text-purple-600 dark:text-purple-400" />
                                        @break
                                    @case('healthcard_issued')
                                    @case('healthcard_expiry')
                                        <flux:icon name="identification" class="text-blue-600 dark:text-blue-400" />
                                        @break
                                    @case('new_appointment')
                                    @case('new_registration')
                                        <flux:icon name="bell" class="text-orange-600 dark:text-orange-400" />
                                        @break
                                    @default
                                        <flux:icon name="bell" class="{{ $notification->isRead() ? 'text-zinc-600 dark:text-zinc-400' : 'text-blue-600 dark:text-blue-400' }}" />
                                @endswitch
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <flux:heading size="sm">{{ $notification->title }}</flux:heading>
                                    <flux:text size="sm" class="mt-1 text-zinc-600 dark:text-zinc-400">
                                        {{ $notification->message }}
                                    </flux:text>

                                    <!-- Action Buttons -->
                                    @if($notification->type === 'appointment_reminder' || $notification->type === 'appointment_confirmation')
                                        <div class="mt-3">
                                            <flux:button :href="route('patient.appointments.list')" variant="primary" color="blue" size="sm" wire:navigate>
                                                View Appointment
                                            </flux:button>
                                        </div>
                                    @elseif($notification->type === 'feedback_request')
                                        <div class="mt-3">
                                            <flux:button :href="route('patient.feedback')" variant="primary" size="sm" wire:navigate>
                                                Provide Feedback
                                            </flux:button>
                                        </div>
                                    @elseif($notification->type === 'medical_record_update')
                                        <div class="mt-3">
                                            <flux:button :href="route('patient.records.show', ['record' => $notification->data['medical_record_id'] ?? null])" variant="primary" size="sm" wire:navigate>
                                                View Record
                                            </flux:button>
                                        </div>
                                    @elseif($notification->type === 'healthcard_issued')
                                        <div class="mt-3">
                                            <flux:button :href="route('patient.health-card')" variant="primary" size="sm" wire:navigate>
                                                View Health Card
                                            </flux:button>
                                        </div>
                                    @elseif($notification->type === 'new_appointment' || $notification->type === 'new_registration')
                                        <div class="mt-3">
                                            @if($notification->type === 'new_registration')
                                                <flux:button :href="route('admin.approvals')" variant="primary" size="sm" wire:navigate>
                                                    Review Registration
                                                </flux:button>
                                            @endif
                                        </div>
                                    @endif

                                    <flux:text size="xs" class="mt-2 text-zinc-500 dark:text-zinc-500">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </flux:text>
                                </div>

                                <!-- Mark as Read Button -->
                                @if(!$notification->isRead())
                                    <button
                                        wire:click="markAsRead({{ $notification->id }})"
                                        class="shrink-0 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                                        title="Mark as read">
                                        <flux:icon name="check" size="sm" />
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
