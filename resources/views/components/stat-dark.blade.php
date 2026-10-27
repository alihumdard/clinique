@props(['icon', 'value', 'label'])

@php
    // Splits "500+" into a numeric target (500) and a suffix ("+"), so the
    // count-up animation (same script as the Activités impact stats) has
    // something to animate toward while keeping the "+" in the markup.
    $numeric = (int) str_replace(' ', '', $value);
    $suffix = preg_replace('/[\d\s]+/', '', $value);
@endphp

<div class="rounded-lg border border-white/10 bg-white/5 p-6 text-center">
    <x-icon-badge class="mx-auto !bg-white/10 !text-white">
        <x-icon :name="$icon" class="h-6 w-6" />
    </x-icon-badge>
    <p
        class="mt-4 text-3xl font-bold text-white"
        data-counter
        data-counter-target="{{ $numeric }}"
        data-counter-suffix="{{ $suffix }}"
    >{{ $value }}</p>
    <p class="mt-1 text-sm text-slate-300">{{ $label }}</p>
</div>
