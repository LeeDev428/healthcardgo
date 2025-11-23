<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Pending Patient Approvals</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Review and approve patient registration requests
        </p>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg flex items-center gap-3 mb-4">
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($pendingUsers->isEmpty())
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 text-center py-12">
            <div class="text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-lg font-medium">No pending approvals</p>
                <p class="text-sm mt-1">All patient registrations have been processed</p>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($pendingUsers as $user)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex-1">
                            <div class="flex items-start space-x-4">
                                @if($user->patient && $user->patient->photo_path)
                                    <img src="{{ Storage::url($user->patient->photo_path) }}"
                                         alt="{{ $user->name }}"
                                         class="h-16 w-16 rounded-full object-cover">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="text-xl font-semibold text-gray-600 dark:text-gray-300">
                                            {{ substr($user->name, 0, 1) }}
                                        </span>
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $user->name }}
                                    </h3>
                                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-600 dark:text-gray-400">
                                        <div>
                                            <span class="font-medium">Email:</span> {{ $user->email }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Contact:</span> {{ $user->contact_number }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Date of Birth:</span>
                                            {{ $user->patient?->date_of_birth ? \Carbon\Carbon::parse($user->patient->date_of_birth)->format('M d, Y') : 'N/A' }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Gender:</span> {{ ucfirst($user->patient?->gender ?? 'N/A') }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Barangay:</span> {{ $user->patient?->barangay?->name ?? 'N/A' }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Blood Type:</span> {{ $user->patient?->blood_type ?? 'N/A' }}
                                        </div>
                                        @if($user->patient && $user->patient->emergency_contact)
                                            <div class="md:col-span-2">
                                                <span class="font-medium">Emergency Contact:</span>
                                                {{ $user->patient->emergency_contact['name'] ?? 'N/A' }}
                                                ({{ $user->patient->emergency_contact['number'] ?? 'N/A' }})
                                            </div>
                                        @endif
                                    </div>

                                    @if($user->patient && ($user->patient->allergies || $user->patient->current_medications || $user->patient->medical_history))
                                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                            @if($user->patient->allergies)
                                                <div class="text-sm mb-1">
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">Allergies:</span>
                                                    <span class="text-gray-600 dark:text-gray-400">{{ is_array($user->patient->allergies) ? implode(', ', $user->patient->allergies) : $user->patient->allergies }}</span>
                                                </div>
                                            @endif
                                            @if($user->patient->current_medications)
                                                <div class="text-sm mb-1">
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">Current Medications:</span>
                                                    <span class="text-gray-600 dark:text-gray-400">{{ is_array($user->patient->current_medications) ? implode(', ', $user->patient->current_medications) : $user->patient->current_medications }}</span>
                                                </div>
                                            @endif
                                            @if($user->patient->medical_history)
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">Medical History:</span>
                                                    <span class="text-gray-600 dark:text-gray-400">{{ is_array($user->patient->medical_history) ? implode(', ', $user->patient->medical_history) : $user->patient->medical_history }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Submitted ID Preview -->
                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Submitted ID</p>
                                        @if($user->patient && $user->patient->photo_path)
                                            <div class="flex items-center gap-4">
                                                <a href="{{ Storage::url($user->patient->photo_path) }}" target="_blank" class="block">
                                                    <img src="{{ Storage::url($user->patient->photo_path) }}"
                                                         alt="Submitted ID for {{ $user->name }}"
                                                         class="h-24 w-36 object-cover rounded border border-gray-200 dark:border-gray-700">
                                                </a>
                                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                                    <p class="mb-1">The applicant uploaded an ID. Click the image to view full size.</p>
                                                    <a href="{{ Storage::url($user->patient->photo_path) }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">Open in new tab</a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-4">
                                                <div class="h-24 w-36 rounded border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-900 flex items-center justify-center">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">No ID uploaded</span>
                                                </div>
                                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                                    <p class="mb-1">The applicant did not upload an ID document.</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                        Registered: {{ $user->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 md:mt-0 md:ml-6 flex space-x-2">
                            <flux:button wire:click="approve({{ $user->id }})" variant="primary" color="green">
                                Approve
                            </flux:button>
                            <flux:button wire:click="openRejectModal({{ $user->id }})" variant="danger">
                                Reject
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $pendingUsers->links() }}
        </div>
    @endif

    <!-- Reject Modal -->
    @if($showRejectModal)
        <flux:modal wire:model="showRejectModal">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reject Registration</h3>

                <flux:field>
                    <flux:label>Reason for Rejection *</flux:label>
                    <flux:textarea wire:model="rejectionReason"
                                   placeholder="Please provide a reason for rejection (minimum 10 characters)..."
                                   rows="4" />
                    <flux:error name="rejectionReason" />
                </flux:field>

                <div class="mt-6 flex justify-end space-x-2">
                    <flux:button wire:click="$set('showRejectModal', false)" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button wire:click="reject" variant="danger">
                        Confirm Rejection
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
