<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="mb-1">Health Card Historical Data</flux:heading>
            <flux:text>Manage historical health card issuance data to enable SARIMA predictions</flux:text>
        </div>
        <flux:button wire:click="openForm" icon="plus">
            Add Historical Data
        </flux:button>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('message') }}
        </flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger" icon="exclamation-triangle">
            {{ session('error') }}
        </flux:callout>
    @endif

    {{-- Data Status Card --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
        <flux:heading size="lg" class="mb-4">Prediction Readiness Status</flux:heading>
        <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <flux:text class="font-semibold">Health Card Issuance Predictions</flux:text>
                <flux:badge :color="$this->dataStatus['sufficient'] ? 'green' : 'orange'">
                    {{ $this->dataStatus['data_points'] }}/{{ $this->dataStatus['required'] }} months
                </flux:badge>
            </div>
            @php
                $percentage = min(100, ($this->dataStatus['data_points'] / $this->dataStatus['required']) * 100);
            @endphp
            <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 mb-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
            </div>
            @if($this->dataStatus['sufficient'])
                <flux:text class="text-sm text-green-600 dark:text-green-400">
                    ✓ Ready for predictions! Forecasts will be generated automatically.
                </flux:text>
            @else
                <flux:text class="text-sm text-orange-600 dark:text-orange-400">
                    Need {{ $this->dataStatus['missing'] }} more months of data for accurate predictions.
                </flux:text>
            @endif
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
        <flux:heading size="lg" class="mb-4">Filters</flux:heading>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <flux:field>
                    <flux:label>Start Date</flux:label>
                    <flux:input type="date" wire:model.live="filterStartDate" />
                </flux:field>
            </div>
            <div>
                <flux:field>
                    <flux:label>End Date</flux:label>
                    <flux:input type="date" wire:model.live="filterEndDate" />
                </flux:field>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="p-6">
            <flux:heading size="lg" class="mb-4">Historical Records</flux:heading>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Issued</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Notes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Source</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Created By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($this->historicalData as $data)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:text>{{ $data->record_date->format('M Y') }}</flux:text>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:text class="font-semibold text-blue-600 dark:text-blue-400">{{ $data->issued_count }}</flux:text>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:text class="text-sm">{{ $data->notes ?: '-' }}</flux:text>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:badge :color="$data->data_source === 'manual' ? 'blue' : 'green'">
                                        {{ ucfirst($data->data_source) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:text class="text-sm">{{ $data->creator?->name ?? 'System' }}</flux:text>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex gap-2">
                                        <flux:button wire:click="edit({{ $data->id }})" size="sm" variant="ghost" icon="pencil">
                                            Edit
                                        </flux:button>
                                        <flux:button
                                            wire:click="delete({{ $data->id }})"
                                            wire:confirm="Are you sure you want to delete this record?"
                                            size="sm"
                                            variant="danger"
                                            icon="trash">
                                            Delete
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center">
                                    <flux:text class="text-zinc-500">No historical data found. Add records to enable predictions.</flux:text>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $this->historicalData->links() }}
        </div>
    </div>

    {{-- Form Modal --}}
    @if($showForm)
        <flux:modal wire:model="showForm" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit' : 'Add' }} Historical Data</flux:heading>
                <flux:text class="mt-2">
                    Enter health card issuance data for a specific month to improve prediction accuracy.
                </flux:text>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Record Date (Month/Year)</flux:label>
                    <flux:input type="date" wire:model="recordDate" />
                    <flux:error name="recordDate" />
                    <flux:text class="text-sm text-zinc-500 mt-1">Select the first day of the month you're recording data for</flux:text>
                </flux:field>

                <flux:field>
                    <flux:label>Health Cards Issued</flux:label>
                    <flux:input type="number" wire:model="issuedCount" min="0" />
                    <flux:error name="issuedCount" />
                    <flux:text class="text-sm text-zinc-500 mt-1">Number of health cards issued during this period</flux:text>
                </flux:field>

                <flux:field>
                    <flux:label>Notes (Optional)</flux:label>
                    <flux:textarea wire:model="notes" rows="3" placeholder="Any relevant notes about this period..."></flux:textarea>
                    <flux:error name="notes" />
                </flux:field>
            </div>

            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="closeForm">
                    Cancel
                </flux:button>
                <flux:button variant="primary" color="blue" wire:click="save" icon="check">
                    {{ $editingId ? 'Update' : 'Save' }} Record
                </flux:button>
            </div>
        </flux:modal>
    @endif
</div>

