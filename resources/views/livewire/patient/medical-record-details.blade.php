<div class="space-y-6">
    <div>
        <flux:heading size="xl">Medical Record</flux:heading>
        <flux:text class="mt-2">Details of your medical record.</flux:text>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <flux:heading size="sm" class="mb-2">Overview</flux:heading>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Service</span>
                    <span class="font-medium">{{ $medicalRecord->service->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Recorded</span>
                    <span class="font-medium">{{ optional($medicalRecord->recorded_at)->format('M d, Y g:i A') ?? $medicalRecord->created_at->format('M d, Y g:i A') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Doctor</span>
                    <span class="font-medium">{{ $medicalRecord->doctor->name ?? '—' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <flux:heading size="sm" class="mb-2">Summary</flux:heading>
            <div class="space-y-2 text-sm">
                <div>
                    <span class="block text-zinc-600 dark:text-zinc-400">Title</span>
                    <span class="font-medium">{{ $medicalRecord->title ?? '—' }}</span>
                </div>
                <div>
                    <span class="block text-zinc-600 dark:text-zinc-400">Description</span>
                    <span class="font-medium">{{ $medicalRecord->description ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
        <flux:heading size="sm" class="mb-3">Clinical Notes</flux:heading>
        <div class="grid md:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="block text-zinc-600 dark:text-zinc-400">Diagnosis</span>
                <span class="font-medium">{{ $medicalRecord->diagnosis ?? '—' }}</span>
            </div>
            <div>
                <span class="block text-zinc-600 dark:text-zinc-400">Treatment</span>
                <span class="font-medium">{{ $medicalRecord->treatment ?? '—' }}</span>
            </div>
            <div>
                <span class="block text-zinc-600 dark:text-zinc-400">Notes</span>
                <span class="font-medium">{{ $medicalRecord->notes ?? '—' }}</span>
            </div>
        </div>
    </div>

    <div>
        <flux:button :href="route('patient.dashboard')" variant="subtle" wire:navigate>
            <flux:icon name="arrow-left" />
            Back to Dashboard
        </flux:button>
    </div>
</div>
