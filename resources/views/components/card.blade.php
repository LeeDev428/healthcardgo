@props(['title', 'value', 'icon' => null, 'color' => 'blue', 'href' => null])

@php
    $colorClasses = [
        'blue' => 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20',
        'green' => 'text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20',
        'yellow' => 'text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20',
        'red' => 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20',
        'purple' => 'text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20',
        'indigo' => 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20',
    ];
    $classes = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900']) }}>
    @if($href)
        <a href="{{ $href }}" wire:navigate class="block">
    @endif

    <div class="flex items-start justify-between">
        <div class="flex-1">
            <flux:subheading class="mb-2">{{ $title }}</flux:subheading>
            <flux:heading size="xl" class="text-zinc-900 dark:text-white">{{ $value }}</flux:heading>
        </div>
        @if($icon)
            <div class="flex h-12 w-12 items-center justify-center rounded-lg {{ $classes }}">
                <flux:icon :name="$icon" class="h-6 w-6" />
            </div>
        @endif
    </div>

    @if($href)
        </a>
    @endif
</div>
