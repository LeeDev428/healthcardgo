<div class="max-w-7xl">
    <div class="space-y-8">
        <!-- Enhanced Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" class="bg-linear-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent dark:from-blue-400 dark:to-purple-400">
                    {{ $adminCategory ? ucfirst($adminCategory) . ' Admin' : 'Healthcare Admin' }} Dashboard
                </flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">Welcome back! Here's your overview for {{ now()->format('l, F j, Y') }}</flux:text>
            </div>
            <div class="hidden sm:block">
                <flux:badge size="lg" variant="outline" class="font-medium">
                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ now()->format('g:i A') }}
                </flux:badge>
            </div>
        </div>

        <!-- Enhanced Statistics Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Pending Approvals -->
            <div class="hidden group relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 transition duration-300 hover:shadow-lg dark:bg-zinc-800 dark:ring-zinc-700">
                <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-yellow-500/10"></div>
                <div class="relative flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-linear-to-br from-yellow-400 to-yellow-600 shadow-lg shadow-yellow-500/30">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <flux:text class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Pending Approvals</flux:text>
                            <div class="mt-2 flex items-baseline gap-2">
                                <flux:heading size="2xl" class="font-bold text-zinc-900 dark:text-white">{{ number_format($statistics['pending_approvals']) }}</flux:heading>
                                @if($statistics['pending_approvals'] > 0)
                                    <flux:badge variant="warning" size="sm">Needs Attention</flux:badge>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Patients -->
            <div class="group relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 transition duration-300 hover:shadow-lg dark:bg-zinc-800 dark:ring-zinc-700">
                <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-blue-500/10"></div>
                <div class="relative flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-linear-to-br from-blue-400 to-blue-600 shadow-lg shadow-blue-500/30">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <flux:text class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Patients</flux:text>
                            <div class="mt-2 flex items-baseline gap-2">
                                <flux:heading size="2xl" class="font-bold text-zinc-900 dark:text-white">{{ number_format($statistics['total_patients']) }}</flux:heading>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Appointments -->
            <div class="group relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 transition duration-300 hover:shadow-lg dark:bg-zinc-800 dark:ring-zinc-700">
                <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-green-500/10"></div>
                <div class="relative flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-linear-to-br from-green-400 to-green-600 shadow-lg shadow-green-500/30">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <flux:text class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Today's Appointments</flux:text>
                            <div class="mt-2 flex items-baseline gap-2">
                                <flux:heading size="2xl" class="font-bold text-zinc-900 dark:text-white">{{ number_format($statistics['today_appointments']) }}</flux:heading>
                                <flux:text class="text-xs text-green-600 dark:text-green-400">Today</flux:text>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Patients with Health Cards -->
            <div class="group relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 transition duration-300 hover:shadow-lg dark:bg-zinc-800 dark:ring-zinc-700">
                <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-indigo-500/10"></div>
                <div class="relative flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-linear-to-br from-indigo-400 to-indigo-600 shadow-lg shadow-indigo-500/30">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <flux:text class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Patients with Health Cards</flux:text>
                            <div class="mt-2 flex items-baseline gap-2">
                                <flux:heading size="2xl" class="font-bold text-zinc-900 dark:text-white">{{ number_format($statistics['total_healthcard_patients']) }}</flux:heading>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Appointments -->
            <div class="group relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 transition duration-300 hover:shadow-lg dark:bg-zinc-800 dark:ring-zinc-700">
                <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-purple-500/10"></div>
                <div class="relative flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-linear-to-br from-purple-400 to-purple-600 shadow-lg shadow-purple-500/30">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <flux:text class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Pending Appointments</flux:text>
                            <div class="mt-2 flex items-baseline gap-2">
                                <flux:heading size="2xl" class="font-bold text-zinc-900 dark:text-white">{{ number_format($statistics['pending_appointments']) }}</flux:heading>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category-Specific Statistics -->
        @if ($adminCategory)
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @if (isset($statistics['healthcard_patients']))
                    <div class="hidden group relative overflow-hidden rounded-xl bg-linear-to-br from-indigo-500 to-purple-600 p-6 shadow-lg">
                        <div class="absolute right-0 top-0 h-32 w-32 translate-x-12 -translate-y-12 rounded-full bg-white/10"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 backdrop-blur">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <flux:text class="text-sm font-medium text-white/80">Healthcard Patients</flux:text>
                                <flux:heading size="2xl" class="mt-2 font-bold text-white">{{ number_format($statistics['healthcard_patients']) }}</flux:heading>
                            </div>
                        </div>
                    </div>

                    <div class="hidden group relative overflow-hidden rounded-xl bg-linear-to-br from-teal-500 to-cyan-600 p-6 shadow-lg">
                        <div class="absolute right-0 top-0 h-32 w-32 translate-x-12 -translate-y-12 rounded-full bg-white/10"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 backdrop-blur">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <flux:text class="text-sm font-medium text-white/80">Active Healthcards</flux:text>
                                <flux:heading size="2xl" class="mt-2 font-bold text-white">{{ number_format($statistics['active_healthcards']) }}</flux:heading>
                            </div>
                        </div>
                    </div>
                @endif

                @if (isset($statistics['hiv_appointments']))
                    <div class="group relative overflow-hidden rounded-xl bg-linear-to-br from-red-500 to-pink-600 p-6 shadow-lg">
                        <div class="absolute right-0 top-0 h-32 w-32 translate-x-12 -translate-y-12 rounded-full bg-white/10"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 backdrop-blur">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <flux:text class="text-sm font-medium text-white/80">HIV Appointments</flux:text>
                                <flux:heading size="2xl" class="mt-2 font-bold text-white">{{ number_format($statistics['hiv_appointments']) }}</flux:heading>
                            </div>
                        </div>
                    </div>
                @endif

                @if (isset($statistics['pregnancy_appointments']))
                    <div class="group relative overflow-hidden rounded-xl bg-linear-to-br from-pink-500 to-rose-600 p-6 shadow-lg">
                        <div class="absolute right-0 top-0 h-32 w-32 translate-x-12 -translate-y-12 rounded-full bg-white/10"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 backdrop-blur">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <flux:text class="text-sm font-medium text-white/80">Pregnancy Appointments</flux:text>
                                <flux:heading size="2xl" class="mt-2 font-bold text-white">{{ number_format($statistics['pregnancy_appointments']) }}</flux:heading>
                            </div>
                        </div>
                    </div>
                @endif

                @if (isset($statistics['total_records']))
                    <div class="group relative overflow-hidden rounded-xl bg-linear-to-br from-orange-500 to-amber-600 p-6 shadow-lg">
                        <div class="absolute right-0 top-0 h-32 w-32 translate-x-12 -translate-y-12 rounded-full bg-white/10"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 backdrop-blur">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <flux:text class="text-sm font-medium text-white/80">Total Records</flux:text>
                                <flux:heading size="2xl" class="mt-2 font-bold text-white">{{ number_format($statistics['total_records']) }}</flux:heading>
                            </div>
                        </div>
                    </div>
                    <div class="group relative overflow-hidden rounded-xl bg-linear-to-br from-cyan-500 to-blue-600 p-6 shadow-lg">
                        <div class="absolute right-0 top-0 h-32 w-32 translate-x-12 -translate-y-12 rounded-full bg-white/10"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 backdrop-blur">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <flux:text class="text-sm font-medium text-white/80">Records This Month</flux:text>
                                <flux:heading size="2xl" class="mt-2 font-bold text-white">{{ number_format($statistics['records_this_month']) }}</flux:heading>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Appointment Trends Chart for Healthcard/HIV Admin -->
        @if ($appointmentTrends)
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-800 dark:ring-zinc-700">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <flux:heading size="lg" class="flex items-center gap-2">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>
                                @if(in_array(strtolower($adminCategory ?? ''), ['healthcard', 'healthcard admin']))
                                    Health Card Appointments: Trend & Prediction Analysis
                                @elseif(in_array(strtolower($adminCategory ?? ''), ['hiv', 'hiv admin']))
                                    HIV Testing Appointments: Trend & Prediction Analysis
                                @elseif(in_array(strtolower($adminCategory ?? ''), ['pregnancy', 'pregnancy admin']))
                                    Pregnancy Care Appointments: Trend & Prediction Analysis
                                @endif
                            </span>
                        </flux:heading>
                        <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">12-month historical data with AI-powered predictions for next 2 months</flux:text>
                    </div>
                </div>

                <!-- Chart Legend Summary Cards -->
                <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-lg border-2 border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-500">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div>
                                <flux:text class="text-xs font-medium text-red-600 dark:text-red-400">No Show</flux:text>
                                <flux:heading size="lg" class="font-bold text-red-700 dark:text-red-300">{{ array_sum($appointmentTrends['datasets'][0]['data']) }}</flux:heading>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border-2 border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-500">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <flux:text class="text-xs font-medium text-green-600 dark:text-green-400">Completed</flux:text>
                                <flux:heading size="lg" class="font-bold text-green-700 dark:text-green-300">{{ array_sum($appointmentTrends['datasets'][1]['data']) }}</flux:heading>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border-2 border-orange-200 bg-orange-50 p-4 dark:border-orange-800 dark:bg-orange-900/20">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-500">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <flux:text class="text-xs font-medium text-orange-600 dark:text-orange-400">Cancelled</flux:text>
                                <flux:heading size="lg" class="font-bold text-orange-700 dark:text-orange-300">{{ array_sum($appointmentTrends['datasets'][2]['data']) }}</flux:heading>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart Container -->
                <div class="relative">
                    <canvas id="appointmentTrendsChart" class="h-80 w-full" data-chart="{{ json_encode($appointmentTrends) }}"></canvas>
                </div>

                <!-- Chart Info -->
                <div class="mt-6 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="flex-1">
                            <flux:text class="text-sm font-medium text-blue-900 dark:text-blue-100">Analysis Insight</flux:text>
                            <flux:text class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                This chart displays 12 months of historical trends (January to December) and predictions for health card appointment outcomes. The last two data points represent predicted values for the next two months based on historical patterns, helping you anticipate resource needs and identify potential issues.
                            </flux:text>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Recent Health Card Patients Table (for healthcard admins only) -->
        @if ($recentHealthCardPatients && $recentHealthCardPatients->count() > 0)
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-800 dark:ring-zinc-700">
                <div class="border-b border-zinc-200 p-6 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:heading size="lg" class="flex items-center gap-2">
                                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                                <span>Recent Health Card Patients</span>
                                <flux:badge size="lg">{{ $recentHealthCardPatients->count() }}</flux:badge>
                            </flux:heading>
                            <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Most recent patients who availed health cards</flux:text>
                        </div>
                        <a href="{{ route('healthcare_admin.health-cards') }}" class="group flex items-center gap-1 text-sm font-medium text-blue-600 transition-colors hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                            View All
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">Patient</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">Card Number</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">Barangay</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">Issue Date</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">Expiry Date</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-800">
                            @foreach ($recentHealthCardPatients as $healthCard)
                                <tr class="transition-colors duration-150 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-linear-to-br from-indigo-400 to-indigo-600 text-sm font-semibold text-white shadow-md">
                                                {{ $healthCard->patient->user->initials() }}
                                            </div>
                                            <div>
                                                <flux:text class="font-semibold text-zinc-900 dark:text-white">{{ $healthCard->patient->user->name }}</flux:text>
                                                <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Patient #{{ $healthCard->patient->patient_number }}</flux:text>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                            </svg>
                                            <flux:text class="font-mono font-medium text-zinc-900 dark:text-white">{{ $healthCard->card_number }}</flux:text>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <flux:text class="text-zinc-900 dark:text-white">{{ $healthCard->patient->barangay->name ?? 'N/A' }}</flux:text>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <flux:text class="text-zinc-900 dark:text-white">{{ $healthCard->issue_date->format('M d, Y') }}</flux:text>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <flux:text class="text-zinc-900 dark:text-white">{{ $healthCard->expiry_date->format('M d, Y') }}</flux:text>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if ($healthCard->status === 'active')
                                            <flux:badge color="green" icon="check-circle">
                                                Active
                                            </flux:badge>
                                        @elseif ($healthCard->status === 'expired')
                                            <flux:badge color="red">
                                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                                Expired
                                            </flux:badge>
                                        @elseif ($healthCard->status === 'suspended')
                                            <flux:badge color="yellow">
                                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                Suspended
                                            </flux:badge>
                                        @else
                                            <flux:badge class="bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300">
                                                {{ ucfirst($healthCard->status) }}
                                            </flux:badge>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Enhanced Upcoming Appointments -->
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-800 dark:ring-zinc-700">
            <div class="border-b border-zinc-200 p-6 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg" class="flex items-center gap-2">
                            <span>Upcoming Appointments</span>
                            <flux:badge size="lg">{{ $upcomingAppointments->count() }}</flux:badge>
                        </flux:heading>
                        <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">View and manage scheduled appointments</flux:text>
                    </div>
                    <a href="{{ route('healthcare_admin.appointments') }}" class="group flex items-center gap-1 text-sm font-medium text-blue-600 transition-colors hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        View All
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">Patient</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">Service</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">Date & Time</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-800">
                        @forelse ($upcomingAppointments as $appointment)
                            <tr class="transition-colors duration-150 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-linear-to-br from-blue-400 to-blue-600 text-sm font-semibold text-white shadow-md">
                                            {{ $appointment->patient->user->initials() }}
                                        </div>
                                        <div>
                                            <flux:text class="font-semibold text-zinc-900 dark:text-white">{{ $appointment->patient->user->name }}</flux:text>
                                            <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Patient ID: #{{ $appointment->patient->id }}</flux:text>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                                        <flux:text class="font-medium text-zinc-900 dark:text-white">{{ $appointment->service->name }}</flux:text>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <flux:text class="font-medium text-zinc-900 dark:text-white">{{ $appointment->scheduled_at->format('M d, Y') }}</flux:text>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ $appointment->scheduled_at->format('g:i A') }}</flux:text>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($appointment->status === 'confirmed')
                                        <flux:badge color="blue" icon="check-circle">
                                            Confirmed
                                        </flux:badge>
                                    @elseif ($appointment->status === 'pending')
                                        <flux:badge color="yellow">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            Pending
                                        </flux:badge>
                                    @elseif ($appointment->status === 'checked_in')
                                        <flux:badge class="inline-flex items-center gap-1.5 bg-blue-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-blue-400">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Checked In
                                        </flux:badge>
                                    @elseif ($appointment->status === 'in_progress')
                                        <flux:badge class="inline-flex items-center gap-1.5 bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            In Progress
                                        </flux:badge>
                                    @elseif ($appointment->status === 'completed')
                                        <flux:badge class="inline-flex items-center gap-1.5 bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-400">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Completed
                                        </flux:badge>
                                    @elseif ($appointment->status === 'cancelled')
                                        <flux:badge class="inline-flex items-center gap-1.5 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            Cancelled
                                        </flux:badge>
                                    @else
                                        <flux:badge class="inline-flex items-center gap-1.5 bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300">
                                            {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                        </flux:badge>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-700">
                                            <svg class="h-8 w-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">No upcoming appointments</flux:heading>
                                            <flux:text class="mt-1 text-sm text-zinc-500">There are no appointments scheduled at the moment.</flux:text>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Enhanced Quick Actions -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-800 dark:ring-zinc-700">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <flux:heading size="lg">Quick Actions</flux:heading>
                    <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Commonly used features</flux:text>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {{-- <a href="{{ route('healthcare_admin.approvals') }}" class="group relative overflow-hidden rounded-lg border-2 border-zinc-200 bg-linear-to-br from-yellow-50 to-orange-50 p-5 transition-all duration-300 hover:border-yellow-400 hover:shadow-lg dark:border-zinc-700 dark:from-yellow-900/20 dark:to-orange-900/20 dark:hover:border-yellow-600">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-yellow-500 shadow-md transition-transform duration-300 group-hover:scale-110">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <flux:text class="font-semibold text-zinc-900 dark:text-white">Patient Approvals</flux:text>
                            <flux:text class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Review pending registrations</flux:text>
                            @if($statistics['pending_approvals'] > 0)
                                <flux:badge variant="warning" size="sm" class="mt-2">{{ $statistics['pending_approvals'] }} Pending</flux:badge>
                            @endif
                        </div>
                        <svg class="h-5 w-5 text-zinc-400 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a> --}}

                @if (in_array(strtolower($adminCategory ?? ''), ['healthcard', 'healthcard admin', 'medical records', 'medical records admin']))
                    <a href="{{ route('healthcare_admin.health-cards') }}" class="group relative overflow-hidden rounded-lg border-2 border-zinc-200 bg-linear-to-br from-blue-50 to-indigo-50 p-5 transition-all duration-300 hover:border-blue-400 hover:shadow-lg dark:border-zinc-700 dark:from-blue-900/20 dark:to-indigo-900/20 dark:hover:border-blue-600">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-500 shadow-md transition-transform duration-300 group-hover:scale-110">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <flux:text class="font-semibold text-zinc-900 dark:text-white">Health Cards</flux:text>
                                <flux:text class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Manage health cards</flux:text>
                            </div>
                            <svg class="h-5 w-5 text-zinc-400 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                @endif

                <a href="{{ route('healthcare_admin.appointments') }}" class="group relative overflow-hidden rounded-lg border-2 border-zinc-200 bg-linear-to-br from-green-50 to-emerald-50 p-5 transition-all duration-300 hover:border-green-400 hover:shadow-lg dark:border-zinc-700 dark:from-green-900/20 dark:to-emerald-900/20 dark:hover:border-green-600">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-green-500 shadow-md transition-transform duration-300 group-hover:scale-110">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <flux:text class="font-semibold text-zinc-900 dark:text-white">Appointments</flux:text>
                            <flux:text class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">View all appointments</flux:text>
                        </div>
                        <svg class="h-5 w-5 text-zinc-400 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>
        </div>

        <!-- Enhanced Pending Patient Approvals -->
        @if ($pendingPatients->count() > 0)
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-800 dark:ring-zinc-700">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <flux:heading size="lg" class="flex items-center gap-2">
                            <span>Pending Patient Approvals</span>
                            <flux:badge variant="warning" size="lg">{{ $pendingPatients->count() }}</flux:badge>
                        </flux:heading>
                        <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Review and approve new patient registrations</flux:text>
                    </div>
                    <a href="{{ route('healthcare_admin.approvals') }}" class="group flex items-center gap-1 text-sm font-medium text-blue-600 transition-colors hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        View All
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="space-y-3">
                    @foreach ($pendingPatients as $pendingUser)
                        <div class="group relative overflow-hidden rounded-lg border-2 border-zinc-200 bg-zinc-50 p-4 transition-all duration-300 hover:border-yellow-400 hover:bg-white hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900/50 dark:hover:border-yellow-600 dark:hover:bg-zinc-800">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="relative h-12 w-12 shrink-0">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-linear-to-br from-yellow-400 to-orange-500 shadow-md ring-2 ring-white dark:ring-zinc-800">
                                            <span class="text-lg font-bold text-white">{{ $pendingUser->initials() }}</span>
                                        </div>
                                        <div class="absolute -right-1 -top-1 h-4 w-4 rounded-full bg-yellow-500 ring-2 ring-white dark:ring-zinc-800"></div>
                                    </div>
                                    <div class="flex-1">
                                        <flux:text class="font-semibold text-zinc-900 dark:text-white">{{ $pendingUser->name }}</flux:text>
                                        <div class="mt-1 flex items-center gap-2">
                                            <svg class="h-4 w-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ $pendingUser->email }}</flux:text>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('healthcare_admin.approvals') }}" class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all duration-300 hover:bg-blue-700 hover:shadow-md">
                                    Review
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
