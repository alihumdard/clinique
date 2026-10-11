{{--
    On/off switch built from a real checkbox, styled with peer-checked.
    No JavaScript, and it keeps native keyboard and form behaviour — the
    value posts like any other checkbox.
--}}
@props([
    'name',
    'label',
    'checked' => false,
])

@php $id = 'toggle-' . $name; @endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <input
        type="checkbox"
        id="{{ $id }}"
        name="{{ $name }}"
        value="1"
        @checked($checked)
        class="peer sr-only"
    >

    <label
        for="{{ $id }}"
        class="relative h-6 w-11 shrink-0 cursor-pointer rounded-full bg-slate-300 transition-colors peer-checked:bg-primary-600 peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-primary-600 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-transform after:content-[''] peer-checked:after:translate-x-5"
    >
        <span class="sr-only">{{ $label }}</span>
    </label>

    <label for="{{ $id }}" class="cursor-pointer text-sm text-slate-700">{{ $label }}</label>
</div>
