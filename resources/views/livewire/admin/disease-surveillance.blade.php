    <div x-data="initSurveillanceData(@js($this->statistics), @js($this->heatmapData), @js($this->trendsData))"
         x-init="
            updateGlobalData();
            $wire.on('filters-updated', (event) => {
                console.log('Filters updated event received', event);
                const data = event[0] || event;
                statistics = data.statistics;
                heatmapData = data.heatmapData;
                trendsData = data.trendsData;
                updateGlobalData();

                // Wait for Livewire to finish morphing the DOM
                $nextTick(() => {
                    // Give extra time for DOM to stabilize, especially for conditional renders
                    setTimeout(() => {
                        console.log('Reinitializing after filter change');
                        if (typeof window.initializeActiveTab === 'function') {
                            window.initializeActiveTab();
                        }
                    }, 100);
                });
            });
         "
         class="space-y-6">
        {{-- Header with title and filters --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="mb-1">Disease Surveillance Dashboard</flux:heading>
            <flux:text>Real-time disease monitoring and outbreak detection across all barangays</flux:text>
        </div>
        <div class="flex gap-2">
            <flux:button icon="plus" variant="primary" color="cyan" href="{{ route('admin.historical-data') }}">
                Manage Historical Data
            </flux:button>

            <flux:button wire:click="resetFilters" variant="filled" icon="arrow-path">
                Reset Filters
            </flux:button>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
        <flux:heading size="lg" class="mb-4">Filters</flux:heading>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Disease Type Filter --}}
            <div>
                <flux:field>
                    <flux:label>Disease Type</flux:label>
                    <flux:select wire:model.live="selectedDiseaseType">
                        <option value="">All Diseases</option>
                        @foreach($this->diseaseTypes as $key => $name)
                            <option value="{{ $key }}">{{ $name }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            {{-- Period Filter --}}
            <div>
                <flux:field>
                    <flux:label>Time Period</flux:label>
                    <flux:select wire:model.live="selectedPeriod">
                        <option value="7days">Last 7 Days</option>
                        <option value="30days">Last 30 Days</option>
                        <option value="90days">Last 90 Days</option>
                        <option value="1year">Last 1 Year</option>
                        @if(!empty($this->availableYears))
                            <optgroup label="By Year">
                                @foreach($this->availableYears as $year)
                                    <option value="year:{{ $year }}">Year {{ $year }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    </flux:select>
                </flux:field>
            </div>

            {{-- Barangay Filter --}}
            <div class="hidden">
                <flux:field>
                    <flux:label>Barangay</flux:label>
                    <flux:select wire:model.live="selectedBarangayId">
                        <option value="">All Barangays</option>
                        @foreach($this->barangays as $barangay)
                            <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Total Cases --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Total Cases</flux:text>
                    <flux:heading size="xl" class="mt-1">{{ $this->statistics['total_cases'] }}</flux:heading>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- New Cases (7 days) --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">New Cases (7 Days)</flux:text>
                    <flux:heading size="xl" class="mt-1">{{ $this->statistics['new_cases_7days'] }}</flux:heading>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- High Risk Barangays --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">High Risk Barangays</flux:text>
                    <flux:heading size="xl" class="mt-1">{{ count($this->statistics['high_risk_barangays']) }}</flux:heading>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Trend Direction --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Trend Direction</flux:text>
                    <flux:heading size="xl" class="mt-1 capitalize">{{ $this->statistics['trend_direction'] }}</flux:heading>
                </div>
                <div class="p-3 {{ $this->statistics['trend_direction'] === 'up' ? 'bg-red-100 dark:bg-red-900' : ($this->statistics['trend_direction'] === 'down' ? 'bg-green-100 dark:bg-green-900' : 'bg-gray-100 dark:bg-gray-900') }} rounded-lg">
                    @if($this->statistics['trend_direction'] === 'up')
                        <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    @elseif($this->statistics['trend_direction'] === 'down')
                        <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        </svg>
                    @else
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                        </svg>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Outbreak Alerts --}}
    @if(count($this->outbreakAlerts) > 0)
        <flux:callout variant="danger" icon="exclamation-triangle">
            <flux:heading size="lg" class="mb-2">Outbreak Alerts Detected!</flux:heading>
            <flux:text>{{ count($this->outbreakAlerts) }} barangay(s) showing significant disease activity increases.</flux:text>
            <div class="mt-3 space-y-2">
                @foreach($this->outbreakAlerts as $alert)
                    <div class="bg-white dark:bg-zinc-900 rounded p-3">
                        <flux:text class="font-semibold">{{ $alert['barangay_name'] }}</flux:text>
                        <flux:text class="text-sm">
                            Recent cases: {{ $alert['recent_cases'] }} |
                            Previous: {{ $alert['previous_cases'] }} |
                            Increase: {{ $alert['increase_rate'] }}% |
                            Risk: {{ $alert['risk_level'] }}
                        </flux:text>
                    </div>
                @endforeach
            </div>
        </flux:callout>
    @endif

    {{-- Tabs Navigation --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="border-b border-zinc-200 dark:border-zinc-700 px-6">
            <nav class="flex gap-4" aria-label="Tabs">
                <button
                    wire:click="setTab('overview')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Overview
                </button>
                <button
                    wire:click="setTab('heatmap')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'heatmap' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Heatmap
                </button>
                <button
                    wire:click="setTab('trends')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'trends' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Trend Analysis
                </button>
                <button
                    wire:click="setTab('highrisk')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'highrisk' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    High Risk Areas
                </button>
            </nav>
        </div>

        <div class="p-6">
            {{-- Overview Tab --}}
            @if($activeTab === 'overview')
                <div class="space-y-6">
                    <flux:heading size="lg">Disease Distribution</flux:heading>

                    {{-- Chart.js Disease Distribution Chart --}}
                    <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700" style="height: 400px;">
                        <canvas id="disease-overview-chart"></canvas>
                    </div>
                </div>
            @endif

            {{-- Heatmap Tab --}}
            @if($activeTab === 'heatmap')
                <div class="space-y-6">
                    <flux:heading size="lg">Geographic Distribution Heatmap</flux:heading>

                    {{-- Leaflet.js Heatmap Visualization --}}
                    <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 shadow-sm border border-zinc-200 dark:border-zinc-700">
                        <div id="disease-heatmap" style="height: 500px; width: 100%;" class="rounded-lg"></div>

                        {{-- Legend --}}
                        <div class="flex items-center justify-center gap-6 mt-4 flex-wrap">
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded bg-green-500"></div>
                                <flux:text class="text-sm">None (0)</flux:text>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded bg-yellow-500"></div>
                                <flux:text class="text-sm">Low (1-2)</flux:text>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded bg-orange-500"></div>
                                <flux:text class="text-sm">Medium (3-5)</flux:text>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded bg-red-500"></div>
                                <flux:text class="text-sm">High (6-10)</flux:text>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded bg-red-900"></div>
                                <flux:text class="text-sm">Critical (10+)</flux:text>
                            </div>
                        </div>
                    </div>

                    {{-- Heatmap Data Table --}}
                    <div class="mt-6">
                        <flux:heading size="lg" class="mb-4">Cases by Barangay</flux:heading>
                        <div class="overflow-auto max-h-[500px]">
                            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                                <thead class="bg-zinc-50 dark:bg-zinc-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Barangay</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Cases</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Intensity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                                    @forelse($this->heatmapData as $data)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <flux:text>{{ $data['barangay_name'] }}</flux:text>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <flux:text class="font-semibold">{{ $data['cases_count'] }}</flux:text>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <flux:text class="capitalize">{{ $data['intensity_level'] }}</flux:text>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <flux:badge
                                                    :color="match($data['intensity_level']) {
                                                        'critical' => 'red',
                                                        'high' => 'orange',
                                                        'medium' => 'yellow',
                                                        'low' => 'green',
                                                        default => 'zinc'
                                                    }">
                                                    {{ ucfirst($data['intensity_level']) }}
                                                </flux:badge>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center">
                                                <flux:text class="text-zinc-500">No data available for the selected filters</flux:text>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Trends Tab --}}
            @if($activeTab === 'trends')
                <div class="space-y-6">
                    <flux:heading size="lg">Trend Analysis & Predictions</flux:heading>

                    @if($selectedDiseaseType)
                        {{-- Chart.js Trend Chart --}}
                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700" style="height: 400px;">
                            <canvas id="disease-trends-chart"></canvas>
                        </div>

                        {{-- Trend Data Tables --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Historical Data --}}
                            <div>
                                <flux:heading size="md" class="mb-4">Historical Cases</flux:heading>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                                        <thead class="bg-zinc-50 dark:bg-zinc-900">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500">Month</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500">Cases</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                                            @foreach($this->trendAnalysis['historical'] as $data)
                                                <tr>
                                                    <td class="px-4 py-2"><flux:text class="text-sm">{{ $data['month'] }}</flux:text></td>
                                                    <td class="px-4 py-2"><flux:text class="text-sm font-semibold">{{ $data['cases'] }}</flux:text></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Predicted Data --}}
                            <div>
                                <flux:heading size="md" class="mb-4">Predictions (Next 6 Months)</flux:heading>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                                        <thead class="bg-zinc-50 dark:bg-zinc-900">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500">Month</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500">Predicted</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500">Range</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                                            @forelse($this->trendAnalysis['predicted'] as $prediction)
                                                <tr>
                                                    <td class="px-4 py-2"><flux:text class="text-sm">{{ $prediction['month'] }}</flux:text></td>
                                                    <td class="px-4 py-2"><flux:text class="text-sm font-semibold">{{ number_format($prediction['predicted_cases'], 1) }}</flux:text></td>
                                                    <td class="px-4 py-2">
                                                        <flux:text class="text-sm text-zinc-600">
                                                            {{ number_format($prediction['lower_bound'], 1) }} - {{ number_format($prediction['upper_bound'], 1) }}
                                                        </flux:text>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-4 py-4 text-center">
                                                        <flux:text class="text-sm text-zinc-500">No prediction data available</flux:text>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <flux:callout variant="info" icon="information-circle">
                            Please select a specific disease type to view trend analysis and predictions.
                        </flux:callout>
                    @endif
                </div>
            @endif

            {{-- High Risk Areas Tab --}}
            @if($activeTab === 'highrisk')
                <div class="space-y-6">
                    <flux:heading size="lg">High Risk Barangays</flux:heading>

                    @if(count($this->highRiskBarangays) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($this->highRiskBarangays as $barangay)
                                <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border-2 {{
                                    $barangay['risk_level'] === 'Critical' ? 'border-red-500' :
                                    ($barangay['risk_level'] === 'High' ? 'border-orange-500' : 'border-yellow-500')
                                }}">
                                    <div class="flex items-center justify-between mb-4">
                                        <flux:heading size="md">{{ $barangay['barangay_name'] }}</flux:heading>
                                        <flux:badge
                                            :color="match($barangay['risk_level']) {
                                                'Critical' => 'red',
                                                'High' => 'orange',
                                                'Moderate' => 'yellow',
                                                default => 'green'
                                            }">
                                            {{ $barangay['risk_level'] }}
                                        </flux:badge>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Total Cases:</flux:text>
                                            <flux:text class="text-sm font-semibold">{{ $barangay['cases_count'] }}</flux:text>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <flux:callout variant="success" icon="check-circle">
                            No high-risk barangays detected for the selected period. All areas showing normal disease activity levels.
                        </flux:callout>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
