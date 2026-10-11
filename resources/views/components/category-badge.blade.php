{{--
    Coloured category pill overlaid on a card image. Single definition of the
    category palette, shared by news cards, activity cards and Web TV capsules
    so a new category colour is added in one file only.
--}}
@props([
    'color' => 'primary',
    'position' => 'overlay',
])

@php
    $colors = [
        'primary' => 'bg-primary-600',
        'amber'   => 'bg-amber-500',
        'green'   => 'bg-emerald-600',
        'purple'  => 'bg-violet-600',
        'red'     => 'bg-rose-600',
        'slate'   => 'bg-slate-700',
    ];

    $base = 'rounded-md px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-white';
    $place = $position === 'overlay' ? 'absolute left-3 top-3 ' : 'inline-block ';
@endphp

<span {{ $attributes->merge(['class' => $place . $base . ' ' . ($colors[$color] ?? $colors['primary'])]) }}>
    {{ $slot }}
</span>
