<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Create Medical Record</flux:heading>
            @if($patient)
                <flux:text class="mt-2">Creating medical record for: {{ $patient->fullName }} ({{ $patient->patient_number }})</flux:text>
            @else
                <flux:text class="mt-2">Create a new medical record for a patient</flux:text>
            @endif
        </div>

        @if($appointment)
            <flux:badge variant="primary">
                Appointment: {{ $appointment->appointment_number }}
            </flux:badge>
        @endif
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

    @if($patient)
        <!-- Patient Summary Card -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <flux:heading size="md" class="mb-4">Patient Information</flux:heading>

            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Patient Name</flux:text>
                    <flux:text weight="semibold">{{ $patient->fullName }}</flux:text>
                </div>

                <div>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Patient Number</flux:text>
                    <flux:text weight="semibold">{{ $patient->patient_number }}</flux:text>
                </div>

                <div>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Age / Gender</flux:text>
                    <flux:text weight="semibold">{{ $patient->age }} years / {{ ucfirst($patient->user->gender) }}</flux:text>
                </div>

                @if($patient->blood_type)
                    <div>
                        <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Blood Type</flux:text>
                        <flux:text weight="semibold">{{ $patient->blood_type }}</flux:text>
                    </div>
                @endif

                @if($patient->barangay)
                    <div>
                        <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Barangay</flux:text>
                        <flux:text weight="semibold">{{ $patient->barangay->name }}</flux:text>
                    </div>
                @endif

                @if($patient->allergies && !empty($patient->allergies))
                    <div>
                        <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Allergies</flux:text>
                        <flux:text weight="semibold" class="text-red-600 dark:text-red-400">
                            {{ is_array($patient->allergies) ? implode(', ', $patient->allergies) : $patient->allergies }}
                        </flux:text>
                    </div>
                @endif
            </div>
        </div>

        <!-- Template Selection -->
        @if(!$selectedTemplate)
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading size="md" class="mb-4">Select Medical Record Template</flux:heading>
                <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-6">
                    Choose the appropriate template based on the type of consultation or service provided.
                </flux:text>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($templates as $template)
                        <button
                            wire:click="selectTemplate('{{ $template['name'] }}')"
                            class="p-6 border-2 border-zinc-200 dark:border-zinc-700 rounded-lg hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all text-left group">
                            <div class="flex items-start gap-3">
                                <div class="shrink-0">
                                    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <flux:icon name="document-text" class="text-blue-600 dark:text-blue-400" />
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <flux:heading size="sm">{{ str_replace('_', ' ', ucwords($template['name'], '_')) }}</flux:heading>
                                    <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mt-1">
                                        {{ $template['category'] }}
                                    </flux:text>

                                    @if($template['requires_encryption'])
                                        <flux:badge variant="warning" size="sm" class="mt-2">
                                            <flux:icon name="lock-closed" size="xs" /> Encrypted
                                        </flux:badge>
                                    @endif
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Record Form -->
        @if($selectedTemplate && !empty($templateFields))
            <form wire:submit="createRecord">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 space-y-6">
                    <!-- Template Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-zinc-200 dark:border-zinc-700">
                        <div>
                            <flux:heading size="md">{{ str_replace('_', ' ', ucwords($selectedTemplate, '_')) }}</flux:heading>
                            <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mt-1">
                                Complete the form below to create the medical record
                            </flux:text>
                        </div>
                        <flux:button wire:click="$set('selectedTemplate', '')" variant="ghost" size="sm">
                            Change Template
                        </flux:button>
                    </div>

                    <!-- Dynamic Form Fields -->
                    <div class="grid md:grid-cols-2 gap-6">
                        @foreach($templateFields as $field)
                            <div class="{{ in_array($field['type'], ['textarea', 'json']) ? 'md:col-span-2' : '' }}">
                                <flux:field>
                                    <flux:label>
                                        {{ $field['label'] }}
                                        @if($field['required'])
                                            <span class="text-red-600">*</span>
                                        @endif
                                    </flux:label>

                                    @if($field['type'] === 'text' || $field['type'] === 'email' || $field['type'] === 'number')
                                        <flux:input
                                            wire:model="recordData.{{ $field['name'] }}"
                                            type="{{ $field['type'] }}"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                            {{ $field['readonly'] ?? false ? 'readonly' : '' }}
                                        />
                                    @elseif($field['type'] === 'date')
                                        <flux:input
                                            wire:model="recordData.{{ $field['name'] }}"
                                            type="date"
                                        />
                                    @elseif($field['type'] === 'textarea')
                                        <flux:textarea
                                            wire:model="recordData.{{ $field['name'] }}"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                            rows="4"
                                        />
                                    @elseif($field['type'] === 'select')
                                        <flux:select wire:model="recordData.{{ $field['name'] }}">
                                            <option value="">Select {{ $field['label'] }}</option>
                                            @foreach($field['options'] as $option)
                                                <option value="{{ $option }}">{{ ucfirst($option) }}</option>
                                            @endforeach
                                        </flux:select>
                                    @elseif($field['type'] === 'checkbox')
                                        <flux:checkbox wire:model="recordData.{{ $field['name'] }}" />
                                    @endif

                                    @error('recordData.'.$field['name'])
                                        <flux:error>{{ $message }}</flux:error>
                                    @enderror
                                </flux:field>
                            </div>
                        @endforeach
                    </div>

                    <!-- Additional Notes -->
                    <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:field>
                            <flux:label>Additional Notes (Optional)</flux:label>
                            <flux:textarea
                                wire:model="notes"
                                placeholder="Any additional observations, recommendations, or follow-up instructions..."
                                rows="4"
                            />
                        </flux:field>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-3 justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:button type="button" variant="ghost" wire:click="$set('selectedTemplate', '')">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            <flux:icon name="check" />
                            Create Medical Record
                        </flux:button>
                    </div>
                </div>
            </form>
        @endif
    @else
        <!-- No Patient Selected -->
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-zinc-100 dark:bg-zinc-800 mb-4">
                <flux:icon name="user-group" size="lg" class="text-zinc-400" />
            </div>

            <flux:heading size="lg" class="mb-2">No Patient Selected</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400 mb-6 max-w-md mx-auto">
                Please select a patient from an appointment or search for a patient to create a medical record.
            </flux:text>

            <flux:button href="{{ route('doctor.appointments.list') }}" variant="primary">
                View Appointments
            </flux:button>
        </div>
    @endif
</div>
