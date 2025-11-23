<div class="min-h-screen bg-zinc-50 dark:bg-zinc-800">
  <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
      <flux:heading size="2xl" class="text-zinc-900 dark:text-white mb-2">
        {{ $patient ? __('Edit Profile') : __('Complete Your Profile') }}
      </flux:heading>
      <flux:text class="text-zinc-600 dark:text-zinc-400">
        {{ $patient ? __('Update your personal and medical information') : __('Please complete your profile to access all features') }}
      </flux:text>
    </div>

    @if (session('success'))
      <flux:callout variant="success" icon="check-circle" class="mb-6">
        {{ session('success') }}
      </flux:callout>
    @endif

    <form wire:submit="save">
      <!-- Personal Information Section -->
      <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm mb-6">
        <flux:heading size="lg" class="mb-6 text-zinc-900 dark:text-white">
          Personal Information
        </flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Date of Birth -->
          <flux:field>
            <flux:label>Date of Birth <span class="text-red-500">*</span></flux:label>
            <flux:input type="date" wire:model="date_of_birth" max="{{ date('Y-m-d') }}" />
            <flux:error name="date_of_birth" />
          </flux:field>

          <!-- Gender -->
          <flux:field>
            <flux:label>Gender <span class="text-red-500">*</span></flux:label>
            <flux:select wire:model="gender" placeholder="Select gender...">
              <option value="male">Male</option>
              <option value="female">Female</option>
            </flux:select>
            <flux:error name="gender" />
          </flux:field>

          <!-- Barangay -->
          <flux:field>
            <flux:label>Barangay <span class="text-red-500">*</span></flux:label>
            <flux:select wire:model="barangay_id" placeholder="Select barangay...">
              @foreach($barangays as $barangay)
                <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
              @endforeach
            </flux:select>
            <flux:error name="barangay_id" />
          </flux:field>

          <!-- Blood Type -->
          <flux:field>
            <flux:label>Blood Type</flux:label>
            <flux:select wire:model="blood_type" placeholder="Select blood type...">
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

          <!-- PhilHealth Number -->
          <flux:field class="md:col-span-2">
            <flux:label>PhilHealth Number</flux:label>
            <flux:input wire:model="philhealth_number" placeholder="Enter PhilHealth number" />
            <flux:error name="philhealth_number" />
          </flux:field>
        </div>
      </div>

      <!-- Emergency Contact Section -->
      <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm mb-6">
        <flux:heading size="lg" class="mb-6 text-zinc-900 dark:text-white">
          Emergency Contact
        </flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Emergency Contact Name -->
          <flux:field>
            <flux:label>Contact Name <span class="text-red-500">*</span></flux:label>
            <flux:input wire:model="emergency_contact_name" placeholder="Enter contact name" />
            <flux:error name="emergency_contact_name" />
          </flux:field>

          <!-- Emergency Contact Number -->
          <flux:field>
            <flux:label>Contact Number <span class="text-red-500">*</span></flux:label>
            <flux:input wire:model="emergency_contact_number" placeholder="09XXXXXXXXX" />
            <flux:error name="emergency_contact_number" />
          </flux:field>

          <!-- Relationship -->
          <flux:field class="md:col-span-2">
            <flux:label>Relationship</flux:label>
            <flux:input wire:model="emergency_contact_relationship" placeholder="e.g., Spouse, Parent, Sibling" />
            <flux:error name="emergency_contact_relationship" />
          </flux:field>
        </div>
      </div>

      <!-- Medical Information Section -->
      <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm mb-6">
        <flux:heading size="lg" class="mb-6 text-zinc-900 dark:text-white">
          Medical Information
        </flux:heading>

        <div class="grid grid-cols-1 gap-6">
          <!-- Allergies -->
          <flux:field>
            <flux:label>Allergies</flux:label>
            <flux:textarea wire:model="allergies" placeholder="List any allergies (separate with commas)" rows="3" />
            <flux:description>Separate multiple allergies with commas</flux:description>
            <flux:error name="allergies" />
          </flux:field>

          <!-- Current Medications -->
          <flux:field>
            <flux:label>Current Medications</flux:label>
            <flux:textarea wire:model="current_medications" placeholder="List current medications (separate with commas)" rows="3" />
            <flux:description>Separate multiple medications with commas</flux:description>
            <flux:error name="current_medications" />
          </flux:field>

          <!-- Medical History -->
          <flux:field>
            <flux:label>Medical History</flux:label>
            <flux:textarea wire:model="medical_history" placeholder="Previous illnesses, surgeries, conditions (separate with commas)" rows="3" />
            <flux:description>Separate multiple entries with commas</flux:description>
            <flux:error name="medical_history" />
          </flux:field>

          <!-- Accessibility Requirements -->
          <flux:field>
            <flux:label>Accessibility Requirements</flux:label>
            <flux:textarea wire:model="accessibility_requirements" placeholder="Any special accessibility needs" rows="2" />
            <flux:error name="accessibility_requirements" />
          </flux:field>
        </div>
      </div>

      <!-- Photo Section -->
      <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm mb-6">
        <flux:heading size="lg" class="mb-6 text-zinc-900 dark:text-white">
          Profile Photo
        </flux:heading>

        <div class="flex items-start gap-6">
          @if($existing_photo_path)
            <div class="shrink-0">
              <img src="{{ Storage::url($existing_photo_path) }}" alt="Profile Photo" class="h-32 w-32 rounded-lg object-cover border-2 border-zinc-200 dark:border-zinc-700">
            </div>
            <div class="flex-1">
              <flux:button type="button" wire:click="removePhoto" variant="danger" size="sm">
                Remove Photo
              </flux:button>
              <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-2">
                Upload a new photo to replace the current one
              </flux:text>
            </div>
          @endif

          <div class="flex-1">
            <flux:field>
              <flux:label>{{ $existing_photo_path ? 'Change Photo' : 'Upload Photo' }}</flux:label>
              <flux:input type="file" wire:model="photo" accept="image/*" />
              <flux:description>Maximum file size: 2MB. Accepted formats: JPG, PNG</flux:description>
              <flux:error name="photo" />
            </flux:field>

            @if ($photo)
              <div class="mt-4">
                <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-2">Preview:</flux:text>
                <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="h-32 w-32 rounded-lg object-cover border-2 border-zinc-200 dark:border-zinc-700">
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-end gap-4">
        <flux:button type="button" wire:navigate href="{{ route('patient.dashboard') }}" variant="ghost">
          Cancel
        </flux:button>

        <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
          <span wire:loading.remove>{{ $patient ? __('Update Profile') : __('Create Profile') }}</span>
          <span wire:loading>Saving...</span>
        </flux:button>
      </div>
    </form>
  </div>
</div>
