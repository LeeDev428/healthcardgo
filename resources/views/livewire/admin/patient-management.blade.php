<div class="max-w-7xl space-y-6">
  <!-- Header -->
  <div class="flex items-center justify-between">
    <div>
      <flux:heading size="xl">Patient Management</flux:heading>
      <flux:text class="text-zinc-600 dark:text-zinc-400">Manage and monitor all registered patients</flux:text>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Total Patients</flux:text>
      <flux:text weight="bold" size="xl">{{ number_format($stats['total']) }}</flux:text>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Active</flux:text>
      <flux:text weight="bold" size="xl" class="text-green-600 dark:text-green-400">{{ number_format($stats['active']) }}</flux:text>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Inactive</flux:text>
      <flux:text weight="bold" size="xl" class="text-red-600 dark:text-red-400">{{ number_format($stats['inactive']) }}</flux:text>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">With Appointments</flux:text>
      <flux:text weight="bold" size="xl" class="text-blue-600 dark:text-blue-400">{{ number_format($stats['with_appointments']) }}</flux:text>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">With Health Cards</flux:text>
      <flux:text weight="bold" size="xl" class="text-purple-600 dark:text-purple-400">{{ number_format($stats['with_health_cards']) }}</flux:text>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">New This Month</flux:text>
      <flux:text weight="bold" size="xl" class="text-orange-600 dark:text-orange-400">{{ number_format($stats['new_this_month']) }}</flux:text>
    </div>
  </div>

  <!-- Filters -->
  <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <!-- Search -->
      <div>
        <flux:input
          wire:model.live.debounce.300ms="search"
          placeholder="Search by name, email, or contact..."
        >
          <x-slot name="iconTrailing">
            <flux:icon name="magnifying-glass" />
          </x-slot>
        </flux:input>
      </div>

      <!-- Status Filter -->
      <div>
        <flux:select wire:model.live="statusFilter">
          <option value="all">All Status</option>
          <option value="approved">Active</option>
          <option value="inactive">Inactive</option>
          <option value="pending">Pending Approval</option>
        </flux:select>
      </div>

      <!-- Barangay Filter -->
      <div>
        <flux:select wire:model.live="barangayFilter">
          <option value="all">All Barangays</option>
          @foreach($barangays as $barangay)
            <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
          @endforeach
        </flux:select>
      </div>
    </div>
  </div>

  <!-- Patients Table -->
  <div class="rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
          <tr>
            <th class="px-6 py-3 text-left">
              <flux:text size="sm" weight="semibold" class="text-zinc-600 dark:text-zinc-400">Patient</flux:text>
            </th>
            <th class="px-6 py-3 text-left">
              <flux:text size="sm" weight="semibold" class="text-zinc-600 dark:text-zinc-400">Contact</flux:text>
            </th>
            <th class="px-6 py-3 text-left">
              <flux:text size="sm" weight="semibold" class="text-zinc-600 dark:text-zinc-400">Barangay</flux:text>
            </th>
            <th class="px-6 py-3 text-left">
              <flux:text size="sm" weight="semibold" class="text-zinc-600 dark:text-zinc-400">Blood Type</flux:text>
            </th>
            <th class="px-6 py-3 text-left">
              <flux:text size="sm" weight="semibold" class="text-zinc-600 dark:text-zinc-400">Appointments</flux:text>
            </th>
            <th class="px-6 py-3 text-left">
              <flux:text size="sm" weight="semibold" class="text-zinc-600 dark:text-zinc-400">Status</flux:text>
            </th>
            <th class="px-6 py-3 text-left">
              <flux:text size="sm" weight="semibold" class="text-zinc-600 dark:text-zinc-400">Actions</flux:text>
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
          @forelse($patients as $patient)
            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
              <td class="px-6 py-4">
                <div>
                  <flux:text size="sm" weight="semibold">{{ $patient->full_name }}</flux:text>
                  <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">{{ $patient->user?->email ?? 'N/A' }}</flux:text>
                </div>
              </td>
              <td class="px-6 py-4">
                <flux:text size="sm">{{ $patient->contact_number }}</flux:text>
              </td>
              <td class="px-6 py-4">
                <flux:text size="sm">{{ $patient->barangay->name ?? 'N/A' }}</flux:text>
              </td>
              <td class="px-6 py-4">
                <flux:badge variant="ghost" size="sm">{{ $patient->blood_type ?? 'N/A' }}</flux:badge>
              </td>
              <td class="px-6 py-4">
                <flux:text size="sm">{{ $patient->appointments->count() }}</flux:text>
              </td>
              <td class="px-6 py-4">
                @if($patient->user->status === 'approved')
                  <flux:badge variant="success" size="sm">Active</flux:badge>
                @elseif($patient->user->status === 'inactive')
                  <flux:badge variant="danger" size="sm">Inactive</flux:badge>
                @elseif($patient->user->status === 'pending')
                  <flux:badge variant="warning" size="sm">Pending</flux:badge>
                @endif
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <flux:button wire:click="viewDetails({{ $patient->id }})" variant="ghost" size="sm">
                    <flux:icon name="eye" />
                  </flux:button>
                  {{-- <flux:button wire:click="editPatient({{ $patient->id }})" variant="ghost" size="sm">
                    <flux:icon name="pencil" />
                  </flux:button> --}}
                  @if($patient->user->status === 'approved')
                    <flux:button wire:click="deactivatePatient({{ $patient->id }})" variant="ghost" size="sm">
                      <flux:icon name="x-circle" />
                    </flux:button>
                  @elseif($patient->user->status === 'inactive')
                    <flux:button wire:click="activatePatient({{ $patient->id }})" variant="ghost" size="sm">
                      <flux:icon name="check-circle" />
                    </flux:button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-6 py-12 text-center">
                <flux:icon name="users" class="h-12 w-12 text-zinc-400 dark:text-zinc-600 mx-auto mb-2" />
                <flux:text class="text-zinc-600 dark:text-zinc-400">No patients found</flux:text>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    @if($patients->hasPages())
      <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
        {{ $patients->links() }}
      </div>
    @endif
  </div>

  <!-- Details Modal -->
  @if($showDetailsModal && $selectedPatient)
    <flux:modal wire:model="showDetailsModal" variant="flyout">
      <flux:heading size="lg" class="mb-4">Patient Details</flux:heading>

      <div class="space-y-6">
        <!-- Personal Info -->
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
          <flux:subheading class="mb-3">Personal Information</flux:subheading>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Full Name</flux:text>
              <flux:text weight="semibold">{{ $selectedPatient->user->name }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Email</flux:text>
              <flux:text weight="semibold">{{ $selectedPatient->user->email }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Contact Number</flux:text>
              <flux:text weight="semibold">{{ $selectedPatient->contact_number }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Gender</flux:text>
              <flux:text weight="semibold">{{ ucfirst($selectedPatient->gender) }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Date of Birth</flux:text>
              <flux:text weight="semibold">{{ $selectedPatient->date_of_birth ? \Carbon\Carbon::parse($selectedPatient->date_of_birth)->format('M d, Y') : 'N/A' }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Blood Type</flux:text>
              <flux:text weight="semibold">{{ $selectedPatient->blood_type ?? 'N/A' }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Barangay</flux:text>
              <flux:text weight="semibold">{{ $selectedPatient->barangay->name ?? 'N/A' }}</flux:text>
            </div>
            <div>
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Status</flux:text>
              @if($selectedPatient->user->status === 'approved')
                <flux:badge variant="success">Active</flux:badge>
              @elseif($selectedPatient->user->status === 'inactive')
                <flux:badge variant="danger">Inactive</flux:badge>
              @else
                <flux:badge variant="warning">{{ ucfirst($selectedPatient->user->status) }}</flux:badge>
              @endif
            </div>
          </div>
          @if($selectedPatient->address)
            <div class="mt-3">
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Address</flux:text>
              <flux:text weight="semibold">{{ $selectedPatient->address }}</flux:text>
            </div>
          @endif
        </div>

        <!-- Statistics -->
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
          <flux:subheading class="mb-3">Statistics</flux:subheading>
          <div class="grid grid-cols-3 gap-3">
            <div class="text-center p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20">
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Appointments</flux:text>
              <flux:text weight="bold" size="xl" class="text-blue-600 dark:text-blue-400">{{ $selectedPatient->appointments->count() }}</flux:text>
            </div>
            <div class="text-center p-3 rounded-lg bg-purple-50 dark:bg-purple-900/20">
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Health Cards</flux:text>
              <flux:text weight="bold" size="xl" class="text-purple-600 dark:text-purple-400">{{ $selectedPatient->healthCards->count() }}</flux:text>
            </div>
            <div class="text-center p-3 rounded-lg bg-green-50 dark:bg-green-900/20">
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Medical Records</flux:text>
              <flux:text weight="bold" size="xl" class="text-green-600 dark:text-green-400">{{ $selectedPatient->medicalRecords->count() }}</flux:text>
            </div>
          </div>
        </div>

        <!-- Recent Appointments -->
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
          <flux:subheading class="mb-3">Recent Appointments</flux:subheading>
          <div class="space-y-2">
            @forelse($selectedPatient->appointments->take(5) as $apt)
              <div class="flex items-center justify-between p-2 rounded hover:bg-zinc-50 dark:hover:bg-zinc-800">
                <div>
                  <flux:text size="sm" weight="semibold">{{ $apt->service->name }}</flux:text>
                  <flux:text size="xs" class="text-zinc-500 dark:text-zinc-500">{{ $apt->scheduled_at->format('M d, Y g:i A') }}</flux:text>
                </div>
                <flux:badge variant="{{ $apt->status === 'completed' ? 'success' : 'ghost' }}" size="sm">
                  {{ ucfirst($apt->status) }}
                </flux:badge>
              </div>
            @empty
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">No appointments yet</flux:text>
            @endforelse
          </div>
        </div>

        <!-- Account Info -->
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
          <flux:subheading class="mb-3">Account Information</flux:subheading>
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Registered</flux:text>
              <flux:text size="sm">{{ $selectedPatient->created_at->format('M d, Y') }}</flux:text>
            </div>
            <div class="flex items-center justify-between">
              <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Last Updated</flux:text>
              <flux:text size="sm">{{ $selectedPatient->updated_at->diffForHumans() }}</flux:text>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-6 flex justify-end gap-2">
        <flux:button icon="pencil-square" wire:click="editPatient({{ $selectedPatient->id }})" variant="primary" color="cyan">
            Edit Patient
        </flux:button>
        <flux:button wire:click="closeDetailsModal" variant="ghost">Close</flux:button>
      </div>
    </flux:modal>
  @endif

  <!-- Edit Modal -->
  @if($showEditModal)
    <flux:modal wire:model="showEditModal" variant="flyout">
      <flux:heading size="lg" class="mb-4">Edit Patient</flux:heading>

      <form wire:submit.prevent="updatePatient" class="space-y-4">
        <!-- Name -->
        <div>
          <flux:input wire:model="editForm.name" label="Full Name" required />
          @error('editForm.name') <flux:text size="sm" class="text-red-600">{{ $message }}</flux:text> @enderror
        </div>

        <!-- Email -->
        <div>
          <flux:input wire:model="editForm.email" type="email" label="Email" required />
          @error('editForm.email') <flux:text size="sm" class="text-red-600">{{ $message }}</flux:text> @enderror
        </div>

        <!-- Contact Number -->
        <div>
          <flux:input wire:model="editForm.contact_number" label="Contact Number" required />
          @error('editForm.contact_number') <flux:text size="sm" class="text-red-600">{{ $message }}</flux:text> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
          <!-- Gender -->
          <div>
            <flux:select wire:model="editForm.gender" label="Gender" required>
              <option value="">Select Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </flux:select>
            @error('editForm.gender') <flux:text size="sm" class="text-red-600">{{ $message }}</flux:text> @enderror
          </div>

          <!-- Blood Type -->
          <div>
            <flux:input wire:model="editForm.blood_type" label="Blood Type" placeholder="e.g., A+, O-, B+" />
            @error('editForm.blood_type') <flux:text size="sm" class="text-red-600">{{ $message }}</flux:text> @enderror
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <!-- Date of Birth -->
          <div>
            <flux:input wire:model="editForm.date_of_birth" type="date" label="Date of Birth" />
            @error('editForm.date_of_birth') <flux:text size="sm" class="text-red-600">{{ $message }}</flux:text> @enderror
          </div>

          <!-- Barangay -->
          <div>
            <flux:select wire:model="editForm.barangay_id" label="Barangay" required>
              <option value="">Select Barangay</option>
              @foreach($barangays as $barangay)
                <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
              @endforeach
            </flux:select>
            @error('editForm.barangay_id') <flux:text size="sm" class="text-red-600">{{ $message }}</flux:text> @enderror
          </div>
        </div>

        <!-- Address -->
        <div>
          <flux:textarea wire:model="editForm.address" label="Address" rows="3" />
          @error('editForm.address') <flux:text size="sm" class="text-red-600">{{ $message }}</flux:text> @enderror
        </div>

        <div class="flex justify-end gap-2 pt-4">
          <flux:button type="button" wire:click="closeEditModal" variant="ghost">Cancel</flux:button>
          <flux:button type="submit" variant="primary" color="cyan">Update Patient</flux:button>
        </div>
      </form>
    </flux:modal>
  @endif
</div>
