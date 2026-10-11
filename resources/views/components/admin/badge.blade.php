{{--
    Small status / category pill used throughout the admin tables.
    One palette definition, so a new status colour is added in one place.
--}}
@props(['tone' => 'slate'])

@php
    $tones = [
        'blue'   => 'bg-primary-50 text-primary-700',
        'green'  => 'bg-emerald-50 text-emerald-700',
        'amber'  => 'bg-amber-50 text-amber-700',
        'red'    => 'bg-rose-50 text-rose-700',
        'purple' => 'bg-violet-50 text-violet-700',
        'pink'   => 'bg-pink-50 text-pink-700',
        'slate'  => 'bg-slate-100 text-slate-700',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-block whitespace-nowrap rounded px-2.5 py-1 text-xs font-medium ' . ($tones[$tone] ?? $tones['slate'])]) }}>
    {{ $slot }}
</span>
