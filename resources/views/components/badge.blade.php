{{-- Usage: <x-badge type="success">Active</x-badge> --}}
@props(['type' => 'neutral'])

@php
    // Each type has its own colours. Unknown types fall back to "neutral".
    $styles = [
        'success' => 'bg-emerald-100 text-emerald-800', // good: active, available
        'danger' => 'bg-red-100 text-red-800',          // problem: overdue, none left
        'info' => 'bg-blue-100 text-blue-800',          // in progress: borrowed
        'neutral' => 'bg-slate-100 text-slate-600',     // nothing special: inactive, returned
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex rounded-full px-3 py-1 text-xs font-semibold '.($styles[$type] ?? $styles['neutral'])]) }}>
    {{ $slot }}
</span>