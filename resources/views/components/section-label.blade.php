@props(['color' => 'primary'])

@php
    $colors = [
        'primary' => 'bg-primary-600',
        'amber' => 'bg-amber-400',
    ];
@endphp

<h2 class="flex items-center gap-2.5 text-xl font-bold text-slate-900">
    <span class="h-0.5 w-6 shrink-0 {{ $colors[$color] ?? $colors['primary'] }}" aria-hidden="true"></span>
    {{ $slot }}
</h2>
