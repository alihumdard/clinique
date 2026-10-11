{{--
    Checklist row. `variant` picks the mark style:
    plain  - bare emerald tick (default, used across the services pages)
    filled - tick in a coloured disc, used in the Web TV descriptions
--}}
@props([
    'variant' => 'plain',
    'color' => 'primary',
])

@php
    $discs = [
        'primary' => 'bg-primary-600 text-white',
        'green'   => 'bg-emerald-500 text-white',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start gap-2.5']) }}>
    @if($variant === 'filled')
        <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full {{ $discs[$color] ?? $discs['primary'] }}" aria-hidden="true">
            <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
        </span>
    @else
        <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
        </svg>
    @endif

    <span class="text-sm leading-snug text-slate-700">{{ $slot }}</span>
</div>
