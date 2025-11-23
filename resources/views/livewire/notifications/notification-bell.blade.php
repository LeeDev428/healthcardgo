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
            class="fixed inset-0 flex items-center justify-center bg-black/50 px-4 py-12"
            style="z-index: 9999 !important; display: none;"
            x-cloak>
            
            <div
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-zinc-800 rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col"
                @click.stop>
                
                <!-- Modal Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    <!-- Status Badge -->
                    <div class="mb-6">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'confirmed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                'checked_in' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                'in_progress' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                                'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                'no_show' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
                            ];
                            $statusColor = $statusColors[$selectedAppointment->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                            {{ ucfirst(str_replace('_', ' ', $selectedAppointment->status)) }}
                        </span>
                    </div>

                    <!-- Two Column Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Patient Information -->
                            <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-5 h-5 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Patient Information</h3>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <p class="text-zinc-500 dark:text-zinc-400">Name</p>
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedAppointment->patient->user->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-zinc-500 dark:text-zinc-400">Patient Number</p>
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedAppointment->patient->patient_number }}</p>
                                    </div>
                                    <div>
                                        <p class="text-zinc-500 dark:text-zinc-400">Age</p>
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedAppointment->patient->date_of_birth->age }} years old</p>
                                    </div>
                                    <div>
                                        <p class="text-zinc-500 dark:text-zinc-400">Gender</p>
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ ucfirst($selectedAppointment->patient->gender) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-zinc-500 dark:text-zinc-400">Blood Type</p>
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedAppointment->patient->blood_type ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-zinc-500 dark:text-zinc-400">Barangay</p>
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedAppointment->patient->barangay->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule -->
                            <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-5 h-5 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Schedule</h3>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <p class="text-zinc-500 dark:text-zinc-400">Date & Time</p>
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedAppointment->scheduled_at->format('M d, Y g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Service Details -->
                            <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-5 h-5 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Service Details</h3>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <p class="text-zinc-500 dark:text-zinc-400">Service</p>
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedAppointment->service->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-zinc-500 dark:text-zinc-400">Description</p>
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedAppointment->service->description }}</p>
                                    </div>
                                    <div>
                                        <p class="text-zinc-500 dark:text-zinc-400">Duration</p>
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedAppointment->service->duration }} minutes</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Provider -->
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <p class="font-semibold text-yellow-900 dark:text-yellow-200">Provider Not Assigned</p>
                                        <p class="text-sm text-yellow-700 dark:text-yellow-300">Will be assigned by admin</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment -->
                            <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-5 h-5 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Payment</h3>
                                </div>
                                <p class="text-green-600 dark:text-green-400 font-bold text-lg">₱0.00<span class="text-sm font-normal">Free</span></p>
                            </div>

                            <!-- Notes -->
                            @if($selectedAppointment->notes)
                                <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-4">
                                    <div class="flex items-center gap-2 mb-3">
                                        <svg class="w-5 h-5 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Notes</h3>
                                    </div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $selectedAppointment->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="border-t border-zinc-200 dark:border-zinc-700 p-4 flex justify-end">
                    <button
                        @click="$wire.closeModal()"
                        class="px-6 py-2 bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-600 transition-colors font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
