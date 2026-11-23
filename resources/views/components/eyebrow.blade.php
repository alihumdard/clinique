@props(['color' => 'primary'])

@php
    $colors = [
        // UAM red kicker above a dark heading: the charte's second colour.
        'primary' => 'text-accent-600',
        'muted'   => 'text-slate-500',
        'amber'   => 'text-amber-500',
        'white'   => 'text-white/70',
    ];
@endphp

<p {{ $attributes->merge(['class' => 'text-xs font-semibold uppercase tracking-wider ' . ($colors[$color] ?? $colors['primary'])]) }}>
    {{ $slot }}
</p>
