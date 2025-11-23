<div class="min-h-screen">
  <div class="space-y-6">
    <!-- Welcome Section -->
    <div class="mb-8">
      <flux:heading size="lg" class="text-zinc-900 dark:text-white mb-2">
        {{ __('Welcome back, :name', ['name' => Auth::user()->name]) }}
      </flux:heading>
      <flux:text class="text-zinc-600 dark:text-zinc-400">
        {{ __('Here\'s an overview of your healthcare information') }}
      </flux:text>
    </div>

    @if ($profileIncomplete ?? false)
      <!-- Profile Incomplete Alert -->
      <flux:callout variant="warning" icon="exclamation-triangle" class="mb-8">
        <flux:heading size="lg" class="mb-2">Complete Your Profile</flux:heading>
        <flux:text class="mb-4">
          Your patient profile is incomplete. Please complete your profile to book appointments and access all features.
        </flux:text>
        <flux:button href="{{ route('patient.profile') }}" variant="primary">
          Complete Profile
        </flux:button>
      </flux:callout>
    @endif

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Total Appointments -->
      <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <flux:subheading class="mb-2 text-zinc-600 dark:text-zinc-400">Total Appointments</flux:subheading>
            <flux:heading size="xl" class="text-zinc-900 dark:text-white">
              {{ $stats['total_appointments'] }}
            </flux:heading>
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-500">
              {{ $stats['completed_appointments'] }} completed
            </flux:text>
          </div>
          <div
            class="flex h-12 w-12 items-center justify-center rounded-lg text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20">
            <flux:icon name="calendar" class="h-6 w-6" />
          </div>
        </div>
      </div>

      <!-- Upcoming Appointments -->
      <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <flux:subheading class="mb-2 text-zinc-600 dark:text-zinc-400">Upcoming</flux:subheading>
            <flux:heading size="xl" class="text-zinc-900 dark:text-white">
              {{ $stats['upcoming_appointments'] }}
            </flux:heading>
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-500">
              Scheduled visits
            </flux:text>
          </div>
          <div
            class="flex h-12 w-12 items-center justify-center rounded-lg text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20">
            <flux:icon name="clock" class="h-6 w-6" />
          </div>
        </div>
      </div>

      <!-- Medical Records -->
      <div class="hidden rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <flux:subheading class="mb-2 text-zinc-600 dark:text-zinc-400">Medical Records</flux:subheading>
            <flux:heading size="xl" class="text-zinc-900 dark:text-white">
              {{ $stats['medical_records'] }}
            </flux:heading>
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-500">
              Total records
            </flux:text>
          </div>
          <div
            class="flex h-12 w-12 items-center justify-center rounded-lg text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20">
            <flux:icon name="document-text" class="h-6 w-6" />
          </div>
        </div>
      </div>

      <!-- Notifications -->
      <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <flux:subheading class="mb-2 text-zinc-600 dark:text-zinc-400">Notifications</flux:subheading>
            <flux:heading size="xl" class="text-zinc-900 dark:text-white">
              {{ $stats['unread_notifications'] }}
            </flux:heading>
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-500">
              Unread messages
            </flux:text>
          </div>
          <div
            class="flex h-12 w-12 items-center justify-center rounded-lg text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20">
            <flux:icon name="bell" class="h-6 w-6" />
          </div>
        </div>
      </div>
    </div>

    <!-- Next Appointment Card -->
    @if ($nextAppointment)
      <div
        class="mb-8 rounded-lg border-2 border-blue-500 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-6 shadow-md">
        <div class="flex items-start justify-between mb-4">
          <div>
            <flux:heading size="lg" class="mb-1">Your Next Appointment</flux:heading>
            <flux:badge variant="primary" size="lg">
              Queue #{{ $nextAppointment->queue_number }}
            </flux:badge>
          </div>
          <flux:button href="{{ route('patient.appointments.details', $nextAppointment) }}" variant="primary" color="blue" wire:navigate>
            View Details
          </flux:button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-1">Date & Time</flux:text>
            <flux:text weight="semibold" size="lg">{{ $nextAppointment->scheduled_at->format('M d, Y') }}
            </flux:text>
            <flux:text class="text-zinc-700 dark:text-zinc-300">{{ $nextAppointment->scheduled_at->format('g:i A') }}
            </flux:text>
          </div>
          <div>
            <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-1">Service</flux:text>
            <flux:text weight="semibold" size="lg">{{ $nextAppointment->service->name }}</flux:text>
          </div>
          <div>
            <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-1">Status</flux:text>
            @if ($nextAppointment->status === 'confirmed')
              <flux:badge variant="success" size="lg">Confirmed</flux:badge>
            @elseif($nextAppointment->status === 'pending')
              <flux:badge variant="warning" size="lg">Pending</flux:badge>
            @elseif($nextAppointment->status === 'checked_in')
              <flux:badge variant="primary" size="lg">Checked In</flux:badge>
            @endif
          </div>
        </div>

        @if ($nextAppointment->scheduled_at->diffInDays(now()) <= 3)
          <div
            class="mt-4 p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800">
            <div class="flex items-center gap-2">
              <flux:icon name="exclamation-triangle" class="text-yellow-600 dark:text-yellow-400" />
              <flux:text size="sm" weight="semibold" class="text-yellow-800 dark:text-yellow-200">
                Your appointment is in {{ $nextAppointment->scheduled_at->diffForHumans() }}. Please arrive 15 minutes
                early.
              </flux:text>
            </div>
          </div>
        @endif
      </div>
    @endif

    <!-- Quick Actions -->
    <div class="mb-8">
      <flux:heading size="lg" class="mb-4">Quick Actions</flux:heading>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <flux:button href="{{ route('patient.book-appointment') }}" variant="primary" color="cyan" class="h-auto py-4"
          wire:navigate>
          <div class="flex flex-col items-center gap-2">
            <flux:icon name="calendar" class="h-8 w-8" />
            <span>Book Appointment</span>
          </div>
        </flux:button>

        <flux:button href="{{ route('patient.health-card') }}" variant="ghost" class="h-auto py-4 hidden!" wire:navigate>
          <div class="flex flex-col items-center gap-2">
            <flux:icon name="identification" class="h-8 w-8" />
            <span>View Health Card</span>
          </div>
        </flux:button>

        <flux:button href="{{ route('patient.feedback') }}" variant="ghost" class="h-auto py-4" wire:navigate>
          <div class="flex flex-col items-center gap-2">
            <flux:icon name="star" class="h-8 w-8" />
            <span>Give Feedback</span>
          </div>
        </flux:button>
      </div>
    </div>

    <!-- Upcoming Appointments -->
    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <flux:heading size="lg">Upcoming Appointments</flux:heading>
        <flux:button href="{{ route('patient.appointments.list') }}" variant="ghost" size="sm" wire:navigate>
          View All
        </flux:button>
      </div>

      <div class="space-y-3">
        @forelse($upcomingAppointments as $appointment)
          <div
            class="flex items-start gap-3 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
              <flux:text weight="bold" class="text-blue-600 dark:text-blue-400">
                {{ $appointment->queue_number }}
              </flux:text>
            </div>
            <div class="flex-1 min-w-0">
              <flux:text weight="semibold">{{ $appointment->service->name }}</flux:text>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                {{ $appointment->scheduled_at->format('M d, Y • g:i A') }}
              </flux:text>
              @if ($appointment->doctor)
                <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">
                  Dr. {{ $appointment->doctor->user->name }}
                </flux:text>
              @endif
            </div>
            <flux:badge variant="{{ $appointment->status === 'confirmed' ? 'success' : 'warning' }}" size="sm">
              {{ ucfirst($appointment->status) }}
            </flux:badge>
          </div>
        @empty
          <div class="text-center py-8">
            <flux:icon name="calendar" class="h-12 w-12 text-zinc-400 dark:text-zinc-600 mx-auto mb-2" />
            <flux:text class="text-zinc-600 dark:text-zinc-400">No upcoming appointments</flux:text>
            <flux:button href="{{ route('patient.book-appointment') }}" variant="primary" size="sm" color="blue"
              class="mt-3" wire:navigate>
              Book Appointment
            </flux:button>
          </div>
        @endforelse
      </div>
    </div>

    <!-- Two Column Layout -->
    <div class="hidden grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Health Card Section -->
      @if ($healthCard)
        <div
          class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <flux:heading size="lg">My Health Card</flux:heading>
            <flux:button href="{{ route('patient.health-card') }}" variant="ghost" size="sm" wire:navigate>
              View Full Card
            </flux:button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-1">Card Number</flux:text>
                  <flux:text weight="semibold">{{ $healthCard->card_number }}</flux:text>
                </div>
                <div>
                  <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-1">Status</flux:text>
                  <flux:badge variant="success">{{ ucfirst($healthCard->status) }}</flux:badge>
                </div>
                <div>
                  <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-1">Issue Date</flux:text>
                  <flux:text weight="semibold">{{ $healthCard->issue_date->format('M d, Y') }}</flux:text>
                </div>
                <div>
                  <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-1">Expiry Date</flux:text>
                  <flux:text weight="semibold">{{ $healthCard->expiry_date->format('M d, Y') }}</flux:text>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-center">
              <div class="p-4 rounded-lg bg-zinc-100 dark:bg-zinc-800">
                <flux:text size="sm" class="text-center text-zinc-600 dark:text-zinc-400 mb-2">QR Code
                </flux:text>
                <div class="w-32 h-32 bg-white dark:bg-white rounded flex items-center justify-center">
                  <flux:icon name="qr-code" class="h-24 w-24 text-zinc-900" />
                </div>
              </div>
            </div>
          </div>
        </div>
      @else
        <div
          class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm text-center">
          <flux:icon name="identification" class="h-16 w-16 text-zinc-400 dark:text-zinc-600 mx-auto mb-4" />
          <flux:heading size="lg" class="mb-2">No Health Card Yet</flux:heading>
          <flux:text class="text-zinc-600 dark:text-zinc-400 mb-4">
            Your health card will be issued after your first appointment.
          </flux:text>
          <flux:button href="{{ route('patient.book-appointment') }}" variant="primary" color="blue" wire:navigate>
            Book Your First Appointment
          </flux:button>
        </div>
      @endif

      <!-- Recent Medical Records -->
      <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
        <flux:heading size="lg" class="mb-4">Recent Medical Records</flux:heading>

        <div class="space-y-3">
          @forelse($recentMedicalRecords as $record)
            <div
              class="flex items-start gap-3 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
              <div class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30">
                <flux:icon name="document-text" size="sm" class="text-purple-600 dark:text-purple-400" />
              </div>
              <div class="flex-1 min-w-0">
                <flux:text weight="semibold">{{ ucfirst(str_replace('_', ' ', $record->category)) }}</flux:text>
                <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                  Dr. {{ $record->doctor->user->name }}
                </flux:text>
                <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">
                  {{ $record->created_at->format('M d, Y') }}
                </flux:text>
              </div>
              <flux:badge variant="ghost" size="sm">{{ ucfirst($record->template_type) }}</flux:badge>
            </div>
          @empty
            <div class="text-center py-8">
              <flux:icon name="document-text" class="h-12 w-12 text-zinc-400 dark:text-zinc-600 mx-auto mb-2" />
              <flux:text class="text-zinc-600 dark:text-zinc-400">No medical records yet</flux:text>
            </div>
          @endforelse
        </div>
      </div>
    </div>

    <!-- Recent Completed Appointments -->
    @if ($recentAppointments->count() > 0)
      <div class="mt-8 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
        <flux:heading size="lg" class="mb-4">Recently Completed</flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          @foreach ($recentAppointments as $appointment)
            <div class="p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:shadow-md transition-shadow">
              <div class="flex items-center justify-between mb-2">
                <flux:badge variant="success" size="sm">Completed</flux:badge>
                <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">
                  {{ $appointment->completed_at->diffForHumans() }}
                </flux:text>
              </div>
              <flux:text weight="semibold" class="mb-1">{{ $appointment->service->name }}</flux:text>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                {{ $appointment->completed_at->format('M d, Y') }}
              </flux:text>
              @if ($appointment->doctor)
                <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">
                  Dr. {{ $appointment->doctor->user->name }}
                </flux:text>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</div>
