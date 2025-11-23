<div class="max-w-7xl space-y-6">
  <!-- Welcome Header -->
  <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
    <div class="flex items-center justify-between">
      <div class="space-y-2">
        <flux:heading size="xl" class="text-zinc-900 dark:text-white">
          {{ __('Welcome back, :name', ['name' => Auth::user()->name]) }}
        </flux:heading>
        <flux:badge size="lg" color="red">
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

  <!-- Main Statistics Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Total Patients -->
    <div
      class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm hover:shadow-md transition-shadow">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <flux:subheading class="mb-2 text-zinc-600 dark:text-zinc-400">Total Patients</flux:subheading>
          <flux:heading size="xl" class="text-zinc-900 dark:text-white mb-2">
            {{ number_format($stats['total_patients']) }}
          </flux:heading>
          @if ($stats['total_patients_growth'] > 0)
            <flux:badge variant="success" size="sm">
              <flux:icon name="arrow-up" size="xs" /> {{ $stats['total_patients_growth'] }}% this month
            </flux:badge>
          @elseif($stats['total_patients_growth'] < 0)
            <flux:badge variant="danger" size="sm">
              <flux:icon name="arrow-down" size="xs" /> {{ abs($stats['total_patients_growth']) }}% this month
            </flux:badge>
          @else
            <flux:badge variant="ghost" size="sm">No change</flux:badge>
          @endif
        </div>
        <div
          class="flex h-12 w-12 items-center justify-center rounded-lg text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20">
          <flux:icon name="users" class="h-6 w-6" />
        </div>
      </div>
    </div>

    <!-- Total Appointments -->
    <div
      class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm hover:shadow-md transition-shadow">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <flux:subheading class="mb-2 text-zinc-600 dark:text-zinc-400">Total Appointments</flux:subheading>
          <flux:heading size="xl" class="text-zinc-900 dark:text-white mb-2">
            {{ number_format($stats['total_appointments']) }}
          </flux:heading>
          @if ($stats['total_appointments_growth'] > 0)
            <flux:badge variant="success" size="sm">
              <flux:icon name="arrow-up" size="xs" /> {{ $stats['total_appointments_growth'] }}% this month
            </flux:badge>
          @elseif($stats['total_appointments_growth'] < 0)
            <flux:badge variant="danger" size="sm">
              <flux:icon name="arrow-down" size="xs" /> {{ abs($stats['total_appointments_growth']) }}% this month
            </flux:badge>
          @else
            <flux:badge variant="ghost" size="sm">No change</flux:badge>
          @endif
        </div>
        <div
          class="flex h-12 w-12 items-center justify-center rounded-lg text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20">
          <flux:icon name="calendar" class="h-6 w-6" />
        </div>
      </div>
    </div>

    <!-- Today's Appointments -->
    <div
      class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm hover:shadow-md transition-shadow">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <flux:subheading class="mb-2 text-zinc-600 dark:text-zinc-400">Today's Appointments</flux:subheading>
          <flux:heading size="xl" class="text-zinc-900 dark:text-white mb-2">
            {{ number_format($stats['today_appointments']) }}
          </flux:heading>
          <flux:badge variant="primary" size="sm">
            {{ $stats['completed_today'] }} completed
          </flux:badge>
        </div>
        <div
          class="flex h-12 w-12 items-center justify-center rounded-lg text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20">
          <flux:icon name="clock" class="h-6 w-6" />
        </div>
      </div>
    </div>

    <!-- Pending Appointments -->
    <div
      class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm hover:shadow-md transition-shadow">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <flux:subheading class="mb-2 text-zinc-600 dark:text-zinc-400">Pending Appointments</flux:subheading>
          <flux:heading size="xl" class="text-zinc-900 dark:text-white mb-2">
            {{ number_format($stats['pending_appointments']) }}
          </flux:heading>
          {{-- @if ($stats['pending_approvals'] > 0)
            <flux:button href="{{ route('admin.approvals') }}" variant="primary" size="sm" color="orange">
              Review Now
            </flux:button>
          @else
            <flux:badge variant="success" size="sm">All caught up!</flux:badge>
          @endif --}}
        </div>
        <div
          class="flex h-12 w-12 items-center justify-center rounded-lg text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20">
          <flux:icon name="calendar-days" class="h-6 w-6" />
        </div>
      </div>
    </div>
  </div>

  <!-- Secondary Statistics Grid -->
  <div class="hidden grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
      <div class="flex items-center gap-3">
        <div
          class="flex h-10 w-10 items-center justify-center rounded-lg text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20">
          <flux:icon name="identification" size="sm" />
        </div>
        <div>
          <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Health Cards</flux:text>
          <flux:text weight="bold" size="lg">{{ number_format($stats['active_health_cards']) }}</flux:text>
        </div>
      </div>
    </div>

    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
      <div class="flex items-center gap-3">
        <div
          class="flex h-10 w-10 items-center justify-center rounded-lg text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20">
          <flux:icon name="user-group" size="sm" />
        </div>
        <div>
          <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Doctors</flux:text>
          <flux:text weight="bold" size="lg">{{ number_format($stats['total_doctors']) }}</flux:text>
        </div>
      </div>
    </div>

    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
      <div class="flex items-center gap-3">
        <div
          class="flex h-10 w-10 items-center justify-center rounded-lg text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20">
          <flux:icon name="star" size="sm" />
        </div>
        <div>
          <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Avg Rating</flux:text>
          <flux:text weight="bold" size="lg">{{ $stats['average_feedback_rating'] }}/5</flux:text>
        </div>
      </div>
    </div>

    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
      <div class="flex items-center gap-3">
        <div
          class="flex h-10 w-10 items-center justify-center rounded-lg text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20">
          <flux:icon name="exclamation-triangle" size="sm" />
        </div>
        <div>
          <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Disease Cases</flux:text>
          <flux:text weight="bold" size="lg">{{ number_format($stats['disease_cases']) }}</flux:text>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts and Analytics Section -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Weekly Trend Chart -->
    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
      <flux:heading size="md" class="mb-4">Weekly Appointment Trend (All Appointments)</flux:heading>
      <div class="relative">
        <div class="h-64">
          <canvas id="weekly-appointments-chart" data-labels='@json(array_map(fn($d) => \Carbon\Carbon::parse($d)->format('D, M j'), array_keys($weeklyTrend)))'
            data-values='@json(array_values($weeklyTrend))'></canvas>
        </div>
        <!-- Accessible fallback summary -->
        {{-- <div class="mt-4 space-y-3">
          @foreach ($weeklyTrend as $date => $count)
            <div class="flex items-center justify-between">
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">{{ \Carbon\Carbon::parse($date)->format('D, M j') }}</flux:text>
              <flux:text size="sm" weight="semibold">{{ $count }}</flux:text>
            </div>
          @endforeach
        </div> --}}
      </div>
    </div>

    <!-- Recent Appointments -->
    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <flux:heading size="md">Recent Appointments</flux:heading>
        <flux:button href="{{ route('admin.dashboard') }}" variant="ghost" size="sm">View All</flux:button>
      </div>
      <div class="space-y-3">
        @forelse($recentAppointments as $appointment)
          <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
              <flux:icon name="calendar" size="sm" class="text-blue-600 dark:text-blue-400" />
            </div>
            <div class="flex-1 min-w-0">
              <flux:text weight="semibold" class="truncate">{{ $appointment->patient->fullName }}</flux:text>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">{{ $appointment->service->name }}
              </flux:text>
              <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">
                {{ $appointment->scheduled_at->format('M d, Y g:i A') }}</flux:text>
            </div>
            <flux:badge
              variant="{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'pending' ? 'warning' : 'primary') }}"
              size="sm">
              {{ ucfirst($appointment->status) }}
            </flux:badge>
          </div>
        @empty
          <flux:text class="text-zinc-600 dark:text-zinc-400 text-center py-4">No recent appointments</flux:text>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Recent Activities Section -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Top Services -->
    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
      <flux:heading size="md" class="mb-4">Top Services</flux:heading>
      <div class="space-y-4">
        @forelse($topServices as $index => $item)
          <div class="flex items-center gap-3">
            <div
              class="flex h-8 w-8 items-center justify-center rounded-full {{ $index === 0 ? 'bg-yellow-100 dark:bg-yellow-900/30' : 'bg-zinc-100 dark:bg-zinc-800' }}">
              <flux:text weight="bold"
                class="{{ $index === 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-zinc-600 dark:text-zinc-400' }}">
                {{ $index + 1 }}
              </flux:text>
            </div>
            <div class="flex-1">
              <flux:text weight="semibold">{{ $item->service->name }}</flux:text>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">{{ $item->count }} appointments
              </flux:text>
            </div>
          </div>
        @empty
          <flux:text class="text-zinc-600 dark:text-zinc-400">No services data available</flux:text>
        @endforelse
      </div>
    </div>

    <!-- Recent Feedback Section -->
    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <flux:heading size="md">Recent Feedback</flux:heading>
        <flux:button href="{{ route('admin.feedback') }}" variant="ghost" size="sm">View All</flux:button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($recentFeedback as $feedback)
          <div class="p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
              <flux:text weight="semibold">{{ $feedback->patient->fullName }}</flux:text>
              <div class="flex items-center gap-1">
                <flux:icon name="star" size="sm" class="text-yellow-400" />
                <flux:text weight="bold">{{ $feedback->overall_rating }}</flux:text>
              </div>
            </div>
            @if ($feedback->comments)
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 line-clamp-2 mb-2">
                "{{ $feedback->comments }}"
              </flux:text>
            @endif
            <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">
              {{ $feedback->created_at->diffForHumans() }}
            </flux:text>
          </div>
        @empty
          <flux:text class="text-zinc-600 dark:text-zinc-400 col-span-3 text-center py-4">No feedback yet</flux:text>
        @endforelse
      </div>
    </div>

    <!-- Pending Approvals List -->
    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm hidden">
      <div class="flex items-center justify-between mb-4">
        <flux:heading size="md">Pending Approvals</flux:heading>
        <flux:button href="{{ route('admin.approvals') }}" variant="ghost" size="sm">View All</flux:button>
      </div>
      <div class="space-y-3">
        @forelse($pendingApprovals as $user)
          <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/30">
              <flux:icon name="user" size="sm" class="text-orange-600 dark:text-orange-400" />
            </div>
            <div class="flex-1 min-w-0">
              <flux:text weight="semibold" class="truncate">{{ $user->name }}</flux:text>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">{{ $user->email }}</flux:text>
              <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">Registered
                {{ $user->created_at->diffForHumans() }}</flux:text>
            </div>
            <flux:button href="{{ route('admin.approvals') }}" variant="primary" size="sm" color="cyan">
              Review
            </flux:button>
          </div>
        @empty
          <flux:text class="text-zinc-600 dark:text-zinc-400 text-center py-4">No pending approvals</flux:text>
        @endforelse
      </div>
    </div>
  </div>
</div>
