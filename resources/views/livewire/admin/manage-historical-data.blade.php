<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="mb-1">Historical Disease Data</flux:heading>
            <flux:text>Manage historical disease data to enable SARIMA predictions</flux:text>
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

    {{-- Data Status Cards --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
        <flux:heading size="lg" class="mb-4">Prediction Readiness Status</flux:heading>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($this->diseaseTypes as $key => $name)
                @php
                    $status = $this->dataStatus[$key];
                    $percentage = min(100, ($status['data_points'] / $status['required']) * 100);
                @endphp
                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <flux:text class="font-semibold">{{ $name }}</flux:text>
                        <flux:badge :color="$status['sufficient'] ? 'green' : 'orange'">
                            {{ $status['data_points'] }}/{{ $status['required'] }}
                        </flux:badge>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 mb-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    @if($status["sufficient"])
                        <div class="flex items-center justify-between">
                            <flux:text class="text-sm text-green-600 dark:text-green-400">Ready for predictions (auto-generated nightly)</flux:text>
                        </div>
                    @else
                        <flux:text class="text-sm text-orange-600 dark:text-orange-400">
                            Need {{ $status['missing'] }} more months
                        </flux:text>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
        <flux:heading size="lg" class="mb-4">Filters</flux:heading>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <flux:field>
                    <flux:label>Disease Type</flux:label>
                    <flux:select wire:model.live="filterDiseaseType">
                        <option value="">All Diseases</option>
                        @foreach($this->diseaseTypes as $key => $name)
                            <option value="{{ $key }}">{{ $name }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>
            <div>
                <flux:field>
                    <flux:label>Barangay</flux:label>
                    <flux:select wire:model.live="filterBarangayId">
                        <option value="">All Barangays (City-wide)</option>
                        @foreach($this->barangays as $barangay)
                            <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                        @endforeach
                    </flux:select>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Disease Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Barangay</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Cases</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Source</th>
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
                                    <flux:text>{{ $this->diseaseTypes[$data->disease_type] }}</flux:text>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:text>{{ $data->barangay?->name ?? 'City-wide' }}</flux:text>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:text class="font-semibold">{{ $data->case_count }}</flux:text>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:badge :color="$data->data_source === 'manual' ? 'blue' : 'green'">
                                        {{ ucfirst($data->data_source) }}
                                    </flux:badge>
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
        @if($this->historicalData->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $this->historicalData->links() }}
            </div>
        @endif
    </div>

    {{-- Add/Edit Modal --}}
    @if($showForm)
        <flux:modal wire:model="showForm" class="md:w-96">
            <flux:heading size="lg" class="mb-6">{{ $editingId ? 'Edit' : 'Add' }} Historical Data</flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>Disease Type</flux:label>
                    <flux:select wire:model="diseaseType">
                        @foreach($this->diseaseTypes as $key => $name)
                            <option value="{{ $key }}">{{ $name }}</option>
                        @endforeach
                    </flux:select>
                    @error('diseaseType')
                        <flux:text class="text-red-600 text-sm mt-1">{{ $message }}</flux:text>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Barangay (Optional)</flux:label>
                    <flux:select wire:model="barangayId">
                        <option value="">City-wide</option>
                        @foreach($this->barangays as $barangay)
                            <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                        @endforeach
                    </flux:select>
                    @error('barangayId')
                        <flux:text class="text-red-600 text-sm mt-1">{{ $message }}</flux:text>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Record Date (Month/Year)</flux:label>
                    <flux:input type="month" wire:model="recordDate" />
                    @error('recordDate')
                        <flux:text class="text-red-600 text-sm mt-1">{{ $message }}</flux:text>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Case Count</flux:label>
                    <flux:input type="number" wire:model="caseCount" min="0" />
                    @error('caseCount')
                        <flux:text class="text-red-600 text-sm mt-1">{{ $message }}</flux:text>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Notes (Optional)</flux:label>
                    <flux:textarea wire:model="notes" rows="3" />
                    @error('notes')
                        <flux:text class="text-red-600 text-sm mt-1">{{ $message }}</flux:text>
                    @enderror
                </flux:field>

                <div class="flex gap-3 justify-end">
                    <flux:button type="button" wire:click="closeForm" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? 'Update' : 'Save' }}
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
