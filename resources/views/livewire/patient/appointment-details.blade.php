<div class="max-w-3xl mx-auto space-y-6" wire:poll.5s="refresh">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Appointment Details</h1>
            <p class="text-gray-600 dark:text-gray-300">Review your appointment information</p>
        </div>
        <a href="{{ route('patient.appointments.list') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm hover:bg-gray-200 dark:hover:bg-gray-600">
            <flux:icon name="arrow-left" class="w-4 h-4 mr-2" /> Back to list
        </a>
    </div>

    @if ($appointment)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700">
            <div class="p-5 grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Appointment #</h2>
                    <p class="mt-1 text-gray-900 dark:text-gray-100 font-semibold">{{ $appointment->appointment_number }}</p>
                </div>
                <div>
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Status</h2>
                                <p class="mt-1"><x-appointment-status-badge :status="$appointment->status" /></p>
                </div>
                <div>
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Service</h2>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $appointment->service?->name ?? '—' }}</p>
                    @if ($appointment->service)
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Duration: {{ $appointment->service->duration_minutes }} mins</p>
                    @endif
                </div>
                <div>
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Scheduled At</h2>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ optional($appointment->scheduled_at)->format('l, F j, Y') }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ optional($appointment->scheduled_at)->format('g:i A') }}</p>
                </div>
            </div>
            <div class="p-5 grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Queue #</h2>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $appointment->queue_number ?? '—' }}</p>
                </div>
                <div>
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Healthcare Provider</h2>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $appointment->doctor?->name ?? 'To be assigned' }}</p>
                </div>
                <div class="md:col-span-2">
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Notes</h2>
                    <p class="mt-1 text-gray-700 dark:text-gray-200 whitespace-pre-line">{{ $appointment->notes ?? '—' }}</p>
                </div>
            </div>

            @if ($appointment->qr_code_path || $appointment->digital_copy_path)
                <div class="p-5 flex flex-col md:flex-row gap-8 items-start">
                    @if ($appointment->qr_code_path)
                        <div class="space-y-2">
                            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">QR Code</h2>
                            <img src="{{ asset('storage/'.$appointment->qr_code_path) }}" alt="Appointment QR Code" class="w-40 h-40 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-2"/>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Scan to open the digital copy of this appointment.</p>
                        </div>
                    @endif
                    @if ($appointment->digital_copy_path)
                        <div class="space-y-2">
                            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Digital Copy</h2>
                            <div class="flex flex-wrap gap-2">
                                <flux:button icon="document" href="{{ URL::temporarySignedRoute('appointments.digital', now()->addMinutes(30), ['appointment' => $appointment->id]) }}" target="_blank" variant="primary" color="blue">
                                    View Digital Copy
                                </flux:button>
                                <flux:button icon="arrow-down-tray" href="{{ URL::temporarySignedRoute('appointments.digital.download', now()->addMinutes(30), ['appointment' => $appointment->id]) }}" variant="outline">
                                    Download PDF
                                </flux:button>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Link expires in 30 minutes. You can always scan the QR again.</p>
                        </div>
                    @endif
                </div>
            @endif

            @if ($appointment->status === 'cancelled' && $appointment->cancellation_reason)
                <div class="p-5 bg-red-50 dark:bg-red-900/20">
                    <h2 class="text-sm font-medium text-red-700 dark:text-red-400 uppercase">Cancellation Reason</h2>
                    <p class="mt-1 text-red-800 dark:text-red-300">{{ $appointment->cancellation_reason }}</p>
                </div>
            @endif

                    <div class="p-5 flex justify-between gap-2">
                        <a href="{{ route('patient.book-appointment') }}" class="px-3 py-2 text-sm rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Book another</a>

                        @if ($appointment->canBeCancelled())
                            <flux:button icon="x-circle" variant="danger" wire:click="openCancelModal" wire:loading.attr="disabled">
                                Cancel Appointment
                            </flux:button>
                        @endif
                    </div>
        </div>
    @else
        <div class="p-8 text-center text-gray-500 dark:text-gray-400">Appointment not found.</div>
    @endif

            {{-- Cancel Modal --}}
            <flux:modal wire:model="showCancelModal">
                <div class="p-4 space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cancel appointment?</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">You can only cancel 24+ hours before the scheduled time. This action cannot be undone.</p>

                    <flux:field>
                        <flux:label>Reason (optional)</flux:label>
                        <flux:textarea wire:model="cancellationReason" placeholder="Reason for cancellation..."></flux:textarea>
                    </flux:field>

                    <div class="flex justify-end gap-2">
                        <flux:button variant="outline" wire:click="closeCancelModal">Close</flux:button>
                        <flux:button variant="danger" wire:click="cancelAppointment" wire:loading.attr="disabled">
                            <span wire:loading.remove>Confirm Cancel</span>
                            <span wire:loading>Processing...</span>
                        </flux:button>
                    </div>
                </div>
            </flux:modal>
</div>
