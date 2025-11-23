<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                Appointment Management
            </h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                Manage {{ $adminCategory ? $adminCategory->label() : 'all' }} appointments
            </p>
        </div>
    </div>

    {{-- Success Message --}}
    @if (session()->has('message'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-7 gap-4">
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Today's Total</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">
                        {{ $statistics['total_today'] }}
                    </p>
                </div>
                <div class="text-blue-500">
                    <flux:icon.calendar class="w-8 h-8" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">
                        {{ $statistics['pending'] }}
                    </p>
                </div>
                <div class="text-yellow-500">
                    <flux:icon.clock class="w-8 h-8" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Confirmed</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                        {{ $statistics['confirmed'] }}
                    </p>
                </div>
                <div class="text-blue-500">
                    <flux:icon.check-circle class="w-8 h-8" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Checked In</p>
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">
                        {{ $statistics['checked_in'] }}
                    </p>
                </div>
                <div class="text-indigo-500">
                    <flux:icon name="user" class="w-8 h-8" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">In Progress</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">
                        {{ $statistics['in_progress'] }}
                    </p>
                </div>
                <div class="text-purple-500">
                    <flux:icon.play class="w-8 h-8" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Completed</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                        {{ $statistics['completed_today'] }}
                    </p>
                </div>
                <div class="text-green-500">
                    <flux:icon.check class="w-8 h-8" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">No-Show</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">
                        {{ $statistics['no_show_today'] }}
                    </p>
                </div>
                <div class="text-red-500">
                    <flux:icon.x-circle class="w-8 h-8" />
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search appointments..." />
            </div>

            <div>
                <flux:select wire:model.live="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="checked_in">Checked In</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="no_show">No-Show</option>
                </flux:select>
            </div>

            <div>
                <flux:input type="date" wire:model.live="dateFilter" />
            </div>

            <div>
                <flux:select wire:model.live="serviceFilter">
                    <option value="">All Services</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </flux:select>
            </div>

            <div>
                <flux:button wire:click="$set('search', '')" variant="ghost" class="w-full">
                    Clear Filters
                </flux:button>
            </div>
        </div>
    </div>

    {{-- Appointments Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Appointment #
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Patient
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Service
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Purpose
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Scheduled Date/Time
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Doctor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-nowrap">
                            Queue #
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($appointments as $appointment)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $appointment->appointment_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                {{ $appointment->patient->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                {{ $appointment->service->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                @if ($appointment->health_card_purpose)
                                    <flux:badge variant="outline" size="sm">
                                        {{ $appointment->health_card_purpose === 'food' ? 'Food Handler' : 'Non-Food Handler' }}
                                    </flux:badge>
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-500">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                {{ $appointment->scheduled_at ? $appointment->scheduled_at->format('M d, Y h:i A') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                @if ($appointment->doctor)
                                    {{ $appointment->doctor->user->name ?? 'N/A' }}
                                @else
                                    <span class="text-zinc-500 dark:text-zinc-400 italic">Not assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                {{ $appointment->queue_number ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'yellow',
                                        'confirmed' => 'blue',
                                        'checked_in' => 'indigo',
                                        'in_progress' => 'purple',
                                        'completed' => 'green',
                                        'cancelled' => 'red',
                                        'no_show' => 'red',
                                    ];
                                    $color = $statusColors[$appointment->status] ?? 'zinc';
                                @endphp
                                <flux:badge color="{{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <flux:button wire:click="viewDetails({{ $appointment->id }})" size="sm" variant="ghost">
                                    View
                                </flux:button>

                                <flux:button wire:click="openStatusModal({{ $appointment->id }})" size="sm" variant="ghost">
                                    Change Status
                                </flux:button>

                                @if (!$appointment->doctor_id || $appointment->status === 'pending')
                                    <flux:button wire:click="openAssignDoctorModal({{ $appointment->id }})" size="sm" variant="primary" color="blue" class="hidden! cursor-pointer">
                                        Assign Doctor
                                    </flux:button>
                                @endif

                                @if ($appointment->status === 'confirmed' && $appointment->canCheckIn())
                                    <flux:button wire:click="checkInAppointment({{ $appointment->id }})" size="sm" variant="ghost">
                                        Check In
                                    </flux:button>
                                @endif

                                @if ($appointment->status === 'checked_in')
                                    <flux:button wire:click="startAppointment({{ $appointment->id }})" size="sm" variant="ghost">
                                        Start
                                    </flux:button>
                                @endif

                                @if ($appointment->status === 'in_progress')
                                    <flux:button wire:click="completeAppointment({{ $appointment->id }})" size="sm" variant="ghost">
                                        Complete
                                    </flux:button>
                                @endif

                                @if (in_array($appointment->status, ['confirmed', 'checked_in']) && now()->greaterThan($appointment->scheduled_at->addMinutes(30)))
                                    <flux:button wire:click="markNoShow({{ $appointment->id }})" size="sm" variant="ghost">
                                        No-Show
                                    </flux:button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                No appointments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $appointments->links() }}
        </div>
    </div>

    {{-- View Details Modal --}}
    @if ($showDetailsModal && $selectedAppointment)
        <flux:modal wire:model="showDetailsModal" class="max-w-2xl">
            <div class="space-y-4">
                <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Appointment Details</h2>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Appointment Number</p>
                        <p class="text-base text-zinc-900 dark:text-white mt-1">{{ $selectedAppointment->appointment_number }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Status</p>
                        <div class="mt-1">
                            @php
                                $statusColors = [
                                    'pending' => 'yellow',
                                    'confirmed' => 'blue',
                                    'checked_in' => 'indigo',
                                    'in_progress' => 'purple',
                                    'completed' => 'green',
                                    'cancelled' => 'red',
                                    'no_show' => 'red',
                                ];
                                $color = $statusColors[$selectedAppointment->status] ?? 'zinc';
                            @endphp
                            <flux:badge color="{{ $color }}">
                                {{ ucfirst(str_replace('_', ' ', $selectedAppointment->status)) }}
                            </flux:badge>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Patient Name</p>
                        <p class="text-base text-zinc-900 dark:text-white mt-1">{{ $selectedAppointment->patient->user->name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Service</p>
                        <p class="text-base text-zinc-900 dark:text-white mt-1">{{ $selectedAppointment->service->name ?? 'N/A' }}</p>
                    </div>

                    @if ($selectedAppointment->health_card_purpose)
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Health Card Purpose</p>
                            <div class="mt-1">
                                <flux:badge variant="outline" size="sm">
                                    {{ $selectedAppointment->health_card_purpose === 'food' ? 'Food Handler' : 'Non-Food Handler' }}
                                </flux:badge>
                            </div>
                        </div>
                    @endif

                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Scheduled Date & Time</p>
                        <p class="text-base text-zinc-900 dark:text-white mt-1">
                            {{ $selectedAppointment->scheduled_at ? $selectedAppointment->scheduled_at->format('M d, Y h:i A') : 'N/A' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Queue Number</p>
                        <p class="text-base text-zinc-900 dark:text-white mt-1">{{ $selectedAppointment->queue_number ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Assigned Doctor</p>
                        <p class="text-base text-zinc-900 dark:text-white mt-1">
                            @if ($selectedAppointment->doctor)
                                {{ $selectedAppointment->doctor->user->name ?? 'N/A' }}
                            @else
                                <span class="text-zinc-500 dark:text-zinc-400 italic">Not assigned</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Fee</p>
                        <p class="text-base text-zinc-900 dark:text-white mt-1">₱{{ number_format($selectedAppointment->fee ?? 0, 2) }}</p>
                    </div>

                    @if ($selectedAppointment->check_in_at)
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Check-in Time</p>
                            <p class="text-base text-zinc-900 dark:text-white mt-1">
                                {{ $selectedAppointment->check_in_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    @endif

                    @if ($selectedAppointment->started_at)
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Started At</p>
                            <p class="text-base text-zinc-900 dark:text-white mt-1">
                                {{ $selectedAppointment->started_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    @endif

                    @if ($selectedAppointment->completed_at)
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Completed At</p>
                            <p class="text-base text-zinc-900 dark:text-white mt-1">
                                {{ $selectedAppointment->completed_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    @endif

                    @if ($selectedAppointment->cancellation_reason)
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Cancellation Reason</p>
                            <p class="text-base text-zinc-900 dark:text-white mt-1">{{ $selectedAppointment->cancellation_reason }}</p>
                        </div>
                    @endif

                    @if ($selectedAppointment->notes)
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Notes</p>
                            <p class="text-base text-zinc-900 dark:text-white mt-1">{{ $selectedAppointment->notes }}</p>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="closeDetailsModal" variant="ghost">
                        Close
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    {{-- Assign Doctor Modal --}}
    @if ($showAssignDoctorModal && $selectedAppointment)
        <flux:modal wire:model="showAssignDoctorModal" class="md:w-96">
            <div class="space-y-4">
                <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Assign Doctor</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                        Appointment: {{ $selectedAppointment->appointment_number }}
                    </p>
                </div>

                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Patient: {{ $selectedAppointment->patient->user->name ?? 'N/A' }}
                    </p>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-4">
                        Service: {{ $selectedAppointment->service->name ?? 'N/A' }}
                    </p>

                    <flux:field>
                        <flux:label>Select Doctor</flux:label>
                        <flux:select wire:model="selectedDoctorId">
                            <option value="">-- Select a doctor --</option>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                            @endforeach
                        </flux:select>
                        @error('selectedDoctorId')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="closeAssignDoctorModal" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button wire:click="assignDoctor" variant="primary" color="cyan">
                        Assign Doctor
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    {{-- Update Status Modal --}}
    @if ($showStatusModal && ($statusForm['appointment_id'] ?? null))
        <flux:modal wire:model="showStatusModal" class="md:w-120">
            <div class="space-y-4">
                <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Update Status</h2>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400 mt-1 space-y-0.5">
                        <p>
                            <span class="font-medium">Appointment:</span>
                            {{ $selectedAppointment?->appointment_number ?? $statusForm['appointment_id'] }}
                        </p>
                        <p>
                            <span class="font-medium">Patient:</span>
                            {{ $selectedAppointment?->patient?->user?->name ?? '—' }}
                        </p>
                        <p>
                            <span class="font-medium">When:</span>
                            {{ optional($selectedAppointment?->scheduled_at)->format('M d, Y g:i A') ?? '—' }}
                        </p>
                        <p>
                            <span class="font-medium">Service:</span>
                            {{ $selectedAppointment?->service?->name ?? '—' }}
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <flux:field>
                        <flux:label>New Status</flux:label>
                        <flux:select wire:model.live="statusForm.to">
                            <option value="">-- Select status --</option>
                            @foreach ($availableStatuses as $opt)
                                <option value="{{ $opt }}">{{ ucfirst(str_replace('_', ' ', $opt)) }}</option>
                            @endforeach
                        </flux:select>
                        @error('statusForm.to')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    @if (($statusForm['to'] ?? '') === 'cancelled')
                        <flux:field>
                            <flux:label>Cancellation Reason</flux:label>
                            <flux:textarea rows="3" wire:model.defer="statusForm.reason" placeholder="Provide a brief reason" />
                            @error('statusForm.reason')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    @endif
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="closeStatusModal" variant="ghost">Cancel</flux:button>
                    <flux:button wire:click="updateStatus" variant="primary" color="blue">Update</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
