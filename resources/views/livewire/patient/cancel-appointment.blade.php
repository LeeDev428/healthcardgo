<div>
    @if ($appointment->canBeCancelled())
        <flux:button variant="danger" size="sm" wire:click="openModal">
            Cancel Appointment
        </flux:button>
    @else
        <flux:text size="sm" class="text-zinc-500">
            Cannot cancel (less than 24 hours before)
        </flux:text>
    @endif

    @if ($showModal)
        <flux:modal wire:model="showModal" class="space-y-6">
            <div>
                <flux:heading size="lg">Cancel Appointment</flux:heading>
                <flux:text class="mt-2">
                    Are you sure you want to cancel this appointment? This action cannot be undone.
                </flux:text>
                <flux:text class="mt-2 text-sm text-zinc-600">
                    Queue numbers for other patients will be automatically recalculated.
                </flux:text>
            </div>

            <div class="space-y-4">
                <div>
                    <flux:text weight="semibold">Appointment Details</flux:text>
                    <flux:text size="sm" class="mt-1">
                        Number: {{ $appointment->appointment_number }}<br>
                        Queue: #{{ $appointment->queue_number }}<br>
                        Date: {{ $appointment->scheduled_at->format('F j, Y') }}<br>
                        Time: {{ $appointment->scheduled_at->format('g:i A') }}<br>
                        Service: {{ $appointment->service->name }}
                    </flux:text>
                </div>

                <flux:field>
                    <flux:label>Reason for Cancellation</flux:label>
                    <flux:textarea
                        wire:model="cancellationReason"
                        placeholder="Please provide a reason for cancelling this appointment (minimum 10 characters)"
                        rows="4"
                    />
                    <flux:error name="cancellationReason" />
                </flux:field>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:button variant="ghost" wire:click="closeModal">
                    Keep Appointment
                </flux:button>
                <flux:button variant="danger" wire:click="cancel">
                    Confirm Cancellation
                </flux:button>
            </div>
        </flux:modal>
    @endif
</div>
