@props([
    'variant' => 'primary',
    'href' => null,
    'arrow' => true,
])

@php
    $variants = [
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700',
        'outline' => 'border border-primary-600 bg-white text-primary-600 hover:bg-primary-50',
        'text' => 'text-primary-600 hover:text-primary-700',
    ];

    $base = $variant === 'text'
        ? 'inline-flex items-center gap-1.5 text-sm font-semibold'
        : 'inline-flex items-center gap-2 rounded-md px-5 py-3 text-sm font-semibold transition-colors';

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
    @if($arrow)
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
    @endif
</{{ $tag }}>
