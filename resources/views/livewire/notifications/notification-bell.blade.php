<div class="relative" x-data="{ open: @entangle('showDropdown') }">
    <!-- Bell Button -->
    <button
        @click="open = !open"
        class="relative p-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">
        <flux:icon name="bell" size="md" />

        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div
        x-show="open"
        @click.outside="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-96 bg-white dark:bg-zinc-800 rounded-lg shadow-xl border border-zinc-200 dark:border-zinc-700 z-50"
        style="display: none;">

        <!-- Header -->
        <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
            <flux:heading size="sm">Notifications</flux:heading>
            <div class="flex items-center gap-2">
                @if($unreadCount > 0)
                    <flux:badge variant="primary" size="sm">{{ $unreadCount }} New</flux:badge>
                    <button
                        wire:click="markAllAsRead"
                        class="text-xs text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100">
                        Mark all as read
                    </button>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            @if(empty($recentNotifications) || count($recentNotifications) === 0)
                <div class="px-4 py-8 text-center">
                    <flux:icon name="bell-slash" class="mx-auto text-zinc-400 mb-2" />
                    <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                        No notifications yet
                    </flux:text>
                </div>
            @else
                @foreach($recentNotifications as $notification)
                    <div
                        wire:key="bell-notification-{{ $notification->id }}"
                        class="px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 border-b border-zinc-100 dark:border-zinc-700 last:border-b-0 {{ !$notification->isRead() ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">

                        <div class="flex gap-3">
                            <!-- Icon -->
                            <div class="shrink-0 mt-1">
                                <div class="w-8 h-8 rounded-full {{ $notification->isRead() ? 'bg-zinc-100 dark:bg-zinc-700' : 'bg-blue-100 dark:bg-blue-900' }} flex items-center justify-center">
                                    @switch($notification->type)
                                        @case('appointment_confirmation')
                                            <flux:icon name="check-circle" size="sm" class="text-green-600 dark:text-green-400" />
                                            @break
                                        @case('appointment_reminder')
                                        @case('appointment_cancellation')
                                            <flux:icon name="x-circle" size="sm" class="text-red-600 dark:text-red-400" />
                                            @break
                                        @case('admin_new_appointment')
                                            <flux:icon name="calendar" size="sm" class="text-blue-600 dark:text-blue-400" />
                                            @break
                                        @case('admin_appointment_cancellation')
                                            <flux:icon name="x-circle" size="sm" class="text-red-600 dark:text-red-400" />
                                            @break
                                        @case('feedback_received')
                                            <flux:icon name="chat-bubble-left-right" size="sm" class="text-purple-600 dark:text-purple-400" />
                                            @break
                                        @case('doctor_schedule')
                                            <flux:icon name="calendar" size="sm" class="text-emerald-600 dark:text-emerald-400" />
                                            @break
                                        @case('patient_checked_in')
                                            <flux:icon name="user" size="sm" class="text-amber-600 dark:text-amber-400" />
                                            @break
                                        @case('urgent_patient_note')
                                            <flux:icon name="exclamation-triangle" size="sm" class="text-amber-600 dark:text-amber-400" />
                                            @break
                                        @case('medical_record_request')
                                            <flux:icon name="clipboard-document" size="sm" class="text-sky-600 dark:text-sky-400" />
                                            @break
                                        @case('registration_approval')
                                            <flux:icon name="check-circle" size="sm" class="text-green-600 dark:text-green-400" />
                                            @break
                                        @case('feedback_request')
                                            <flux:icon name="chat-bubble-left-right" size="sm" class="text-purple-600 dark:text-purple-400" />
                                            @break
                                        @case('medical_record_update')
                                            <flux:icon name="clipboard-document-check" size="sm" class="text-emerald-600 dark:text-emerald-400" />
                                            @break
                                        @case('urgent_note')
                                            <flux:icon name="exclamation-triangle" size="sm" class="text-amber-600 dark:text-amber-400" />
                                            @break
                                        @case('announcement')
                                            <flux:icon name="megaphone" size="sm" class="text-sky-600 dark:text-sky-400" />
                                            @break
                                        @case('new_appointment')
                                        @case('new_patient_registration')
                                        @case('patient_created')
                                            <flux:icon name="users" size="sm" class="text-zinc-600 dark:text-zinc-400" />
                                            @break
                                        @default
                                            <flux:icon name="bell" size="sm" class="{{ $notification->isRead() ? 'text-zinc-600 dark:text-zinc-400' : 'text-blue-600 dark:text-blue-400' }}" />
                                    @endswitch
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <flux:text size="sm" weight="semibold" class="line-clamp-1">
                                    {{ $notification->title }}
                                </flux:text>
                                <flux:text size="xs" class="text-zinc-600 dark:text-zinc-400 line-clamp-2 mt-0.5">
                                    {{ $notification->message }}
                                </flux:text>
                                <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500 mt-1">
                                    {{ $notification->created_at->diffForHumans() }}
                                </flux:text>
                            </div>

                            <!-- Mark as Read -->
                            @if(!$notification->isRead())
                                <button
                                    wire:click="markAsRead({{ $notification->id }})"
                                    class="shrink-0 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                                    <flux:icon name="check" size="sm" />
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 text-center">
            <a
                href="{{ route($notificationRoute) }}"
                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium"
                @click="open = false">
                View All Notifications
            </a>
        </div>
    </div>
</div>
