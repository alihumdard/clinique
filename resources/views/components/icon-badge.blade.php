@props([
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'h-9 w-9',
        'md' => 'h-12 w-12',
        'lg' => 'h-14 w-14',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-full bg-primary-50 text-primary-600 ' . ($sizes[$size] ?? $sizes['md'])]) }}>
    {{ $slot }}
</span>
