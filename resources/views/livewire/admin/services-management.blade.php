<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Services Management</flux:heading>
                <flux:text>Manage healthcare services offered by the facility</flux:text>
            </div>
            <flux:button icon="plus" wire:click="createService" variant="primary" color="cyan">
                Add Service
            </flux:button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-6">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-blue-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Services</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['total']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-green-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">Active</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['active']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-red-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">Inactive</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['inactive']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-purple-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">With Appointments</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['with_appointments']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-yellow-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">Free Services</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['free_services']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-indigo-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">Walk-in</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['walk_in']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <flux:input wire:model.live="search" placeholder="Search services..." icon="magnifying-glass" />
                </div>
                <div>
                    <flux:select wire:model.live="categoryFilter">
                        <option value="all">All Categories</option>
                        @foreach ($categories as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:select wire:model.live="statusFilter">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </flux:select>
                </div>
            </div>
        </div>

        <!-- Services Table -->
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Service Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Duration</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Fee</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Appointments</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-800">
                        @forelse ($services as $service)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $service->name }}</div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ Str::limit($service->description, 60) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $service->category_name }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900 dark:text-white">
                                    {{ $service->duration_minutes ? $service->duration_minutes . ' mins' : 'N/A' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900 dark:text-white">
                                    @if ($service->fee)
                                        ₱{{ number_format($service->fee, 2) }}
                                    @else
                                        <span class="text-green-600 dark:text-green-400">Free</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900 dark:text-white">
                                    {{ number_format($service->appointments_count) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($service->requires_appointment)
                                        <span class="inline-flex rounded-full bg-purple-100 px-2 text-xs font-semibold leading-5 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                            Appointment
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Walk-in
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($service->is_active)
                                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button wire:click="viewDetails({{ $service->id }})" size="sm" variant="ghost" icon="eye" title="View Details" />
                                        <flux:button wire:click="editService({{ $service->id }})" size="sm" variant="ghost" icon="pencil" title="Edit" />
                                        @if ($service->is_active)
                                            <flux:button wire:click="deactivateService({{ $service->id }})" wire:confirm="Are you sure you want to deactivate this service?" size="sm" variant="ghost" icon="x-circle" title="Deactivate" />
                                        @else
                                            <flux:button wire:click="activateService({{ $service->id }})" size="sm" variant="ghost" icon="check-circle" title="Activate" />
                                        @endif
                                        @if ($service->appointments_count === 0)
                                            <flux:button wire:click="deleteService({{ $service->id }})" wire:confirm="Are you sure you want to delete this service? This action cannot be undone." size="sm" variant="ghost" icon="trash" title="Delete" class="text-red-500!" />
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">No services found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700 dark:bg-zinc-800">
                {{ $services->links() }}
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <flux:modal wire:model="showDetailsModal" class="max-w-3xl">
        @if ($selectedService)
            <flux:heading size="lg">Service Details</flux:heading>

            <div class="mt-6 space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Service Name</flux:text>
                        <flux:text class="mt-1">{{ $selectedService->name }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Category</flux:text>
                        <flux:text class="mt-1">{{ $selectedService->category_name }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Duration</flux:text>
                        <flux:text class="mt-1">{{ $selectedService->duration_minutes ? $selectedService->duration_minutes . ' minutes' : 'Not specified' }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Fee</flux:text>
                        <flux:text class="mt-1">
                            @if ($selectedService->fee)
                                ₱{{ number_format($selectedService->fee, 2) }}
                            @else
                                Free
                            @endif
                        </flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Type</flux:text>
                        <flux:text class="mt-1">{{ $selectedService->requires_appointment ? 'Requires Appointment' : 'Walk-in' }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Status</flux:text>
                        <flux:text class="mt-1">{{ $selectedService->is_active ? 'Active' : 'Inactive' }}</flux:text>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Description</flux:text>
                    <flux:text class="mt-1">{{ $selectedService->description }}</flux:text>
                </div>

                <!-- Requirements -->
                @if ($selectedService->requirements && count($selectedService->requirements) > 0)
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Requirements</flux:text>
                        <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-zinc-900 dark:text-white">
                            @foreach ($selectedService->requirements as $requirement)
                                <li>{{ $requirement }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Preparation Instructions -->
                @if ($selectedService->preparation_instructions && count($selectedService->preparation_instructions) > 0)
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Preparation Instructions</flux:text>
                        <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-zinc-900 dark:text-white">
                            @foreach ($selectedService->preparation_instructions as $instruction)
                                <li>{{ $instruction }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Statistics -->
                <div class="grid grid-cols-2 gap-4 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-900">
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Appointments</flux:text>
                        <flux:text class="mt-1 text-2xl font-semibold">{{ number_format($selectedService->appointments_count) }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Created</flux:text>
                        <flux:text class="mt-1">{{ $selectedService->created_at->format('M d, Y') }}</flux:text>
                    </div>
                </div>
            </div>

            <flux:separator class="my-6" />

            <div class="flex justify-end gap-2">
                <flux:button wire:click="closeDetailsModal" variant="ghost">Close</flux:button>
                <flux:button wire:click="editService({{ $selectedService->id }})" variant="primary">Edit Service</flux:button>
            </div>
        @endif
    </flux:modal>

    <!-- Create/Edit Modal -->
    <flux:modal wire:model="{{ $showCreateModal ? 'showCreateModal' : 'showEditModal' }}" class="max-w-3xl">
        <flux:heading size="lg">{{ $showCreateModal ? 'Create New Service' : 'Edit Service' }}</flux:heading>

        <form wire:submit.prevent="{{ $showCreateModal ? 'storeService' : 'updateService' }}" class="mt-6 space-y-6">
            <!-- Basic Information -->
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <flux:field>
                        <flux:label>Service Name *</flux:label>
                        <flux:input wire:model="form.name" placeholder="Enter service name" />
                        <flux:error name="form.name" />
                    </flux:field>
                </div>

                <div class="col-span-2">
                    <flux:field>
                        <flux:label>Description *</flux:label>
                        <flux:textarea wire:model="form.description" rows="3" placeholder="Enter service description" />
                        <flux:error name="form.description" />
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label>Category *</flux:label>
                        <flux:select wire:model="form.category">
                            <option value="">Select category</option>
                            @foreach ($categories as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="form.category" />
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label>Duration (minutes)</flux:label>
                        <flux:input wire:model="form.duration_minutes" type="number" min="1" placeholder="e.g., 30" />
                        <flux:error name="form.duration_minutes" />
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label>Fee (₱)</flux:label>
                        <flux:input wire:model="form.fee" type="number" step="0.01" min="0" placeholder="0.00 for free" />
                        <flux:error name="form.fee" />
                    </flux:field>
                </div>
            </div>

            <!-- Requirements -->
            <div>
                <flux:field>
                    <flux:label>Requirements</flux:label>
                    <flux:textarea wire:model="form.requirements" rows="4" placeholder="Enter each requirement on a new line" />
                    <flux:error name="form.requirements" />
                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Enter each requirement on a new line</flux:text>
                </flux:field>
            </div>

            <!-- Preparation Instructions -->
            <div>
                <flux:field>
                    <flux:label>Preparation Instructions</flux:label>
                    <flux:textarea wire:model="form.preparation_instructions" rows="4" placeholder="Enter each instruction on a new line" />
                    <flux:error name="form.preparation_instructions" />
                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Enter each instruction on a new line</flux:text>
                </flux:field>
            </div>

            <!-- Checkboxes -->
            <div class="space-y-2">
                <flux:checkbox wire:model="form.requires_appointment" label="Requires Appointment" />
                <flux:checkbox wire:model="form.is_active" label="Active" />
            </div>

            <flux:separator />

            <div class="flex justify-end gap-2">
                <flux:button wire:click="{{ $showCreateModal ? 'closeCreateModal' : 'closeEditModal' }}" type="button" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary">{{ $showCreateModal ? 'Create Service' : 'Update Service' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
