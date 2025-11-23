<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Users Management</flux:heading>
                <flux:text>Manage system users and their roles</flux:text>
            </div>
            <flux:button icon="plus" wire:click="createUser" variant="primary">
                Add User
            </flux:button>
        </div>

        @if(session()->has('success'))
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-blue-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Users</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['total']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
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
            </div> --}}

            {{-- <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
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
            </div> --}}

            {{-- <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-yellow-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">Pending</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['pending']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div> --}}

            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-purple-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">Admins</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['admins']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-indigo-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">Doctors</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['doctors']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="flex items-center">
                    <div class="shrink-0 rounded-md bg-teal-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-zinc-500 dark:text-zinc-400">Patients</dt>
                            <dd class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($statistics['patients']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <flux:input wire:model.live="search" placeholder="Search users..." icon="magnifying-glass" />
                </div>
                <div>
                    <flux:select wire:model.live="roleFilter">
                        <option value="all">All Roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:select wire:model.live="statusFilter">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                    </flux:select>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">User</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Contact</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Role</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Created</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-800">
                        @forelse ($users as $user)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 shrink-0">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                                                <span class="text-sm font-medium text-blue-800 dark:text-blue-200">{{ $user->initials() }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $user->name }}</div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900 dark:text-white">
                                    {{ $user->contact_number ?? 'N/A' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex flex-col gap-2">
                                        <span class="inline-flex rounded-full bg-purple-100 px-2 text-xs font-semibold leading-5 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                            {{ $user->role ? ucfirst(str_replace('_', ' ', $user->role->name)) : 'No Role' }}
                                        </span>
                                        @if($user->admin_category)
                                            <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $user->admin_category->label() }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($user->status === 'active')
                                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Active
                                        </span>
                                    @elseif ($user->status === 'inactive')
                                        <span class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Inactive
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button wire:click="viewDetails({{ $user->id }})" size="sm" variant="ghost" icon="eye" title="View Details" />
                                        <flux:button wire:click="editUser({{ $user->id }})" size="sm" variant="ghost" icon="pencil" title="Edit" />
                                        @if ($user->status === 'active')
                                            <flux:button wire:click="deactivateUser({{ $user->id }})" wire:confirm="Are you sure you want to deactivate this user?" size="sm" variant="ghost" icon="x-circle" title="Deactivate" />
                                        @else
                                            <flux:button wire:click="activateUser({{ $user->id }})" size="sm" variant="ghost" icon="check-circle" title="Activate" />
                                        @endif
                                        @if ($user->id !== auth()->id())
                                            <flux:button
                                                x-on:click="if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) $wire.deleteUser({{ $user->id }})"
                                                size="sm"
                                                variant="ghost"
                                                icon="trash"
                                                title="Delete"
                                                class="text-red-600!"
                                            />
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">No users found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700 dark:bg-zinc-800">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <flux:modal wire:model="showDetailsModal" class="max-w-3xl">
        @if ($selectedUser)
            <flux:heading size="lg">User Details</flux:heading>

            <div class="mt-6 space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Full Name</flux:text>
                        <flux:text class="mt-1 text-lg font-semibold">{{ $selectedUser->name }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Email</flux:text>
                        <flux:text class="mt-1">{{ $selectedUser->email }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Contact Number</flux:text>
                        <flux:text class="mt-1">{{ $selectedUser->contact_number ?? 'Not provided' }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Role</flux:text>
                        <flux:text class="mt-1">{{ $selectedUser->role ? ucfirst(str_replace('_', ' ', $selectedUser->role->name)) : 'No Role' }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Status</flux:text>
                        <flux:text class="mt-1">{{ ucfirst($selectedUser->status) }}</flux:text>
                    </div>
                    @if ($selectedUser->admin_category)
                        <div>
                            <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Admin Category</flux:text>
                            <flux:text class="mt-1">{{ $selectedUser->admin_category }}</flux:text>
                        </div>
                    @endif
                </div>

                <!-- Account Information -->
                <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-900">
                    <flux:text class="mb-3 text-sm font-medium text-zinc-500 dark:text-zinc-400">Account Information</flux:text>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Created Date</flux:text>
                            <flux:text>{{ $selectedUser->created_at ? $selectedUser->created_at->format('M d, Y h:i A') : 'N/A' }}</flux:text>
                        </div>
                        @if ($selectedUser->approved_at)
                            <div>
                                <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Approved Date</flux:text>
                                <flux:text>{{ $selectedUser->approved_at->format('M d, Y h:i A') }}</flux:text>
                            </div>
                        @endif
                        @if ($selectedUser->approver)
                            <div>
                                <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Approved By</flux:text>
                                <flux:text>{{ $selectedUser->approver->name }}</flux:text>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Related Records -->
                @if ($selectedUser->doctor || $selectedUser->patient)
                    <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                        <flux:text class="mb-3 text-sm font-medium text-zinc-500 dark:text-zinc-400">Related Records</flux:text>
                        <div class="space-y-2">
                            @if ($selectedUser->doctor)
                                <flux:text class="text-sm">✓ Has doctor profile</flux:text>
                            @endif
                            @if ($selectedUser->patient)
                                <flux:text class="text-sm">✓ Has patient profile</flux:text>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <flux:separator class="my-6" />

            <div class="flex justify-end gap-2">
                <flux:button wire:click="closeDetailsModal" variant="ghost">Close</flux:button>
                <flux:button wire:click="editUser({{ $selectedUser->id }})" variant="primary">Edit User</flux:button>
            </div>
        @endif
    </flux:modal>

    <!-- Create/Edit Modal -->
    <flux:modal wire:model="{{ $showCreateModal ? 'showCreateModal' : 'showEditModal' }}" class="max-w-2xl">
        <flux:heading size="lg">{{ $showCreateModal ? 'Create New User' : 'Edit User' }}</flux:heading>

        <form wire:submit.prevent="{{ $showCreateModal ? 'storeUser' : 'updateUser' }}" class="mt-6 space-y-6">
            <!-- Basic Information -->
            <div class="space-y-4">
                <flux:field>
                    <flux:label>Full Name *</flux:label>
                    <flux:input wire:model="form.name" placeholder="Enter full name" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Email *</flux:label>
                        <flux:input wire:model="form.email" type="email" placeholder="Enter email" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Contact Number</flux:label>
                        <flux:input wire:model="form.contact_number" placeholder="Enter contact number" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Password {{ $showCreateModal ? '*' : '(leave blank to keep current)' }}</flux:label>
                    <flux:input wire:model="form.password" type="password" placeholder="{{ $showCreateModal ? 'Enter password' : 'Enter new password or leave blank' }}" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Role *</flux:label>
                        <flux:select wire:model.live="form.role_id" placeholder="Select role...">
                            @foreach ($roles as $role)
                                <flux:select.option value="{{ $role->id }}" class="text-gray-700">
                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:label>Status *</flux:label>
                        <flux:select wire:model="form.status" placeholder="Select status...">
                            <flux:select.option value="active" class="text-gray-700">
                                Active
                            </flux:select.option>
                            <flux:select.option value="inactive" class="text-gray-700">
                                Inactive
                            </flux:select.option>
                        </flux:select>
                    </flux:field>
                </div>

                @php
                    $selectedRole = $form['role_id'] ? $roles->firstWhere('id', (int) $form['role_id']) : null;
                @endphp

                @if ($selectedRole && $selectedRole->name === 'doctor')
                    <div class="rounded-lg bg-zinc-50 dark:bg-zinc-900">
                        <flux:text class="mb-3 text-sm font-medium text-zinc-500 dark:text-zinc-400">Doctor Profile</flux:text>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <flux:field>
                                <flux:label>License Number *</flux:label>
                                <flux:input wire:model="form.doctor.license_number" placeholder="e.g., PRC-1234567" />
                                <flux:error name="form.doctor.license_number" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Available for Appointments</flux:label>
                                <div class="pt-2">
                                    <flux:switch wire:model="form.doctor.is_available" />
                                </div>
                            </flux:field>
                        </div>
                    </div>
                @endif

                <flux:field>
                    <flux:label>Admin Category (Optional)</flux:label>
                    {{-- <flux:input wire:model="form.admin_category" placeholder="e.g., Healthcare, HIV, Pregnancy, Medical Records" />
                    <flux:error name="form.admin_category" /> --}}
                    <flux:select wire:model="form.admin_category" placeholder="Select category...">
                        @foreach ($categories as $category)
                            <flux:select.option value="{{ $category->value }}" class="text-gray-700">
                                {{ $category->label() }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <flux:separator />

            <div class="flex justify-end gap-2">
                <flux:button wire:click="{{ $showCreateModal ? 'closeCreateModal' : 'closeEditModal' }}" type="button" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary">{{ $showCreateModal ? 'Create User' : 'Update User' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
