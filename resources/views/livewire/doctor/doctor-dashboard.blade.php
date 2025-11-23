<div class="max-w-7xl space-y-6">
  @if(!empty($doctorMissing) && $doctorMissing)
    <flux:callout variant="danger" title="Doctor Profile Missing" class="border-red-300">
      <p class="text-sm">Your user account does not have an associated doctor profile yet. Please contact an administrator to complete your onboarding. Dashboard statistics and queues will appear once your profile is created.</p>
    </flux:callout>
  @endif
  <!-- Welcome Header -->
  <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
    <div class="flex items-center justify-between">
      <div class="space-y-2">
        <flux:heading size="xl" class="text-zinc-900 dark:text-white">
          {{ __('Welcome, Dr. :name', ['name' => Auth::user()->name]) }}
        </flux:heading>
        <flux:badge size="lg" color="blue">
          {{ ucwords(str_replace('_', ' ', Auth::user()->role->name)) }}
        </flux:badge>
      </div>

      <div class="flex items-center gap-3">
        <div class="text-right">
          <flux:subheading>{{ __('Today') }}</flux:subheading>
          <flux:text class="font-semibold">
            {{ now()->format('M j, Y') }}
          </flux:text>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <flux:subheading class="mb-2 text-zinc-600 dark:text-zinc-400">Today's Appointments</flux:subheading>
          <flux:heading size="xl" class="text-zinc-900 dark:text-white mb-2">
            {{ $stats['today_total'] }}
          </flux:heading>
          <flux:badge variant="primary" size="sm">
            {{ $stats['completed_today'] }} completed
          </flux:badge>
        </div>
        <div class="flex h-12 w-12 items-center justify-center rounded-lg text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20">
          <flux:icon name="calendar" class="h-6 w-6" />
        </div>
      </div>
    </div>

    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <flux:subheading class="mb-2 text-zinc-600 dark:text-zinc-400">In Queue</flux:subheading>
          <flux:heading size="xl" class="text-zinc-900 dark:text-white mb-2">
            {{ $stats['in_queue'] }}
          </flux:heading>
          <flux:badge variant="warning" size="sm">
            Waiting patients
          </flux:badge>
        </div>
        <div class="flex h-12 w-12 items-center justify-center rounded-lg text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20">
          <flux:icon name="clock" class="h-6 w-6" />
        </div>
      </div>
    </div>

    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <flux:subheading class="mb-2 text-zinc-600 dark:text-zinc-400">Patients This Week</flux:subheading>
          <flux:heading size="xl" class="text-zinc-900 dark:text-white mb-2">
            {{ $stats['patients_seen_week'] }}
          </flux:heading>
          <flux:badge variant="success" size="sm">
            Unique patients
          </flux:badge>
        </div>
        <div class="flex h-12 w-12 items-center justify-center rounded-lg text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20">
          <flux:icon name="users" class="h-6 w-6" />
        </div>
      </div>
    </div>

    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <flux:subheading class="mb-2 text-zinc-600 dark:text-zinc-400">No Shows</flux:subheading>
          <flux:heading size="xl" class="text-zinc-900 dark:text-white mb-2">
            {{ $stats['no_shows'] }}
          </flux:heading>
          <flux:badge variant="danger" size="sm">
            Today
          </flux:badge>
        </div>
        <div class="flex h-12 w-12 items-center justify-center rounded-lg text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20">
          <flux:icon name="exclamation-circle" class="h-6 w-6" />
        </div>
      </div>
    </div>
  </div>

  <!-- Patient Search -->
  <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
    <flux:heading size="md" class="mb-4">Quick Patient Search</flux:heading>
    <div class="relative">
      <flux:input
        wire:model.live.debounce.300ms="searchPatient"
        placeholder="Search by name, email, or contact number..."
        class="w-full"
      >
        <x-slot name="iconTrailing">
          <flux:icon name="magnifying-glass" />
        </x-slot>
      </flux:input>

      @if($searchResults->count() > 0)
        <div class="absolute z-10 mt-2 w-full rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 shadow-lg max-h-96 overflow-y-auto">
          @foreach($searchResults as $result)
            <button
              wire:click="viewPatient({{ $result->id }})"
              class="w-full px-4 py-3 text-left hover:bg-zinc-50 dark:hover:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 last:border-b-0"
            >
              <div class="flex items-center justify-between">
                <div>
                  <flux:text weight="semibold">{{ $result->user->name }}</flux:text>
                  <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">{{ $result->user->email }}</flux:text>
                  <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">{{ $result->barangay->name ?? 'N/A' }}</flux:text>
                </div>
                <flux:icon name="chevron-right" size="sm" class="text-zinc-400" />
              </div>
            </button>
          @endforeach
        </div>
      @elseif(strlen($searchPatient) >= 2)
        <div class="absolute z-10 mt-2 w-full rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 shadow-lg p-4">
          <flux:text class="text-zinc-600 dark:text-zinc-400">No patients found</flux:text>
        </div>
      @endif
    </div>
  </div>

  <!-- Current Appointment in Progress -->
  @if($currentAppointment)
    <div class="rounded-lg border-2 border-blue-500 bg-blue-50 dark:bg-blue-900/20 p-6 shadow-md">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
          <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-white">
            <flux:icon name="user" class="h-6 w-6" />
          </div>
          <div>
            <flux:heading size="md">Current Patient</flux:heading>
            <flux:badge variant="primary" size="sm">In Progress</flux:badge>
          </div>
        </div>
        <flux:button href="{{ route('doctor.medical-records.create', $currentAppointment->id) }}" variant="primary">
          <flux:icon name="document-text" /> Add Medical Record
        </flux:button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-1">Patient Name</flux:text>
          <flux:text weight="semibold" size="lg">{{ $currentAppointment->patient->user->name }}</flux:text>
        </div>
        <div>
          <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-1">Queue Number</flux:text>
          <flux:text weight="semibold" size="lg">#{{ $currentAppointment->queue_number }}</flux:text>
        </div>
        <div>
          <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-1">Service</flux:text>
          <flux:text weight="semibold">{{ $currentAppointment->service->name }}</flux:text>
        </div>
        <div>
          <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-1">Barangay</flux:text>
          <flux:text weight="semibold">{{ $currentAppointment->patient->barangay->name ?? 'N/A' }}</flux:text>
        </div>
      </div>
    </div>
  @endif

  <!-- Today's Appointments Queue -->
  <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
    <flux:heading size="md" class="mb-4">Today's Appointment Queue</flux:heading>

    @if($todayAppointments->count() > 0)
      <div class="space-y-3">
        @foreach($todayAppointments as $appointment)
          <div class="flex items-center gap-4 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 {{ $appointment->status === 'in_progress' ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-500' : 'hover:bg-zinc-50 dark:hover:bg-zinc-800' }} transition-colors">
            <!-- Queue Number -->
            <div class="flex h-12 w-12 items-center justify-center rounded-full {{ $appointment->status === 'completed' ? 'bg-green-100 dark:bg-green-900/30' : ($appointment->status === 'in_progress' ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-zinc-100 dark:bg-zinc-800') }}">
              <flux:text weight="bold" size="lg" class="{{ $appointment->status === 'completed' ? 'text-green-600 dark:text-green-400' : ($appointment->status === 'in_progress' ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-600 dark:text-zinc-400') }}">
                {{ $appointment->queue_number }}
              </flux:text>
            </div>

            <!-- Patient Info -->
            <div class="flex-1">
              <flux:text weight="semibold">{{ $appointment->patient->user->name }}</flux:text>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">{{ $appointment->service->name }}</flux:text>
              <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">
                {{ $appointment->scheduled_at->format('g:i A') }}
                @if($appointment->check_in_at)
                  • Checked in {{ $appointment->check_in_at->diffForHumans() }}
                @endif
              </flux:text>
            </div>

            <!-- Status Badge -->
            <div>
              @if($appointment->status === 'confirmed')
                <flux:badge variant="warning">Confirmed</flux:badge>
              @elseif($appointment->status === 'checked_in')
                <flux:badge variant="primary">Checked In</flux:badge>
              @elseif($appointment->status === 'in_progress')
                <flux:badge variant="primary">In Progress</flux:badge>
              @elseif($appointment->status === 'completed')
                <flux:badge variant="success">Completed</flux:badge>
              @elseif($appointment->status === 'no_show')
                <flux:badge variant="danger">No Show</flux:badge>
              @endif
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
              @if($appointment->status === 'confirmed' || $appointment->status === 'checked_in')
                @if($appointment->status === 'confirmed')
                  <flux:button wire:click="checkInPatient({{ $appointment->id }})" variant="primary" size="sm">
                    <flux:icon name="check" /> Check In
                  </flux:button>
                @endif

                @if($appointment->status === 'checked_in')
                  <flux:button wire:click="startConsultation({{ $appointment->id }})" variant="primary" size="sm">
                    <flux:icon name="play" /> Start
                  </flux:button>
                @endif
              @endif

              @if($appointment->status === 'in_progress' || $appointment->status === 'completed')
                <flux:button href="{{ route('doctor.medical-records.create', $appointment->id) }}" variant="ghost" size="sm">
                  <flux:icon name="document-text" /> Records
                </flux:button>
              @endif

              <flux:button wire:click="viewPatient({{ $appointment->patient->id }})" variant="ghost" size="sm">
                <flux:icon name="user" /> View
              </flux:button>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-8">
        <flux:icon name="calendar" size="lg" class="text-zinc-400 dark:text-zinc-600 mx-auto mb-2" />
        <flux:text class="text-zinc-600 dark:text-zinc-400">No appointments scheduled for today</flux:text>
      </div>
    @endif
  </div>

  <!-- Two Column Layout -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Upcoming Appointments -->
    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
      <flux:heading size="md" class="mb-4">Upcoming Appointments</flux:heading>
      <div class="space-y-3">
        @forelse($upcomingAppointments as $appointment)
          <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
              <flux:icon name="calendar" size="sm" class="text-blue-600 dark:text-blue-400" />
            </div>
            <div class="flex-1 min-w-0">
              <flux:text weight="semibold" class="truncate">{{ $appointment->patient->user->name }}</flux:text>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">{{ $appointment->service->name }}</flux:text>
              <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">
                {{ $appointment->scheduled_at->format('M d, Y g:i A') }}
              </flux:text>
            </div>
            <flux:badge variant="ghost" size="sm">Q{{ $appointment->queue_number }}</flux:badge>
          </div>
        @empty
          <flux:text class="text-zinc-600 dark:text-zinc-400 text-center py-4">No upcoming appointments</flux:text>
        @endforelse
      </div>
    </div>

    <!-- Recent Completed -->
    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
      <flux:heading size="md" class="mb-4">Recently Completed</flux:heading>
      <div class="space-y-3">
        @forelse($recentCompleted as $appointment)
          <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
              <flux:icon name="check-circle" size="sm" class="text-green-600 dark:text-green-400" />
            </div>
            <div class="flex-1 min-w-0">
              <flux:text weight="semibold" class="truncate">{{ $appointment->patient->user->name }}</flux:text>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">{{ $appointment->service->name }}</flux:text>
              <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">
                {{ $appointment->completed_at->diffForHumans() }}
              </flux:text>
            </div>
            <flux:button href="{{ route('doctor.medical-records.create', $appointment->id) }}" variant="ghost" size="sm">
              View
            </flux:button>
          </div>
        @empty
          <flux:text class="text-zinc-600 dark:text-zinc-400 text-center py-4">No completed appointments yet</flux:text>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Patient Details Modal -->
  @if($showPatientModal && $selectedPatient)
    <flux:modal wire:model="showPatientModal" variant="flyout">
      <flux:heading size="lg" class="mb-4">Patient Details</flux:heading>

      <div class="space-y-4">
        <!-- Patient Info -->
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
          <flux:subheading class="mb-3">Personal Information</flux:subheading>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Name</flux:text>
              <flux:text weight="semibold">{{ $selectedPatient->user->name }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Email</flux:text>
              <flux:text weight="semibold">{{ $selectedPatient->user->email }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Contact</flux:text>
              <flux:text weight="semibold">{{ $selectedPatient->contact_number }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Barangay</flux:text>
              <flux:text weight="semibold">{{ $selectedPatient->barangay->name ?? 'N/A' }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Blood Type</flux:text>
              <flux:text weight="semibold">{{ $selectedPatient->blood_type ?? 'N/A' }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Date of Birth</flux:text>
              <flux:text weight="semibold">{{ $selectedPatient->date_of_birth ? \Carbon\Carbon::parse($selectedPatient->date_of_birth)->format('M d, Y') : 'N/A' }}</flux:text>
            </div>
          </div>
        </div>

        <!-- Recent Appointments -->
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
          <flux:subheading class="mb-3">Recent Appointments</flux:subheading>
          <div class="space-y-2">
            @forelse($selectedPatient->appointments->take(5) as $apt)
              <div class="flex items-center justify-between p-2 rounded hover:bg-zinc-50 dark:hover:bg-zinc-800">
                <div>
                  <flux:text size="sm" weight="semibold">{{ $apt->service->name }}</flux:text>
                  <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">{{ $apt->scheduled_at->format('M d, Y') }}</flux:text>
                </div>
                <flux:badge variant="{{ $apt->status === 'completed' ? 'success' : 'ghost' }}" size="sm">
                  {{ ucfirst($apt->status) }}
                </flux:badge>
              </div>
            @empty
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">No appointments found</flux:text>
            @endforelse
          </div>
        </div>

        <!-- Medical Records Count -->
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
          <flux:subheading class="mb-3">Medical Records</flux:subheading>
          <flux:text>Total Records: {{ $selectedPatient->medicalRecords->count() }}</flux:text>
          <flux:button href="{{ route('doctor.patients.records', $selectedPatient->id) }}" variant="primary" size="sm" class="mt-2">
            View All Records
          </flux:button>
        </div>
      </div>

      <div class="mt-6 flex justify-end">
        <flux:button wire:click="closePatientModal" variant="ghost">Close</flux:button>
      </div>
    </flux:modal>
  @endif
</div>
