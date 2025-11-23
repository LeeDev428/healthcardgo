<div class="space-y-6">
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Reports</h1>
            @if($adminCategory)
                <p class="text-sm text-gray-500">Scoped to: {{ $adminCategory }}</p>
            @endif
        </div>

        <a
            class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
            href="{{ route('healthcare_admin.reports.print', [
                'type' => $this->type,
                'from' => $this->from,
                'to' => $this->to,
                'status' => $this->status,
                'doctor_id' => $this->doctor_id,
                'service_category' => $this->service_category,
                'disease_type' => $this->disease_type,
                'barangay_id' => $this->barangay_id,
            ]) }}" target="_blank"
        >
            <flux:icon name="printer" class="size-5" />
            <span>Print PDF</span>
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="flex flex-col gap-1">
            <flux:label class="text-sm text-gray-600">Report Type</flux:label>
            <flux:select wire:model.live="type" class="rounded border border-gray-300 px-3 py-2">
                <flux:select.option value="appointments">Appointments</flux:select.option>
                <flux:select.option value="diseases">Diseases</flux:select.option>
                <flux:select.option value="feedback">Feedback</flux:select.option>
            </flux:select>
        </div>

        <div class="flex flex-col gap-1">
            <flux:label class="text-sm text-gray-600">From</flux:label>
            <flux:input type="date" wire:model.live="from" />
        </div>

        <div class="flex flex-col gap-1">
            <flux:label class="text-sm text-gray-600">To</flux:label>
            <flux:input type="date" wire:model.live="to" />
        </div>

        <div class="flex items-end gap-2">
            <flux:button icon="arrow-path" variant="primary" color="blue" wire:click="resetFilters">
                Reset
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="flex flex-col gap-1" x-show="$wire.type === 'appointments'">
            <flux:label class="text-sm text-gray-600">Status</flux:label>
            <flux:select wire:model.live="status">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="checked_in">Checked In</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </flux:select>
        </div>
        <div class="flex flex-col gap-1" x-show="$wire.type === 'appointments'">
            <flux:label class="text-sm text-gray-600">Barangay</flux:label>
            <flux:select wire:model.live="barangay_id">
                <option value="">All Barangays</option>
                @foreach($barangays as $barangay)
                    <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="flex flex-col gap-1" x-show="$wire.type === 'appointments'">
            <flux:label class="text-sm text-gray-600">Service Category</flux:label>
            <flux:select wire:model.live="service_category">
                <option value="">All</option>
                @foreach($serviceCategories as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="flex flex-col gap-1" x-show="$wire.type === 'diseases'">
            <flux:label class="text-sm text-gray-600">Disease Type</flux:label>
            <flux:input type="text" placeholder="e.g. dengue, hiv" wire:model.live="disease_type" />
        </div>
        <div class="flex flex-col gap-1" x-show="$wire.type === 'diseases'">
            <flux:label class="text-sm text-gray-600">Barangay</flux:label>
            <flux:select wire:model.live="barangay_id">
                <option value="">All Barangays</option>
                @foreach($barangays as $barangay)
                    <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-lg border p-4">
            <div class="text-sm text-gray-500">Total</div>
            <div class="mt-1 text-2xl font-semibold">{{ $dataset['meta']['total'] ?? 0 }}</div>
        </div>
        <div class="rounded-lg border p-4">
            <div class="text-sm text-gray-500">Range</div>
            <div class="mt-1 text-lg">{{ $dataset['meta']['from'] ?? '' }} → {{ $dataset['meta']['to'] ?? '' }}</div>
        </div>
        <div class="rounded-lg border p-4">
            <div class="text-sm text-gray-500">Report</div>
            <div class="mt-1 text-lg capitalize">{{ $this->type }}</div>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @if($this->type === 'appointments')
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Number</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Scheduled</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Status</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Patient</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Doctor</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Service</th>
                    @elseif($this->type === 'diseases')
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Type</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Patient</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Barangay</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Diagnosis Date</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Status</th>
                    @else
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Patient</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Appointment</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Overall</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Comments</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Submitted</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse(($dataset['list'] ?? []) as $row)
                    <tr>
                        @if($this->type === 'appointments')
                            <td class="px-4 py-2">{{ $row['number'] }}</td>
                            <td class="px-4 py-2">{{ $row['scheduled_at'] }}</td>
                            <td class="px-4 py-2">{{ ucfirst(str_replace('_',' ', $row['status'])) }}</td>
                            <td class="px-4 py-2">{{ $row['patient'] }}</td>
                            <td class="px-4 py-2">{{ $row['doctor'] }}</td>
                            <td class="px-4 py-2">{{ $row['service'] }}</td>
                        @elseif($this->type === 'diseases')
                            <td class="px-4 py-2">{{ $row['disease_type'] }}</td>
                            <td class="px-4 py-2">{{ $row['patient'] }}</td>
                            <td class="px-4 py-2">{{ $row['barangay'] }}</td>
                            <td class="px-4 py-2">{{ $row['diagnosis_date'] }}</td>
                            <td class="px-4 py-2">{{ ucfirst($row['status']) }}</td>
                        @else
                            <td class="px-4 py-2">{{ $row['patient'] }}</td>
                            <td class="px-4 py-2">#{{ $row['appointment'] }}</td>
                            <td class="px-4 py-2">{{ $row['overall_rating'] }}</td>
                            <td class="px-4 py-2">{{ $row['comments'] }}</td>
                            <td class="px-4 py-2">{{ $row['created_at'] }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-sm text-gray-500" colspan="6">No records found for the selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
