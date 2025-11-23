<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">My Patients</flux:heading>
            <flux:text>Patients you've seen or have scheduled</flux:text>
        </div>
    </div>

    <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-2">
        <flux:input wire:model.live="search" placeholder="Search by name or patient number..." icon="magnifying-glass" />
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Patient #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Age</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-800">
                    @forelse($patients as $patient)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900 dark:text-white">{{ $patient->user?->name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900 dark:text-white">{{ $patient->patient_number }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900 dark:text-white">{{ $patient->age ?? '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" icon="clipboard-document" :href="route('doctor.patients.records', $patient->id)" wire:navigate>View Records</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">No patients found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700 dark:bg-zinc-800">
            {{ $patients->links() }}
        </div>
    </div>
</div>
