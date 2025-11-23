<div class="max-w-7xl space-y-6">
  <!-- Header -->
  <div class="flex items-center justify-between">
    <div>
      <flux:heading size="xl">Appointments Management</flux:heading>
      <flux:text class="text-zinc-600 dark:text-zinc-400">View and manage all appointments</flux:text>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Total</flux:text>
      <flux:text weight="bold" size="xl">{{ number_format($stats['total']) }}</flux:text>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Pending</flux:text>
      <flux:text weight="bold" size="xl" class="text-yellow-600 dark:text-yellow-400">{{ number_format($stats['pending']) }}</flux:text>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Confirmed</flux:text>
      <flux:text weight="bold" size="xl" class="text-blue-600 dark:text-blue-400">{{ number_format($stats['confirmed']) }}</flux:text>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Completed</flux:text>
      <flux:text weight="bold" size="xl" class="text-green-600 dark:text-green-400">{{ number_format($stats['completed']) }}</flux:text>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Cancelled</flux:text>
      <flux:text weight="bold" size="xl" class="text-red-600 dark:text-red-400">{{ number_format($stats['cancelled']) }}</flux:text>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Today</flux:text>
      <flux:text weight="bold" size="xl" class="text-purple-600 dark:text-purple-400">{{ number_format($stats['today']) }}</flux:text>
    </div>
  </div>

  <!-- Filters -->
  <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Search -->
      <div class="lg:col-span-2">
        <flux:input
          wire:model.live.debounce.300ms="search"
          placeholder="Search by patient name, email, or appointment number..."
        >
          <x-slot name="iconTrailing">
            <flux:icon name="magnifying-glass" />
          </x-slot>
        </flux:input>
      </div>

      <!-- Status Filter -->
      <div>
        <flux:select wire:model.live="statusFilter">
          <option value="all">All Status</option>
          <option value="pending">Pending</option>
          <option value="confirmed">Confirmed</option>
          <option value="checked_in">Checked In</option>
          <option value="in_progress">In Progress</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
          <option value="no_show">No Show</option>
        </flux:select>
      </div>

      <!-- Service Filter -->
      <div>
        <flux:select wire:model.live="serviceFilter">
          <option value="all">All Services</option>
          @foreach($services as $service)
            <option value="{{ $service->id }}">{{ $service->name }}</option>
          @endforeach
        </flux:select>
      </div>
    </div>

    <div class="mt-4 flex items-center gap-4">
      <!-- Date Filter -->
      <div class="flex items-center gap-2">
        <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Date:</flux:text>
        <div class="flex gap-2">
          <flux:button
            wire:click="$set('dateFilter', 'all')"
            variant="{{ $dateFilter === 'all' ? 'primary' : 'ghost' }}"
            size="sm"
          >
            All
          </flux:button>
          <flux:button
            wire:click="$set('dateFilter', 'today')"
            variant="{{ $dateFilter === 'today' ? 'primary' : 'ghost' }}"
            size="sm"
          >
            Today
          </flux:button>
          <flux:button
            wire:click="$set('dateFilter', 'tomorrow')"
            variant="{{ $dateFilter === 'tomorrow' ? 'primary' : 'ghost' }}"
            size="sm"
          >
            Tomorrow
          </flux:button>
          <flux:button
            wire:click="$set('dateFilter', 'this_week')"
            variant="{{ $dateFilter === 'this_week' ? 'primary' : 'ghost' }}"
            size="sm"
          >
            This Week
          </flux:button>
          <flux:button
            wire:click="$set('dateFilter', 'this_month')"
            variant="{{ $dateFilter === 'this_month' ? 'primary' : 'ghost' }}"
            size="sm"
          >
            This Month
          </flux:button>
        </div>
      </div>
    </div>
  </div>

  <!-- Appointments Table -->
  <div class="rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
          <tr>
            <th class="px-6 py-3 text-left">
              <flux:text size="sm" weight="semibold" class="text-zinc-600 dark:text-zinc-400">Appt #</flux:text>
            </th>
            <th class="px-6 py-3 text-left">
              <flux:text size="sm" weight="semibold" class="text-zinc-600 dark:text-zinc-400">Patient</flux:text>
            </th>
            <th class="px-6 py-3 text-left">
              <flux:text size="sm" weight="semibold" class="text-zinc-600 dark:text-zinc-400">Service</flux:text>
            </th>
            <th class="px-6 py-3 text-left">
              <flux:text size="sm" weight="semibold" class="text-zinc-600 dark:text-zinc-400">Date & Time</flux:text>
            </th>
            <th class="px-6 py-3 text-left">
              <flux:text size="sm" weight="semibold" class="text-zinc-600 dark:text-zinc-400">Queue</flux:text>
            </th>
            <th class="px-6 py-3 text-left">
              <flux:text size="sm" weight="semibold" class="text-zinc-600 dark:text-zinc-400">Doctor</flux:text>
            </th>
            <th class="px-6 py-3 text-left">
              <flux:text size="sm" weight="semibold" class="text-zinc-600 dark:text-zinc-400">Status</flux:text>
            </th>
            <th class="px-6 py-3 text-left">
              <flux:text size="sm" weight="semibold" class="text-zinc-600 dark:text-zinc-400">Actions</flux:text>
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
          @forelse($appointments as $appointment)
            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
              <td class="px-6 py-4">
                <flux:text size="sm" weight="semibold">{{ $appointment->appointment_number }}</flux:text>
              </td>
              <td class="px-6 py-4">
                <div>
                  <flux:text size="sm" weight="semibold">{{ $appointment->patient->user->name ?? $appointment->patient->full_name }}</flux:text>
                  <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">{{ $appointment->patient->user->email ?? $appointment->patient->contact_number }}</flux:text>
                </div>
              </td>
              <td class="px-6 py-4">
                <flux:text size="sm">{{ $appointment->service->name }}</flux:text>
              </td>
              <td class="px-6 py-4">
                <div>
                  <flux:text size="sm" weight="semibold">{{ $appointment->scheduled_at->format('M d, Y') }}</flux:text>
                  <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">{{ $appointment->scheduled_at->format('g:i A') }}</flux:text>
                </div>
              </td>
              <td class="px-6 py-4">
                <flux:badge variant="ghost" size="sm">#{{ $appointment->queue_number }}</flux:badge>
              </td>
              <td class="px-6 py-4">
                @if($appointment->doctor)
                  <flux:text size="sm">Dr. {{ $appointment->doctor->user->name }}</flux:text>
                @else
                  <flux:text size="sm" class="text-zinc-400 dark:text-zinc-600">Not assigned</flux:text>
                @endif
              </td>
              <td class="px-6 py-4">
                @if($appointment->status === 'pending')
                  <flux:badge color="yellow" size="sm">Pending</flux:badge>
                @elseif($appointment->status === 'confirmed')
                  <flux:badge color="blue" size="sm">Confirmed</flux:badge>
                @elseif($appointment->status === 'checked_in')
                  <flux:badge color="indigo" size="sm">Checked In</flux:badge>
                @elseif($appointment->status === 'in_progress')
                  <flux:badge color="purple" size="sm">In Progress</flux:badge>
                @elseif($appointment->status === 'completed')
                  <flux:badge color="green" size="sm">Completed</flux:badge>
                @elseif($appointment->status === 'cancelled')
                  <flux:badge color="red" size="sm">Cancelled</flux:badge>
                @elseif($appointment->status === 'no_show')
                  <flux:badge color="red" size="sm">No Show</flux:badge>
                @endif
              </td>
              <td class="px-6 py-4">
                <flux:button icon="eye" wire:click="viewDetails({{ $appointment->id }})" variant="ghost" size="sm">
                    View
                </flux:button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-6 py-12 text-center">
                <flux:icon name="calendar" class="h-12 w-12 text-zinc-400 dark:text-zinc-600 mx-auto mb-2" />
                <flux:text class="text-zinc-600 dark:text-zinc-400">No appointments found</flux:text>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    @if($appointments->hasPages())
      <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
        {{ $appointments->links() }}
      </div>
    @endif
  </div>

  <!-- Details Modal -->
  @if($showDetailsModal && $selectedAppointment)
    <flux:modal wire:model="showDetailsModal" variant="flyout">
      <flux:heading size="lg" class="mb-4">Appointment Details</flux:heading>

      <div class="space-y-6">
        <!-- Appointment Info -->
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
          <flux:subheading class="mb-3">Appointment Information</flux:subheading>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Appointment Number</flux:text>
              <flux:text weight="semibold">{{ $selectedAppointment->appointment_number }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Queue Number</flux:text>
              <flux:text weight="semibold">#{{ $selectedAppointment->queue_number }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Date</flux:text>
              <flux:text weight="semibold">{{ $selectedAppointment->scheduled_at->format('M d, Y') }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Time</flux:text>
              <flux:text weight="semibold">{{ $selectedAppointment->scheduled_at->format('g:i A') }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Service</flux:text>
              <flux:text weight="semibold">{{ $selectedAppointment->service->name }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Status</flux:text>
              @if($selectedAppointment->status === 'completed')
                <flux:badge variant="success">Completed</flux:badge>
              @elseif($selectedAppointment->status === 'cancelled')
                <flux:badge variant="danger">Cancelled</flux:badge>
              @else
                <flux:badge variant="primary">{{ ucfirst($selectedAppointment->status) }}</flux:badge>
              @endif
            </div>
          </div>
        </div>

        <!-- Patient Info -->
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
          <flux:subheading class="mb-3">Patient Information</flux:subheading>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Name</flux:text>
              <flux:text weight="semibold">{{ $selectedAppointment->patient->user->name ?? $selectedAppointment->patient->full_name }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Email</flux:text>
              <flux:text weight="semibold">{{ $selectedAppointment->patient->user->email ?? 'N/A' }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Contact</flux:text>
              <flux:text weight="semibold">{{ $selectedAppointment->patient->contact_number ?? $selectedAppointment->patient->user->contact_number ?? 'N/A' }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Barangay</flux:text>
              <flux:text weight="semibold">{{ $selectedAppointment->patient->barangay->name ?? 'N/A' }}</flux:text>
            </div>
          </div>
        </div>

        <!-- Doctor Info -->
        @if($selectedAppointment->doctor)
          <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <flux:subheading class="mb-3">Doctor Information</flux:subheading>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Name</flux:text>
                <flux:text weight="semibold">Dr. {{ $selectedAppointment->doctor->user->name }}</flux:text>
              </div>
              <div>
                <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Specialization</flux:text>
                <flux:text weight="semibold">{{ $selectedAppointment->doctor->specialization ?? 'General' }}</flux:text>
              </div>
            </div>
          </div>
        @endif

        <!-- Timestamps -->
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
          <flux:subheading class="mb-3">Timeline</flux:subheading>
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Created</flux:text>
              <flux:text size="sm">{{ $selectedAppointment->created_at->format('M d, Y g:i A') }}</flux:text>
            </div>
            @if($selectedAppointment->check_in_at)
              <div class="flex items-center justify-between">
                <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Checked In</flux:text>
                <flux:text size="sm">{{ $selectedAppointment->check_in_at->format('M d, Y g:i A') }}</flux:text>
              </div>
            @endif
            @if($selectedAppointment->started_at)
              <div class="flex items-center justify-between">
                <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Started</flux:text>
                <flux:text size="sm">{{ $selectedAppointment->started_at->format('M d, Y g:i A') }}</flux:text>
              </div>
            @endif
            @if($selectedAppointment->completed_at)
              <div class="flex items-center justify-between">
                <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Completed</flux:text>
                <flux:text size="sm">{{ $selectedAppointment->completed_at->format('M d, Y g:i A') }}</flux:text>
              </div>
            @endif
          </div>
        </div>

        @if($selectedAppointment->notes)
          <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <flux:subheading class="mb-3">Notes</flux:subheading>
            <flux:text size="sm">{{ $selectedAppointment->notes }}</flux:text>
          </div>
        @endif

        @if($selectedAppointment->cancellation_reason)
          <div class="rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-4">
            <flux:subheading class="mb-3 text-red-800 dark:text-red-200">Cancellation Reason</flux:subheading>
            <flux:text size="sm" class="text-red-700 dark:text-red-300">{{ $selectedAppointment->cancellation_reason }}</flux:text>
          </div>
        @endif
      </div>

      <div class="mt-6 flex justify-end">
        <flux:button wire:click="closeDetailsModal" variant="ghost">Close</flux:button>
      </div>
    </flux:modal>
  @endif
</div>
