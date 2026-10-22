@props([
    // The overlay variant shows only a label, so a value is not required.
    'value' => null,
    'label',
    'variant' => 'card',
])

@php
    // The counter script animates a number and re-appends the suffix, so the
    // two are split here ("500+" -> 500 and "+"). A value with no digits at
    // all is printed as written rather than counted.
    $numeric = (int) preg_replace('/\D/', '', (string) $value);
    $suffix = preg_replace('/^[\d\s\x{00A0}]+/u', '', (string) $value);
    $countable = $numeric > 0;
@endphp

@if($variant === 'overlay')
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3 text-white']) }}>
        {{ $slot }}
        <span class="text-sm font-medium leading-tight">{{ $label }}</span>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-4']) }}>
        {{ $slot }}
        <div>
            @if($countable)
                {{-- Starts at the real value so it reads correctly without JS --}}
                <p
                    class="text-xl font-bold text-slate-900"
                    data-counter
                    data-counter-target="{{ $numeric }}"
                    data-counter-suffix="{{ $suffix }}"
                >{{ $value }}</p>
            @else
                <p class="text-xl font-bold text-slate-900">{{ $value }}</p>
            @endif
            <p class="text-xs text-slate-500">{{ $label }}</p>
        </div>
    </div>
@endif
