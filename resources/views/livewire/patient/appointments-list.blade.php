<div class="max-w-6xl mx-auto space-y-6" wire:poll.10s="refreshList">
  <div class="my-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">My Appointments</h1>
    <p class="text-gray-600 dark:text-gray-300">View and manage your appointments</p>
  </div>

  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-4 flex items-center justify-between">
      <div class="flex gap-2">
        <flux:button variant="{{ $tab === 'upcoming' ? 'primary' : 'outline' }}" wire:click="setTab('upcoming')">Upcoming
        </flux:button>
        <flux:button variant="{{ $tab === 'past' ? 'primary' : 'outline' }}" wire:click="setTab('past')">Past
        </flux:button>
        <flux:button variant="{{ $tab === 'cancelled' ? 'primary' : 'outline' }}" wire:click="setTab('cancelled')">
          Cancelled</flux:button>
      </div>

      <div>
        <a href="{{ route('patient.book-appointment') }}"
          class="inline-flex items-center px-3 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
          <flux:icon name="calendar" class="w-4 h-4 mr-2" /> Book new
        </a>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th
              class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Appt #</th>
            <th
              class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Service</th>
            <th
              class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Date</th>
            <th
              class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Time</th>
            <th
              class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Status</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
          @forelse ($appointments as $appointment)
            <tr>
              <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $appointment->appointment_number }}</td>
              <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $appointment->service?->name ?? '—' }}
              </td>
              <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                {{ optional($appointment->scheduled_at)->format('M d, Y') }}</td>
              <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                {{ optional($appointment->scheduled_at)->format('g:i A') }}</td>
              <td class="px-4 py-3">
                <x-appointment-status-badge :status="$appointment->status" />
              </td>
              <td class="px-4 py-3 text-right flex items-center gap-2 justify-end">
                <a href="{{ route('patient.appointments.details', ['appointment' => $appointment->id]) }}"
                  class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                  <flux:icon name="eye" class="w-4 h-4 mr-2" /> View
                </a>
                @if ($appointment->canBeCancelled())
                  <flux:button icon="x-circle" size="sm" variant="danger"
                    wire:click="openCancelModal({{ $appointment->id }})" wire:loading.attr="disabled">
                    Cancel
                  </flux:button>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No appointments found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4">{{ $appointments->links() }}</div>

    {{-- Cancel Modal --}}
    <flux:modal wire:model="showCancelModal">
      <div class="p-4 space-y-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cancel appointment?</h3>
        <p class="text-sm text-gray-600 dark:text-gray-300">You can only cancel 24+ hours before its scheduled time.</p>
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
</div>
