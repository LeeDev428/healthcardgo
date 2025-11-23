<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Appointments</flux:heading>
            <flux:text>View and manage your appointments</flux:text>
        </div>
    </div>

    <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-3">
        <flux:input wire:model.live="search" placeholder="Search by patient, number, or service..." icon="magnifying-glass" />
        <div class="md:col-span-1">
            <flux:select wire:model.live="status">
                <option value="all">All Status</option>
                @foreach (['pending','confirmed','checked_in','in_progress','completed','cancelled'] as $st)
                    <option value="{{ $st }}">{{ ucwords(str_replace('_',' ', $st)) }}</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Scheduled</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-800">
                    @forelse($appointments as $apt)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900 dark:text-white">{{ $apt->appointment_number }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900 dark:text-white">{{ $apt->patient?->user?->name ?? 'N/A' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900 dark:text-white">{{ $apt->service?->name ?? 'N/A' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900 dark:text-white">{{ optional($apt->scheduled_at)->format('M d, Y h:i A') }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex rounded-full bg-zinc-100 px-2 text-xs font-semibold leading-5 text-zinc-800 dark:bg-zinc-900 dark:text-zinc-200">{{ ucwords(str_replace('_',' ', $apt->status)) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" icon="clipboard-document-list" :href="route('doctor.medical-records.create', $apt->id)" wire:navigate>Write Record</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">No appointments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700 dark:bg-zinc-800">
            {{ $appointments->links() }}
        </div>
    </div>
</div>
