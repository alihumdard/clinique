{{--
    Tab group built on radio inputs + CSS sibling selectors: no JavaScript,
    so it works with scripts disabled and adds nothing to the JS bundle.

    Uses a fixed set of positional peer names (peer/t1..peer/t5) rather than
    interpolating the tab key, because Tailwind only generates classes it can
    see as literal strings at build time. Supports up to 5 tabs.

    $tabs: ['key' => 'Label', ...]; panels are named slots matching the keys.
--}}
@props([
    'tabs' => [],
    'name' => 'tabs',
])

@php
    $keys = array_keys($tabs);

    // Literal class strings so Tailwind's scanner picks them up.
    $peer = ['peer/t1', 'peer/t2', 'peer/t3', 'peer/t4', 'peer/t5'];

    $label = [
        'peer-checked/t1:border-primary-600 peer-checked/t1:font-semibold peer-checked/t1:text-primary-600',
        'peer-checked/t2:border-primary-600 peer-checked/t2:font-semibold peer-checked/t2:text-primary-600',
        'peer-checked/t3:border-primary-600 peer-checked/t3:font-semibold peer-checked/t3:text-primary-600',
        'peer-checked/t4:border-primary-600 peer-checked/t4:font-semibold peer-checked/t4:text-primary-600',
        'peer-checked/t5:border-primary-600 peer-checked/t5:font-semibold peer-checked/t5:text-primary-600',
    ];

    $panel = [
        'peer-checked/t1:block',
        'peer-checked/t2:block',
        'peer-checked/t3:block',
        'peer-checked/t4:block',
        'peer-checked/t5:block',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    {{-- Radios must precede every label and panel for the sibling selectors to reach them --}}
    @foreach ($keys as $i => $key)
        <input
            type="radio"
            name="{{ $name }}"
            id="{{ $name }}-{{ $key }}"
            class="{{ $peer[$i] ?? '' }} sr-only"
            @checked($i === 0)
        >
    @endforeach

    <div class="scrollbar-hide flex gap-6 overflow-x-auto border-b border-slate-200">
        @foreach ($keys as $i => $key)
            <label
                for="{{ $name }}-{{ $key }}"
                class="shrink-0 cursor-pointer border-b-2 border-transparent pb-3 text-sm font-medium text-slate-500 transition-colors hover:text-primary-600 {{ $label[$i] ?? '' }}"
            >
                {{ $tabs[$key] }}
            </label>
        @endforeach
    </div>

    @foreach ($keys as $i => $key)
        <div role="region" aria-label="{{ $tabs[$key] }}" class="hidden pt-5 {{ $panel[$i] ?? '' }}">
            {{ ${$key} ?? '' }}
        </div>
    @endforeach
</div>
