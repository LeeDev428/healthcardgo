<div class="relative" x-data="{ open: false, showModal: false }" @click.away="open = false" wire:poll.30s="loadNotifications">
    <!-- Bell Icon -->
    <button @click="open = !open" class="relative p-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full flex items-center justify-center">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-96 bg-white dark:bg-zinc-800 rounded-lg shadow-xl border border-zinc-200 dark:border-zinc-700"
         style="z-index: 9999;">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-zinc-900 dark:text-white">Notifications</h3>
                @if($unreadCount > 0)
                    <button wire:click="markAllAsRead" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                        Mark all as read
                    </button>
                @endif
            </div>
        </div>

        <!-- Notification List -->
        <div class="max-h-96 overflow-y-auto">
            @forelse($recentNotifications as $notification)
                <div wire:key="notif-{{ $notification->id }}" 
                     @click="$wire.selectNotification({{ $notification->id }}); showModal = true; open = false"
                     class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 cursor-pointer {{ !$notification->isRead() ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ !$notification->isRead() ? 'bg-blue-100 dark:bg-blue-900' : 'bg-zinc-100 dark:bg-zinc-700' }}">
                                @if(str_contains($notification->type, 'appointment'))
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white truncate">{{ $notification->title }}</p>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 line-clamp-2">{{ $notification->message }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-zinc-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">No notifications yet</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 text-center">
            <a href="{{ route($notificationRoute) }}" 
               wire:navigate
               @click="open = false"
               class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                View All Notifications
            </a>
        </div>
    </div>

    <!-- Notification Details Modal -->
    <div x-show="showModal" 
         x-cloak
         @click.self="showModal = false"
         class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/50">
        <div @click.away="showModal = false"
             class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            @if($selectedNotification)
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                            Confirmed
                        </span>
                    </div>
                    <button @click="showModal = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 grid md:grid-cols-2 gap-6">
                    <!-- Patient Information -->
                    <div>
                        <h3 class="flex items-center gap-2 text-lg font-semibold text-zinc-900 dark:text-white mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Patient Information
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Name</span>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedNotification->data['patient_name'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Patient Number</span>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedNotification->data['patient_number'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Age</span>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedNotification->data['patient_age'] ?? 'N/A' }} years old</p>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Gender</span>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ ucfirst($selectedNotification->data['patient_gender'] ?? 'N/A') }}</p>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Blood Type</span>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedNotification->data['patient_blood_type'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Barangay</span>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedNotification->data['patient_barangay'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Service Details -->
                    <div>
                        <h3 class="flex items-center gap-2 text-lg font-semibold text-zinc-900 dark:text-white mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Service Details
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Service</span>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedNotification->data['service_name'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Description</span>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedNotification->data['service_description'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Duration</span>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedNotification->data['service_duration'] ?? '0' }} minutes</p>
                            </div>
                            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <p class="text-xs font-semibold text-yellow-800 dark:text-yellow-200">Provider Not Assigned</p>
                                        <p class="text-xs text-yellow-700 dark:text-yellow-300">Will be assigned by admin</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule -->
                        <h3 class="flex items-center gap-2 text-lg font-semibold text-zinc-900 dark:text-white mb-4 mt-6">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Schedule
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Date & Time</span>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedNotification->data['appointment_date'] ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Payment -->
                        <h3 class="flex items-center gap-2 text-lg font-semibold text-zinc-900 dark:text-white mb-4 mt-6">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            Payment
                        </h3>
                        <div class="space-y-3 text-sm">
                            <p class="font-bold text-2xl text-green-600 dark:text-green-400">₱{{ number_format($selectedNotification->data['service_price'] ?? 0, 2) }}<span class="text-sm font-normal text-zinc-500">Free</span></p>
                        </div>

                        <!-- Notes -->
                        @if(!empty($selectedNotification->data['notes']))
                            <h3 class="flex items-center gap-2 text-lg font-semibold text-zinc-900 dark:text-white mb-4 mt-6">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Notes
                            </h3>
                            <div class="space-y-3 text-sm">
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedNotification->data['notes'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 flex justify-end">
                    <button @click="showModal = false" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-600">
                        Close
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
