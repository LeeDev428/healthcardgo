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
                    <button
                        wire:key="bell-notification-{{ $notification->id }}"
                        wire:click="viewNotification({{ $notification->id }})"
                        class="w-full px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 border-b border-zinc-100 dark:border-zinc-700 last:border-b-0 {{ !$notification->isRead() ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }} text-left transition-colors cursor-pointer">

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
                    </button>
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

    <!-- Appointment Details Modal -->
    @if($showModal && $selectedAppointment)
        <div
            x-data="{ show: @entangle('showModal') }"
            x-show="show"
            @click.self="$wire.closeModal()"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 flex items-center justify-center bg-black/50 p-4"
            style="z-index: 9999 !important; display: none;"
            x-cloak>
            
            <div
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-zinc-800 rounded-xl shadow-2xl w-[90vw] max-w-5xl max-h-[80vh] flex flex-col"
                @click.stop>
                
                <!-- Modal Header -->
                <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 px-4 py-3 flex items-center justify-between flex-shrink-0">
                    <div>
                        <flux:heading size="base">Appointment Details</flux:heading>
                        <flux:text size="xs" class="text-zinc-600 dark:text-zinc-400">
                            {{ $selectedAppointment->appointment_number }}
                        </flux:text>
                    </div>
                    <button
                        wire:click="closeModal"
                        class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors">
                        <flux:icon name="x-mark" size="md" />
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="px-4 py-3 overflow-y-auto flex-1">
                    
                    <!-- Status Badge -->
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    'confirmed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                    'checked_in' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                    'in_progress' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                ];
                                $statusColor = $statusColors[$selectedAppointment->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                {{ ucfirst(str_replace('_', ' ', $selectedAppointment->status)) }}
                            </span>
                        </div>
                        <div class="text-right">
                            <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">Queue Number</flux:text>
                            <flux:text size="xl" weight="bold" class="text-blue-600 dark:text-blue-400">
                                #{{ $selectedAppointment->queue_number }}
                            </flux:text>
                        </div>
                    </div>

                    <!-- Two Column Layout -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        
                        <!-- Left Column -->
                        <div class="space-y-3">
                            
                            <!-- Patient Information -->
                            <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-2.5">
                                <flux:heading size="xs" class="mb-1.5 flex items-center">
                                    <flux:icon name="user" size="xs" class="mr-1.5" />
                                    Patient Information
                                </flux:heading>
                                <div class="space-y-1">
                                    <div>
                                        <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">Name</flux:text>
                                        <flux:text size="sm" weight="semibold">{{ $selectedAppointment->patient->user->name }}</flux:text>
                                    </div>
                                    @if($selectedAppointment->patient->patient_number)
                                        <div>
                                            <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">Patient Number</flux:text>
                                            <flux:text size="xs">{{ $selectedAppointment->patient->patient_number }}</flux:text>
                                        </div>
                                    @endif
                                    @if($selectedAppointment->patient->barangay)
                                        <div>
                                            <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">Barangay</flux:text>
                                            <flux:text size="xs">{{ $selectedAppointment->patient->barangay->name }}</flux:text>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Service Information -->
                            <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-2.5">
                                <flux:heading size="xs" class="mb-1.5 flex items-center">
                                    <flux:icon name="clipboard-document-list" size="xs" class="mr-1.5" />
                                    Service Details
                                </flux:heading>
                                <div class="space-y-1">
                                    <div>
                                        <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">Service</flux:text>
                                        <flux:text size="sm" weight="semibold">{{ $selectedAppointment->service->name }}</flux:text>
                                    </div>
                                    <div>
                                        <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">Duration</flux:text>
                                        <flux:text size="xs">{{ $selectedAppointment->service->duration_minutes }} minutes</flux:text>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule Information -->
                            <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-2.5">
                                <flux:heading size="xs" class="mb-1.5 flex items-center">
                                    <flux:icon name="calendar" size="xs" class="mr-1.5" />
                                    Schedule
                                </flux:heading>
                                <div class="space-y-1">
                                    <div>
                                        <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">Date & Time</flux:text>
                                        <flux:text size="sm" weight="semibold">{{ $selectedAppointment->scheduled_at->format('M d, Y g:i A') }}</flux:text>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Right Column -->
                        <div class="space-y-3">
                            
                            <!-- Doctor/Provider Information -->
                            @if($selectedAppointment->doctor)
                                <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-2.5">
                                    <flux:heading size="xs" class="mb-1.5 flex items-center">
                                        <flux:icon name="user-circle" size="xs" class="mr-1.5" />
                                        Healthcare Provider
                                    </flux:heading>
                                    <div class="space-y-1">
                                        <div>
                                            <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">Doctor</flux:text>
                                            <flux:text size="sm" weight="semibold">Dr. {{ $selectedAppointment->doctor->user->name }}</flux:text>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-2.5 border border-yellow-200 dark:border-yellow-800">
                                    <div class="flex items-start gap-2">
                                        <flux:icon name="exclamation-circle" class="text-yellow-600 dark:text-yellow-400 mt-0.5" size="xs" />
                                        <div>
                                            <flux:text size="xs" weight="semibold" class="text-yellow-800 dark:text-yellow-300">
                                                Provider Not Assigned
                                            </flux:text>
                                            <flux:text size="xs" class="text-yellow-700 dark:text-yellow-400">
                                                Will be assigned by admin
                                            </flux:text>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Fee Information -->
                            <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-2.5">
                                <flux:heading size="xs" class="mb-1.5 flex items-center">
                                    <flux:icon name="currency-dollar" size="xs" class="mr-1.5" />
                                    Payment
                                </flux:heading>
                                <div class="flex items-baseline">
                                    <flux:text size="lg" weight="bold" class="text-green-600 dark:text-green-400">
                                        ₱{{ number_format($selectedAppointment->fee, 2) }}
                                    </flux:text>
                                    @if($selectedAppointment->fee == 0)
                                        <flux:text size="xs" class="ml-2 text-zinc-500">Free</flux:text>
                                    @endif
                                </div>
                            </div>

                            <!-- Notes Section -->
                            @if($selectedAppointment->notes)
                                <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-2.5">
                                    <flux:heading size="xs" class="mb-1.5 flex items-center">
                                        <flux:icon name="document-text" size="xs" class="mr-1.5" />
                                        Notes
                                    </flux:heading>
                                    <flux:text size="xs" class="text-zinc-700 dark:text-zinc-300">
                                        {{ $selectedAppointment->notes }}
                                    </flux:text>
                                </div>
                            @endif

                        </div>

                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="bg-white dark:bg-zinc-800 border-t border-zinc-200 dark:border-zinc-700 px-4 py-2.5 flex justify-end gap-2 flex-shrink-0">
                    <flux:button wire:click="closeModal" variant="ghost">
                        Close
                    </flux:button>
                </div>

            </div>
        </div>
    @endif
</div>
