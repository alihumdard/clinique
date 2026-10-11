@props(['color' => 'primary'])

@php
    $colors = [
        'primary' => 'text-primary-700',
        'muted' => 'text-slate-500',
    ];
@endphp

<p {{ $attributes->merge(['class' => 'text-xs font-semibold uppercase tracking-wider ' . ($colors[$color] ?? $colors['primary'])]) }}>
    {{ $slot }}
</p>
