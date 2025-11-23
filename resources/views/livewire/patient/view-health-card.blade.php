<div class="space-y-6">
    <!-- Header -->
    <div>
        <flux:heading size="xl">My Health Card</flux:heading>
        <flux:text class="mt-2">View and download your official Panabo City Health Card.</flux:text>
    </div>

    <!-- Flash Messages -->
    @if(session()->has('success'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('success') }}
        </flux:callout>
    @endif

    @if(session()->has('error'))
        <flux:callout variant="danger" icon="x-circle">
            {{ session('error') }}
        </flux:callout>
    @endif

    @if(!$patient)
        <!-- No Patient Profile Message -->
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-zinc-100 dark:bg-zinc-800 mb-4">
                <flux:icon name="user-circle" size="lg" class="text-zinc-400" />
            </div>

            <flux:heading size="lg" class="mb-2">Complete Your Patient Profile</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400 mb-6 max-w-md mx-auto">
                You need to complete your patient profile before you can access health card services.
            </flux:text>

            <flux:button href="{{ route('patient.profile') }}" variant="primary" wire:navigate>
                Complete Profile
            </flux:button>
        </div>

    @elseif($healthCard)
        <!-- Health Card Display -->
        <div class="bg-linear-to-br from-purple-600 via-blue-600 to-purple-700 rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8">
                <!-- Header -->
                <div class="text-center text-white mb-6">
                    <flux:heading size="xl" class="text-white">Panabo City Health Office</flux:heading>
                    <flux:text class="text-purple-100 text-sm">Official Health Card</flux:text>
                </div>

                <!-- Card Content -->
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6">
                    <div class="grid md:grid-cols-3 gap-6 items-center">
                        <!-- Patient Photo & Basic Info -->
                        <div class="text-center space-y-3">
                            @if($patient->photo_path)
                                <img src="{{ Storage::url($patient->photo_path) }}"
                                     alt="Patient Photo"
                                     class="w-32 h-32 rounded-full object-cover border-4 border-white/50 mx-auto shadow-lg">
                            @else
                                <div class="w-32 h-32 rounded-full bg-white/20 flex items-center justify-center mx-auto border-4 border-white/50">
                                    <flux:icon name="user" size="lg" class="text-white/60" />
                                </div>
                            @endif

                            <div class="text-white">
                                <flux:text weight="bold" size="lg" class="text-white">{{ $patient->full_name }}</flux:text>
                                <flux:text size="sm" class="text-purple-100">{{ $patient->patient_number }}</flux:text>
                            </div>
                        </div>

                        <!-- Patient Details -->
                        <div class="space-y-3 text-white">
                            <div>
                                <flux:text size="sm" class="text-purple-200">Card Number</flux:text>
                                <flux:text weight="semibold" class="text-white">{{ $healthCard->card_number }}</flux:text>
                            </div>

                            <div>
                                <flux:text size="sm" class="text-purple-200">Blood Type</flux:text>
                                <flux:text weight="semibold" class="text-white">{{ $healthCard->medical_data['blood_type'] ?? 'N/A' }}</flux:text>
                            </div>

                            <div>
                                <flux:text size="sm" class="text-purple-200">Barangay</flux:text>
                                <flux:text weight="semibold" class="text-white">{{ $healthCard->medical_data['barangay'] ?? 'N/A' }}</flux:text>
                            </div>

                            @php
                                $allergies = $healthCard->medical_data['allergies'] ?? [];
                                $allergiesText = is_array($allergies) ? implode(', ', array_filter($allergies)) : $allergies;
                            @endphp
                            @if(!empty($allergiesText))
                                <div>
                                    <flux:text size="sm" class="text-purple-200">Allergies</flux:text>
                                    <flux:text weight="semibold" class="text-white">{{ $allergiesText }}</flux:text>
                                </div>
                            @endif

                            @php
                                $emergencyContact = $healthCard->medical_data['emergency_contact'] ?? null;
                                $emergencyContactText = '';
                                if ($emergencyContact) {
                                    if (is_array($emergencyContact)) {
                                        $name = $emergencyContact['name'] ?? '';
                                        $phone = $emergencyContact['phone'] ?? $emergencyContact['number'] ?? '';
                                        $emergencyContactText = trim("$name - $phone");
                                    } else {
                                        $emergencyContactText = $emergencyContact;
                                    }
                                }
                            @endphp
                            @if(!empty($emergencyContactText))
                                <div>
                                    <flux:text size="sm" class="text-purple-200">Emergency Contact</flux:text>
                                    <flux:text weight="semibold" class="text-white">{{ $emergencyContactText }}</flux:text>
                                </div>
                            @endif
                        </div>

                        <!-- QR Code -->
                        <div class="text-center space-y-3">
                            <div class="bg-white p-4 rounded-xl shadow-lg">
                                <img src="{{ $healthCard->qr_code }}" alt="QR Code" class="w-full max-w-[200px] mx-auto">
                            </div>
                            <flux:text size="xs" class="text-purple-100">Scan for verification</flux:text>
                        </div>
                    </div>
                </div>

                <!-- Card Dates & Status -->
                <div class="mt-6 grid md:grid-cols-3 gap-4 text-white">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center">
                        <flux:text size="sm" class="text-purple-200">Issue Date</flux:text>
                        <flux:text weight="semibold" class="text-white">{{ $healthCard->issue_date->format('M d, Y') }}</flux:text>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center">
                        <flux:text size="sm" class="text-purple-200">Expiry Date</flux:text>
                        <flux:text weight="semibold" class="text-white">{{ $healthCard->expiry_date->format('M d, Y') }}</flux:text>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center">
                        <flux:text size="sm" class="text-purple-200">Status</flux:text>
                        <div class="mt-1">
                            @if($healthCard->status === 'active' && !$healthCard->isExpired())
                                <flux:badge variant="success">Active</flux:badge>
                            @elseif($healthCard->isExpired())
                                <flux:badge variant="danger">Expired</flux:badge>
                            @elseif($healthCard->status === 'suspended')
                                <flux:badge variant="warning">Suspended</flux:badge>
                            @elseif($healthCard->status === 'revoked')
                                <flux:badge variant="danger">Revoked</flux:badge>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Download Buttons -->
                <div class="mt-6 flex flex-col md:flex-row gap-3 justify-center">
                    <flux:button
                        wire:click="downloadPdf"
                        variant="outline"
                        icon="arrow-down-tray"
                        class="w-full md:w-auto">
                        Download PDF
                    </flux:button>

                    <flux:button
                        wire:click="downloadPng"
                        variant="outline"
                        icon="photo"
                        class="w-full md:w-auto">
                        Download Image (PNG)
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Status Messages -->
        @if($healthCard->isExpired())
            <flux:callout variant="warning" icon="exclamation-triangle">
                Your health card has expired. Please visit the Panabo City Health Office to renew it.
            </flux:callout>
        @elseif($healthCard->status === 'suspended')
            <flux:callout variant="warning" icon="pause">
                Your health card is currently suspended. Please contact the Panabo City Health Office for more information.
            </flux:callout>
        @elseif($healthCard->status === 'revoked')
            <flux:callout variant="danger" icon="x-circle">
                Your health card has been revoked. Please contact the Panabo City Health Office for assistance.
            </flux:callout>
        @endif

        <!-- Instructions -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <flux:heading size="md" class="mb-4">How to Use Your Health Card</flux:heading>

            <div class="space-y-3">
                <div class="flex gap-3">
                    <div class="shrink-0">
                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                            <flux:text weight="bold" class="text-blue-600 dark:text-blue-400">1</flux:text>
                        </div>
                    </div>
                    <div>
                        <flux:text weight="semibold">Keep It Safe</flux:text>
                        <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                            Store your health card in a safe place. You can download the PDF for printing or save it to your device.
                        </flux:text>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="shrink-0">
                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                            <flux:text weight="bold" class="text-blue-600 dark:text-blue-400">2</flux:text>
                        </div>
                    </div>
                    <div>
                        <flux:text weight="semibold">Present When Required</flux:text>
                        <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                            Show your health card when visiting health facilities or during medical emergencies.
                        </flux:text>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="shrink-0">
                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                            <flux:text weight="bold" class="text-blue-600 dark:text-blue-400">3</flux:text>
                        </div>
                    </div>
                    <div>
                        <flux:text weight="semibold">QR Code Verification</flux:text>
                        <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                            Medical staff can scan the QR code to quickly verify your information and access your health records.
                        </flux:text>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="shrink-0">
                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                            <flux:text weight="bold" class="text-blue-600 dark:text-blue-400">4</flux:text>
                        </div>
                    </div>
                    <div>
                        <flux:text weight="semibold">Check Expiry Date</flux:text>
                        <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                            Make sure to renew your health card before it expires to maintain continuous access to health services.
                        </flux:text>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- No Health Card Message -->
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-zinc-100 dark:bg-zinc-800 mb-4">
                <flux:icon name="identification" size="lg" class="text-zinc-400" />
            </div>

            <flux:heading size="lg" class="mb-2">No Health Card Issued Yet</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400 mb-6 max-w-md mx-auto">
                You don't have a health card yet. Your health card will be generated by the Panabo City Health Office once your patient profile is complete and approved.
            </flux:text>

            <flux:callout variant="info" icon="information-circle" class="max-w-lg mx-auto">
                <div class="text-left">
                    <flux:text weight="semibold" class="mb-2">To get your health card:</flux:text>
                    <ol class="list-decimal list-inside space-y-1 text-sm">
                        <li>Ensure your profile is complete with all required information</li>
                        <li>Upload a clear photo for your health card</li>
                        <li>Provide accurate medical information (blood type, allergies, etc.)</li>
                        <li>Wait for the health office to generate your card</li>
                    </ol>
                </div>
            </flux:callout>
        </div>
    @endif
</div>
