<div class="max-w-3xl mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Patient Registration</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Please fill in your information to register. Your account will be pending approval.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6">
            <form wire:submit="register">
                <!-- Personal Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Full Name *</flux:label>
                            <flux:input wire:model="name" placeholder="Juan Dela Cruz" />
                            <flux:error name="name" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Email Address *</flux:label>
                            <flux:input type="email" wire:model="email" placeholder="juan@example.com" />
                            <flux:error name="email" />
                        </flux:field>

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

                        <flux:field class="md:col-span-2">
                            <flux:label>Upload Valid ID *</flux:label>
                            <flux:input type="file" wire:model="photo" accept="image/*"
                                   class="flex w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-blue-50 file:text-blue-700
                                          hover:file:bg-blue-100
                                          dark:file:bg-gray-700 dark:file:text-gray-300" />
                            <flux:error name="photo" />
                            @if ($photo)
                                <div class="mt-2">
                                    <img src="{{ $photo->temporaryUrl() }}" class="h-32 w-32 object-cover rounded-lg">
                                </div>
                            @endif
                        </flux:field>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Emergency Contact</h3>
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

                <!-- Medical Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Medical Information (Optional)</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <flux:field>
                            <flux:label>Known Allergies</flux:label>
                            <flux:textarea wire:model="allergies" placeholder="List any known allergies..." rows="2" />
                            <flux:error name="allergies" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Current Medications</flux:label>
                            <flux:textarea wire:model="current_medications" placeholder="List current medications..." rows="2" />
                            <flux:error name="current_medications" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Medical History</flux:label>
                            <flux:textarea wire:model="medical_history" placeholder="Any existing medical conditions..." rows="2" />
                            <flux:error name="medical_history" />
                        </flux:field>
                    </div>
                </div>

                <!-- Account Security -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Security</h3>
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

                <div class="flex items-center justify-between">
                    <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400">
                        Already have an account? Login
                    </a>
                    <flux:button type="submit" variant="primary" color="cyan">
                        Register
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
