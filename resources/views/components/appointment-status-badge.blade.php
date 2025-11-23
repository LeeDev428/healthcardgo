@props(['status' => 'pending'])
@php
    $variant = match($status) {
        'pending' => 'outline',
        'confirmed' => 'primary',
        'checked_in' => 'primary',
        'in_progress' => 'primary',
        'completed' => 'success',
        'cancelled' => 'danger',
        'no_show' => 'danger',
        default => 'outline',
    };
@endphp
<flux:badge variant="{{ $variant }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</flux:badge>
