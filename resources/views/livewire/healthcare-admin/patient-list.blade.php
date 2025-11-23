<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                Patient List
            </h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                View {{ $adminCategory ? $adminCategory->label() : 'all' }} patients
            </p>
        </div>
        <flux:button icon="user-plus" variant="primary" color="cyan" href="{{ route('healthcare_admin.patients.register') }}">
            Register Walk-in Patient
        </flux:button>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Patients</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">
                        {{ $statistics['total_patients'] }}
                    </p>
                </div>
                <div class="text-blue-500">
                    <flux:icon.users class="w-8 h-8" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Male</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                        {{ $statistics['male_patients'] }}
                    </p>
                </div>
                <div class="text-blue-500">
                    <flux:icon.user class="w-8 h-8" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Female</p>
                    <p class="text-2xl font-bold text-pink-600 dark:text-pink-400 mt-1">
                        {{ $statistics['female_patients'] }}
                    </p>
                </div>
                <div class="text-pink-500">
                    <flux:icon.user class="w-8 h-8" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">This Month</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                        {{ $statistics['patients_this_month'] }}
                    </p>
                </div>
                <div class="text-green-500">
                    <flux:icon.calendar class="w-8 h-8" />
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search patients..." />
            </div>

            <div>
                <flux:select wire:model.live="genderFilter">
                    <option value="">All Genders</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </flux:select>
            </div>

            <div>
                <flux:select wire:model.live="barangayFilter">
                    <option value="">All Barangays</option>
                    @foreach ($barangays as $barangay)
                        <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                    @endforeach
                </flux:select>
            </div>

            <div>
                <flux:button wire:click="$set('search', '')" variant="ghost" class="w-full">
                    Clear Filters
                </flux:button>
            </div>
        </div>
    </div>

    {{-- Patients Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Patient #
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Age/Gender
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Barangay
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Contact
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Blood Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Registered
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($patients as $patient)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $patient->patient_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if ($patient->photo_path)
                                        <img src="{{ Storage::url($patient->photo_path) }}" alt="{{ $patient->full_name }}"
                                             class="h-8 w-8 rounded-full object-cover mr-3">
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center mr-3">
                                            <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-300">
                                                {{ substr($patient->full_name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ $patient->full_name }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                {{ $patient->age ?? 'N/A' }} / {{ ucfirst($patient->gender ?? 'N/A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                {{ $patient->barangay->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                {{ $patient->contact_number ?? $patient->user?->contact_number ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                {{ $patient->blood_type ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                {{ $patient->created_at ? $patient->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <flux:button wire:click="viewDetails({{ $patient->id }})" size="sm" variant="ghost">
                                    View Details
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                No patients found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $patients->links() }}
        </div>
    </div>

    {{-- View Details Modal --}}
    @if ($showDetailsModal && $selectedPatient)
        <flux:modal wire:model="showDetailsModal" class="max-w-4xl">
            <div class="space-y-4">
                <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Patient Details</h2>
                </div>

                {{-- Patient Photo and Basic Info --}}
                <div class="flex items-start gap-6">
                    @if ($selectedPatient->photo_path)
                        <img src="{{ Storage::url($selectedPatient->photo_path) }}" alt="{{ $selectedPatient->full_name }}"
                             class="h-24 w-24 rounded-full object-cover">
                    @else
                        <div class="h-24 w-24 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                            <span class="text-3xl font-semibold text-zinc-600 dark:text-zinc-300">
                                {{ substr($selectedPatient->full_name, 0, 1) }}
                            </span>
                        </div>
                    @endif

                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $selectedPatient->full_name }}</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">{{ $selectedPatient->patient_number }}</p>
                    </div>
                </div>

                {{-- Personal Information --}}
                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-3">Personal Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Email</p>
                            <p class="text-base text-zinc-900 dark:text-white mt-1">{{ $selectedPatient->user?->email ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Contact Number</p>
                            <p class="text-base text-zinc-900 dark:text-white mt-1">{{ $selectedPatient->contact_number ?? $selectedPatient->user?->contact_number ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Date of Birth</p>
                            <p class="text-base text-zinc-900 dark:text-white mt-1">
                                {{ $selectedPatient->date_of_birth ? $selectedPatient->date_of_birth->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Age</p>
                            <p class="text-base text-zinc-900 dark:text-white mt-1">{{ $selectedPatient->age ?? 'N/A' }} years old</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Gender</p>
                            <p class="text-base text-zinc-900 dark:text-white mt-1">{{ ucfirst($selectedPatient->gender ?? 'N/A') }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Blood Type</p>
                            <p class="text-base text-zinc-900 dark:text-white mt-1">{{ $selectedPatient->blood_type ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Barangay</p>
                            <p class="text-base text-zinc-900 dark:text-white mt-1">{{ $selectedPatient->barangay->name ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">PhilHealth Number</p>
                            <p class="text-base text-zinc-900 dark:text-white mt-1">{{ $selectedPatient->philhealth_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Emergency Contact --}}
                @if ($selectedPatient->emergency_contact)
                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-3">Emergency Contact</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Name</p>
                                <p class="text-base text-zinc-900 dark:text-white mt-1">{{ $selectedPatient->emergency_contact['name'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Contact Number</p>
                                <p class="text-base text-zinc-900 dark:text-white mt-1">{{ $selectedPatient->emergency_contact['number'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Medical Information --}}
                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-3">Medical Information</h3>
                    <div class="space-y-3">
                        @if ($selectedPatient->allergies)
                            <div>
                                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Allergies</p>
                                <p class="text-base text-zinc-900 dark:text-white mt-1">
                                    {{ is_array($selectedPatient->allergies) ? implode(', ', $selectedPatient->allergies) : $selectedPatient->allergies }}
                                </p>
                            </div>
                        @endif

                        @if ($selectedPatient->current_medications)
                            <div>
                                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Current Medications</p>
                                <p class="text-base text-zinc-900 dark:text-white mt-1">
                                    {{ is_array($selectedPatient->current_medications) ? implode(', ', $selectedPatient->current_medications) : $selectedPatient->current_medications }}
                                </p>
                            </div>
                        @endif

                        @if ($selectedPatient->medical_history)
                            <div>
                                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Medical History</p>
                                <p class="text-base text-zinc-900 dark:text-white mt-1">
                                    {{ is_array($selectedPatient->medical_history) ? implode(', ', $selectedPatient->medical_history) : $selectedPatient->medical_history }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Appointments Summary --}}
                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-3">Appointments Summary</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-3">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Appointments</p>
                            <p class="text-xl font-bold text-zinc-900 dark:text-white mt-1">
                                {{ $selectedPatient->appointments->count() }}
                            </p>
                        </div>
                        <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-3">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Completed</p>
                            <p class="text-xl font-bold text-green-600 dark:text-green-400 mt-1">
                                {{ $selectedPatient->appointments->where('status', 'completed')->count() }}
                            </p>
                        </div>
                        <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-3">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Health Cards</p>
                            <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                                {{ $selectedPatient->healthCards->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="closeDetailsModal" variant="ghost">
                        Close
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
