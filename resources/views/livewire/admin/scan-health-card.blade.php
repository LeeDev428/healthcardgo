<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Scan Health Card</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Scan patient QR code or enter patient number manually</p>
        </div>

        <!-- Toggle Manual Entry -->
        <div class="mb-6">
            <flux:button wire:click="toggleManualEntry" variant="outline">
                {{ $showManualEntry ? 'Use QR Scanner' : 'Manual Entry' }}
            </flux:button>
        </div>

        <!-- Error Message -->
        @if($error)
            <flux:callout variant="danger" class="mb-6">
                {{ $error }}
            </flux:callout>
        @endif

        <!-- Success Message -->
        @if(session('success'))
            <flux:callout variant="success" class="mb-6">
                {{ session('success') }}
            </flux:callout>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Scanner Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                @if(!$showManualEntry)
                    <!-- QR Scanner -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Scan QR Code</h3>

                        <!-- Camera placeholder - In production, use a JS library like html5-qrcode -->
                        <div class="border-4 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Camera scanner will appear here</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Use a QR scanning library in production</p>
                        </div>

                        <!-- Manual paste for testing -->
                        <div class="mt-4">
                            <flux:field>
                                <flux:label>Or paste encrypted QR data</flux:label>
                                <flux:textarea
                                    wire:model="scannedData"
                                    rows="3"
                                    placeholder="Paste encrypted QR code data here for testing..."
                                />
                            </flux:field>

                            <flux:button wire:click="scanQrCode" class="mt-4" variant="primary">
                                Verify QR Code
                            </flux:button>
                        </div>
                    </div>
                @else
                    <!-- Manual Entry -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Manual Patient Lookup</h3>

                        <flux:field>
                            <flux:label>Patient Number</flux:label>
                            <flux:input
                                wire:model="manualPatientNumber"
                                placeholder="Enter patient number (e.g., P20240001)"
                            />
                            <flux:error name="manualPatientNumber" />
                        </flux:field>

                        <flux:button wire:click="lookupManual" variant="primary">
                            Search Patient
                        </flux:button>
                    </div>
                @endif
            </div>

            <!-- Patient Information Display -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                @if($patientData)
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Patient Information</h3>
                            @if($patientData['is_valid'])
                                <flux:badge color="green">Valid</flux:badge>
                            @else
                                <flux:badge color="red">Invalid</flux:badge>
                            @endif
                        </div>

                        <!-- Patient Photo -->
                        @if($patientData['patient']->photo_path)
                            <div class="flex justify-center">
                                <img src="{{ Storage::url($patientData['patient']->photo_path) }}"
                                     alt="Patient Photo"
                                     class="w-32 h-32 rounded-full object-cover border-4 border-blue-500">
                            </div>
                        @endif

                        <!-- Patient Details -->
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Patient Number</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $patientData['patient']->patient_number }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Full Name</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $patientData['patient']->user->name }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Date of Birth</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($patientData['patient']->date_of_birth)->format('F d, Y') }}
                                    ({{ \Carbon\Carbon::parse($patientData['patient']->date_of_birth)->age }} years old)
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Blood Type</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $patientData['patient']->blood_type ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Barangay</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $patientData['patient']->barangay?->name ?? 'N/A' }}</p>
                            </div>

                            @if($patientData['patient']->emergency_contact)
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Emergency Contact</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $patientData['patient']->emergency_contact['name'] ?? 'N/A' }}
                                        @if(isset($patientData['patient']->emergency_contact['phone']))
                                            <br><span class="text-xs">{{ $patientData['patient']->emergency_contact['phone'] }}</span>
                                        @endif
                                    </p>
                                </div>
                            @endif

                            @if($patientData['qr_data'])
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">QR Code Generated</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($patientData['qr_data']['generated_at'])->format('F d, Y h:i A') }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-3 mt-6">
                            <flux:button wire:click="clearData" variant="ghost">
                                Scan Another
                            </flux:button>
                            <a href="{{ route('admin.health-cards') }}" wire:navigate>
                                <flux:button variant="outline">
                                    View Health Cards
                                </flux:button>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center h-full text-center py-12">
                        <svg class="h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No patient data scanned yet</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Scan a QR code or enter patient number to view information</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
            <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-3">Instructions:</h4>
            <ul class="list-disc list-inside space-y-2 text-sm text-blue-800 dark:text-blue-200">
                <li>Scan the QR code on the patient's health card using the camera scanner</li>
                <li>The system will automatically decrypt and verify the patient information</li>
                <li>If scanning fails, use manual entry with the patient number</li>
                <li>QR codes older than 1 year may be flagged as invalid (patient should update)</li>
                <li>Ensure good lighting and steady hand for successful scanning</li>
            </ul>
        </div>
    </div>
</div>
