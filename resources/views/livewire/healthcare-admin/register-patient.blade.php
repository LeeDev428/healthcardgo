<div class="p-6 max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Register Patient</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Register a walk-in patient, with optional user account creation.</p>
        </div>

        <flux:button icon="arrow-left" variant="primary" color="cyan" href="{{ route('healthcare_admin.patients') }}">
            Back to Patients
        </flux:button>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
        <form wire:submit="submit" class="space-y-8">
            <!-- Registration Mode -->
            <div class="bg-zinc-50 dark:bg-zinc-900/30 rounded-md p-4 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-zinc-900 dark:text-white">Create Patient Portal Account?</h3>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1">Enable to create a login account for the patient. Disable for walk-in record only.</p>
                    </div>
                    <flux:switch wire:model="create_user_account" />
                </div>
            </div>
            <!-- Personal Information -->
            <div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Full Name *</flux:label>
                        <flux:input wire:model="name" placeholder="Juan Dela Cruz" />
                        <flux:error name="name" />
                    </flux:field>

                    <template x-if="$wire.create_user_account">
                        <div>
                            <flux:field>
                                <flux:label>Email Address *</flux:label>
                                <flux:input type="email" wire:model="email" placeholder="juan@example.com" />
                                <flux:error name="email" />
                            </flux:field>
                        </div>
                    </template>

                    <flux:field>
                        <flux:label>Contact Number *</flux:label>
                        <flux:input wire:model="contact_number" placeholder="09171234567" />
                        <flux:error name="contact_number" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Date of Birth *</flux:label>
                        <flux:input type="date" wire:model="date_of_birth" />
                        <flux:error name="date_of_birth" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Gender *</flux:label>
                        <flux:select wire:model="gender">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </flux:select>
                        <flux:error name="gender" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Blood Type</flux:label>
                        <flux:select wire:model="blood_type">
                            <option value="">Select Blood Type</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </flux:select>
                        <flux:error name="blood_type" />
                    </flux:field>

                    <flux:field class="md:col-span-2">
                        <flux:label>Barangay *</flux:label>
                        <flux:select wire:model="barangay_id">
                            <option value="">Select Barangay</option>
                            @foreach($barangays as $barangay)
                                <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="barangay_id" />
                    </flux:field>

                    {{-- <flux:field class="flex md:col-span-2">
                        <flux:label>Patient's Valid ID *</flux:label>
                        <input type="file" wire:model="photo" accept="image/*"
                               class="block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100
                                      dark:file:bg-gray-700 dark:file:text-gray-300">
                        <flux:error name="photo" />
                        @if ($photo)
                            <div class="mt-2">
                                <img src="{{ $photo->temporaryUrl() }}" class="h-32 w-32 object-cover rounded-lg">
                            </div>
                        @endif
                    </flux:field> --}}
                </div>
            </div>

            <!-- Emergency Contact -->
            <div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Emergency Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Contact Name *</flux:label>
                        <flux:input wire:model="emergency_contact_name" placeholder="Emergency contact name" />
                        <flux:error name="emergency_contact_name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Contact Number *</flux:label>
                        <flux:input wire:model="emergency_contact_number" placeholder="Emergency contact number" />
                        <flux:error name="emergency_contact_number" />
                    </flux:field>
                </div>
            </div>

            <!-- Account Security (conditional) -->
            <div x-data="{ enabled: $wire.entangle('create_user_account') }" x-show="enabled" x-cloak>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Account Security</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Password *</flux:label>
                        <flux:input type="password" wire:model="password" placeholder="Minimum 8 characters" />
                        <flux:error name="password" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Confirm Password *</flux:label>
                        <flux:input type="password" wire:model="password_confirmation" placeholder="Confirm password" />
                        <flux:error name="password_confirmation" />
                    </flux:field>
                </div>
            </div>

            <!-- Disease Information (required for walk-ins) -->
            <div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Disease Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Disease Type <span class="text-zinc-500 text-xs">(Required for walk-in)</span></flux:label>
                        <flux:select wire:model="disease_type">
                            <option value="">Select Disease</option>
                            <option value="rabies">Rabies</option>
                            <option value="malaria">Malaria</option>
                            <option value="dengue">Dengue</option>
                            <option value="measles">Measles</option>
                        </flux:select>
                        <flux:error name="disease_type" />
                    </flux:field>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('healthcare_admin.patients') }}">
                    <flux:button variant="ghost">Cancel</flux:button>
                </a>
                <flux:button type="submit" variant="primary" color="blue" class="cursor-pointer">
                    Register Patient
                </flux:button>
            </div>
        </form>
    </div>
</div>
