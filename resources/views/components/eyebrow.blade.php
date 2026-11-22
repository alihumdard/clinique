@props(['color' => 'primary'])

@php
    $colors = [
        'primary' => 'text-primary-700',
        'muted'   => 'text-slate-500',
        'amber'   => 'text-amber-500',
        'white'   => 'text-white/70',
    ];
@endphp

<p {{ $attributes->merge(['class' => 'text-xs font-semibold uppercase tracking-wider ' . ($colors[$color] ?? $colors['primary'])]) }}>
    {{-- Short UAM-red rule: an inline-block so it follows the text's own
         alignment, left or centred. --}}
    @if (in_array($color, ['primary', 'muted'], true))<span class="mr-2 inline-block h-0.5 w-5 bg-accent-500 align-middle" aria-hidden="true"></span>@endif
    {{ $slot }}
</p>
