<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Manage Health Cards</flux:heading>
            <flux:text class="mt-2">Generate and manage patient health cards with QR codes.</flux:text>
        </div>
        <div class="flex gap-2">
            <flux:button wire:click="openCreateModal" variant="primary" icon="plus" color="blue">
                Generate New Health Card
            </flux:button>
            <flux:button variant="primary" icon="plus" color="cyan" href="{{ route('admin.health-card-history') }}">
                Manage Historical Data
            </flux:button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('success') }}
        </flux:callout>
    @endif

    @if (session('error'))
        <flux:callout variant="danger" icon="x-circle">
            {{ session('error') }}
        </flux:callout>
    @endif

    <!-- Issuance Trend with SARIMA Predictions -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="flex items-center justify-between mb-3">
            <div>
                <flux:heading size="md">Health Card Issuance Trend & Predictions</flux:heading>
                <flux:text size="sm" class="text-zinc-500 mt-1">
                    Historical data (blue solid) and SARIMA forecasts (orange dashed)
                </flux:text>
            </div>
            @if($chartData['has_predictions'])
                <flux:badge color="green">Predictions Active</flux:badge>
            @else
                <flux:badge color="yellow">{{ $chartData['message'] ?? 'Insufficient Data' }}</flux:badge>
            @endif
        </div>
        <div class="relative h-64">
            <canvas
                id="health-cards-trend"
                data-labels='@json($chartData['labels'])'
                data-actual='@json($chartData['actual'])'
                data-predicted='@json($chartData['predicted'])'
                data-confidence-lower='@json($chartData['confidence_lower'])'
                data-confidence-upper='@json($chartData['confidence_upper'])'
                data-has-predictions='{{ $chartData['has_predictions'] ? 'true' : 'false' }}'
            ></canvas>
        </div>
    </div>

    <!-- Search -->
    <div class="flex gap-4 md:w-1/2 w-full md:mt-10">
        <flux:label>Search:</flux:label>
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Search by patient name, email, or card number..."
            class="flex-1"
            icon="magnifying-glass"
        />
    </div>

    <!-- Health Cards Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Card Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Blood Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Issue Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Expiry Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($healthCards as $card)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:text weight="semibold">{{ $card->card_number }}</flux:text>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <flux:text weight="semibold">{{ $card->patient->user->name }}</flux:text>
                                    <flux:text size="sm" class="text-zinc-500">{{ $card->patient->patient_number }}</flux:text>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge>{{ $card->medical_data['blood_type'] ?? 'N/A' }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:text size="sm">{{ $card->issue_date->format('M d, Y') }}</flux:text>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:text size="sm">{{ $card->expiry_date->format('M d, Y') }}</flux:text>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($card->status === 'active' && !$card->isExpired())
                                    <flux:badge variant="success">Active</flux:badge>
                                @elseif($card->isExpired())
                                    <flux:badge variant="danger">Expired</flux:badge>
                                @elseif($card->status === 'suspended')
                                    <flux:badge variant="warning">Suspended</flux:badge>
                                @elseif($card->status === 'revoked')
                                    <flux:badge variant="danger">Revoked</flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">
                                    <flux:button size="sm" variant="ghost" wire:click="viewCard({{ $card->id }})" icon="eye">
                                        View
                                    </flux:button>
                                    <a href="{{ route('admin.health-cards.download-pdf', $card->id) }}" target="_blank">
                                        <flux:button size="sm" variant="ghost" icon="arrow-down-tray">
                                            PDF
                                        </flux:button>
                                    </a>

                                    @if($card->status === 'active' && !$card->isExpired())
                                        <flux:button size="sm" variant="ghost" wire:click="suspendCard({{ $card->id }})" icon="pause">
                                            Suspend
                                        </flux:button>
                                    @elseif($card->status === 'suspended')
                                        <flux:button size="sm" variant="ghost" wire:click="activateCard({{ $card->id }})" icon="play">
                                            Activate
                                        </flux:button>
                                    @elseif($card->isExpired())
                                        <flux:button size="sm" variant="primary" wire:click="renewCard({{ $card->id }})" icon="arrow-path">
                                            Renew
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <flux:text class="text-zinc-500">No health cards found.</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $healthCards->links() }}
        </div>
    </div>

    <!-- Create/Generate Modal -->
    @if($showCreateModal)
        <flux:modal wire:model="showCreateModal" class="space-y-6">
            <div>
                <flux:heading size="lg">Generate Health Card</flux:heading>
                <flux:text class="mt-2">
                    Select a patient and generate their official health card with QR code.
                </flux:text>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Patient</flux:label>
                    <flux:select wire:model="patient_id">
                        <option value="">Select a patient...</option>
                        @foreach($availablePatients as $patient)
                            <option value="{{ $patient->id }}">
                                {{ $patient->full_name }} - {{ $patient->patient_number }}
                            </option>
                        @endforeach
                    </flux:select>
                    <flux:error name="patient_id" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Issue Date</flux:label>
                        <flux:input type="date" wire:model="issue_date" />
                        <flux:error name="issue_date" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Expiry Date</flux:label>
                        <flux:input type="date" wire:model="expiry_date" />
                        <flux:error name="expiry_date" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model="status">
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>

                <flux:field>
                    <flux:label>Notes (Optional)</flux:label>
                    <flux:textarea wire:model="notes" placeholder="Any additional notes about this health card..." rows="3" />
                    <flux:error name="notes" />
                </flux:field>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:button variant="ghost" wire:click="$set('showCreateModal', false)">
                    Cancel
                </flux:button>
                <flux:button variant="primary" wire:click="generateCard" icon="document-plus">
                    Generate Health Card
                </flux:button>
            </div>
        </flux:modal>
    @endif

    <!-- View Modal -->
    @if($showViewModal && $selectedCard)
        <flux:modal wire:model="showViewModal" class="space-y-6 max-w-2xl">
            <div>
                <flux:heading size="lg">Health Card Details</flux:heading>
                <flux:text class="mt-2">{{ $selectedCard->card_number }}</flux:text>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Patient Info -->
                <div class="space-y-3">
                    <flux:heading size="md">Patient Information</flux:heading>

                    @if($selectedPatient->photo_path)
                        <div class="flex justify-center">
                            <img src="{{ Storage::url($selectedPatient->photo_path) }}"
                                 alt="Patient Photo"
                                 class="w-24 h-24 rounded-full object-cover border-2 border-blue-500">
                        </div>
                    @endif

                    <div>
                        <flux:text size="sm" class="text-zinc-500">Name</flux:text>
                        <flux:text weight="semibold">{{ $selectedPatient->user->name }}</flux:text>
                    </div>

                    <div>
                        <flux:text size="sm" class="text-zinc-500">Patient Number</flux:text>
                        <flux:text weight="semibold">{{ $selectedPatient->patient_number }}</flux:text>
                    </div>

                    <div>
                        <flux:text size="sm" class="text-zinc-500">Blood Type</flux:text>
                        <flux:text weight="semibold">{{ $selectedCard->medical_data['blood_type'] ?? 'N/A' }}</flux:text>
                    </div>

                    <div>
                        <flux:text size="sm" class="text-zinc-500">Barangay</flux:text>
                        <flux:text weight="semibold">{{ $selectedCard->medical_data['barangay'] ?? 'N/A' }}</flux:text>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="space-y-3">
                    <flux:heading size="md">QR Code</flux:heading>

                    <div class="bg-white p-4 rounded-lg border border-zinc-200">
                        <img src="{{ $selectedCard->qr_code }}" alt="QR Code" class="w-full max-w-xs mx-auto">
                    </div>

                    <flux:text size="sm" class="text-zinc-500 text-center">
                        Scan to verify patient information
                    </flux:text>
                </div>
            </div>

            <!-- Card Details -->
            <div class="grid grid-cols-2 gap-4 p-4 bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                <div>
                    <flux:text size="sm" class="text-zinc-500">Issue Date</flux:text>
                    <flux:text weight="semibold">{{ $selectedCard->issue_date->format('F d, Y') }}</flux:text>
                </div>

                <div>
                    <flux:text size="sm" class="text-zinc-500">Expiry Date</flux:text>
                    <flux:text weight="semibold">{{ $selectedCard->expiry_date->format('F d, Y') }}</flux:text>
                </div>

                <div>
                    <flux:text size="sm" class="text-zinc-500">Status</flux:text>
                    @if($selectedCard->status === 'active')
                        <flux:badge variant="success">Active</flux:badge>
                    @elseif($selectedCard->status === 'suspended')
                        <flux:badge variant="warning">Suspended</flux:badge>
                    @elseif($selectedCard->status === 'revoked')
                        <flux:badge variant="danger">Revoked</flux:badge>
                    @elseif($selectedCard->status === 'expired')
                        <flux:badge variant="danger">Expired</flux:badge>
                    @endif
                </div>

                @if($selectedCard->last_renewed_at)
                    <div>
                        <flux:text size="sm" class="text-zinc-500">Last Renewed</flux:text>
                        <flux:text weight="semibold">{{ $selectedCard->last_renewed_at->format('F d, Y') }}</flux:text>
                    </div>
                @endif
            </div>

            <div class="flex gap-2 justify-end">
                <flux:button variant="ghost" wire:click="closeViewModal">
                    Close
                </flux:button>
                <a href="{{ route('admin.health-cards.download-pdf', $selectedCard->id) }}" target="_blank">
                    <flux:button variant="primary" icon="arrow-down-tray">
                        Download PDF
                    </flux:button>
                </a>
                <flux:button variant="primary" wire:click="regeneratePdf({{ $selectedCard->id }})" icon="arrow-path">
                    Regenerate PDF
                </flux:button>
            </div>
        </flux:modal>
    @endif
</div>
