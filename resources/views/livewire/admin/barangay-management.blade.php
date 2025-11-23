<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Barangay Management</flux:heading>
                <flux:text>Manage barangays in Panabo City</flux:text>
            </div>
            <flux:button icon="plus" wire:click="createBarangay" variant="primary" color="cyan">
                Add Barangay
            </flux:button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-blue-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Barangays</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['total']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-green-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">With Patients</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['with_patients']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div> --}}

            {{-- <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-purple-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Population</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['total_population']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div> --}}

            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-indigo-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Patients</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['total_patients']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-yellow-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">Avg Patients</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['avg_patients_per_barangay'], 1) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-red-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">With Coordinates</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['with_coordinates']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Filter -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
            <flux:input wire:model.live="search" placeholder="Search barangays..." icon="magnifying-glass" />
        </div>

        <!-- Barangays Table -->
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Barangay Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">City</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Population</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Patients</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Coordinates</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Created</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-800">
                        @forelse ($barangays as $barangay)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $barangay->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900 dark:text-white">
                                    {{ $barangay->city }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900 dark:text-white">
                                    {{ $barangay->population ? number_format($barangay->population) : 'N/A' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold leading-5 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ number_format($barangay->patients_count) }} {{ Str::plural('patient', $barangay->patients_count) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900 dark:text-white">
                                    @if ($barangay->latitude && $barangay->longitude)
                                        <span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Set
                                        </span>
                                    @else
                                        <span class="text-zinc-400">Not set</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $barangay->created_at ? $barangay->created_at->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button wire:click="viewDetails({{ $barangay->id }})" size="sm" variant="ghost" icon="eye" title="View Details" />
                                        <flux:button wire:click="editBarangay({{ $barangay->id }})" size="sm" variant="ghost" icon="pencil" title="Edit" />
                                        @if ($barangay->patients_count === 0)
                                            <flux:button wire:click="deleteBarangay({{ $barangay->id }})" wire:confirm="Are you sure you want to delete this barangay? This action cannot be undone." size="sm" variant="ghost" icon="trash" title="Delete" class="text-red-600!"/>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">No barangays found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700 dark:bg-zinc-800">
                {{ $barangays->links() }}
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <flux:modal wire:model="showDetailsModal" class="max-w-2xl">
        @if ($selectedBarangay)
            <flux:heading size="lg">Barangay Details</flux:heading>

            <div class="mt-6 space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Barangay Name</flux:text>
                        <flux:text class="mt-1 text-lg font-semibold">{{ $selectedBarangay->name }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">City</flux:text>
                        <flux:text class="mt-1">{{ $selectedBarangay->city }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Population</flux:text>
                        <flux:text class="mt-1">{{ $selectedBarangay->population ? number_format($selectedBarangay->population) : 'Not specified' }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Registered Patients</flux:text>
                        <flux:text class="mt-1">{{ number_format($selectedBarangay->patients_count) }}</flux:text>
                    </div>
                </div>

                <!-- Coordinates -->
                @if ($selectedBarangay->latitude && $selectedBarangay->longitude)
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Coordinates</flux:text>
                        <div class="mt-2 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-900">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Latitude</flux:text>
                                    <flux:text class="font-mono text-sm">{{ $selectedBarangay->latitude }}</flux:text>
                                </div>
                                <div>
                                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Longitude</flux:text>
                                    <flux:text class="font-mono text-sm">{{ $selectedBarangay->longitude }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Statistics -->
                <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-900">
                    <flux:text class="mb-3 text-sm font-medium text-zinc-500 dark:text-zinc-400">Statistics</flux:text>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Registered Patients</flux:text>
                            <flux:text class="text-2xl font-semibold">{{ number_format($selectedBarangay->patients_count) }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Created Date</flux:text>
                            <flux:text>{{ $selectedBarangay->created_at ? $selectedBarangay->created_at->format('M d, Y') : 'N/A' }}</flux:text>
                        </div>
                    </div>
                </div>
            </div>

            <flux:separator class="my-6" />

            <div class="flex justify-end gap-2">
                <flux:button wire:click="closeDetailsModal" variant="ghost">Close</flux:button>
                <flux:button wire:click="editBarangay({{ $selectedBarangay->id }})" variant="primary">Edit Barangay</flux:button>
            </div>
        @endif
    </flux:modal>

    <!-- Create/Edit Modal -->
    <flux:modal wire:model="{{ $showCreateModal ? 'showCreateModal' : 'showEditModal' }}" class="max-w-2xl">
        <flux:heading size="lg">{{ $showCreateModal ? 'Create New Barangay' : 'Edit Barangay' }}</flux:heading>

        <form wire:submit.prevent="{{ $showCreateModal ? 'storeBarangay' : 'updateBarangay' }}" class="mt-6 space-y-6">
            <!-- Basic Information -->
            <div class="space-y-4">
                <flux:field>
                    <flux:label>Barangay Name *</flux:label>
                    <flux:input wire:model="form.name" placeholder="Enter barangay name" />
                    <flux:error name="form.name" />
                </flux:field>

                <flux:field>
                    <flux:label>City *</flux:label>
                    <flux:input wire:model="form.city" placeholder="Enter city name" />
                    <flux:error name="form.city" />
                </flux:field>

                <flux:field>
                    <flux:label>Population</flux:label>
                    <flux:input wire:model="form.population" type="number" min="0" placeholder="Enter population" />
                    <flux:error name="form.population" />
                </flux:field>
            </div>

            <!-- Coordinates -->
            <div>
                <flux:text class="mb-3 text-sm font-medium">GPS Coordinates (Optional)</flux:text>
                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Latitude</flux:label>
                        <flux:input wire:model="form.latitude" type="number" step="0.00000001" placeholder="e.g., 7.3072" />
                        <flux:error name="form.latitude" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Longitude</flux:label>
                        <flux:input wire:model="form.longitude" type="number" step="0.00000001" placeholder="e.g., 125.6828" />
                        <flux:error name="form.longitude" />
                    </flux:field>
                </div>
            </div>

            <flux:separator />

            <div class="flex justify-end gap-2">
                <flux:button wire:click="{{ $showCreateModal ? 'closeCreateModal' : 'closeEditModal' }}" type="button" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary">{{ $showCreateModal ? 'Create Barangay' : 'Update Barangay' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
