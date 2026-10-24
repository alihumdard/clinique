{{--
    Labelled form control with required marker, optional hint and an error
    slot. Renders input / textarea / select from one definition so every
    form on the site (contact now, pre-saisine in Module 3) is consistent
    and correctly associated with its label.
--}}
@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'placeholder' => null,
    'hint' => null,
    'rows' => 5,
    'options' => [],
    'value' => null,
])

@php
    $id = 'field-' . $name;
    $error = $errors->first($name);

    /*
     * A control points at its hint and its error together, so a screen
     * reader reads both rather than only whichever was written last.
     */
    $describedBy = collect([
        $hint ? $id . '-hint' : null,
        $error ? $id . '-error' : null,
    ])->filter()->implode(' ') ?: null;

    // Whatever was typed survives a failed submission.
    $value = old($name, $value);

    $control = 'w-full rounded-md border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500';
@endphp

<div {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <label for="{{ $id }}" class="block text-sm font-medium text-slate-700">
        {{ $label }}
        @if($required)
            <span class="text-rose-600" aria-hidden="true">*</span>
            <span class="sr-only">(obligatoire)</span>
        @endif
    </label>

    <div class="mt-1.5">
        @if($type === 'textarea')
            <textarea
                id="{{ $id }}"
                name="{{ $name }}"
                rows="{{ $rows }}"
                @if($required) required @endif
                @if($placeholder) placeholder="{{ $placeholder }}" @endif
                @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
                @if($error) aria-invalid="true" @endif
                class="{{ $control }} resize-y {{ $error ? 'border-rose-400' : '' }}"
            >{{ $value }}</textarea>

        @elseif($type === 'select')
            <select
                id="{{ $id }}"
                name="{{ $name }}"
                @if($required) required @endif
                @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
                @if($error) aria-invalid="true" @endif
                class="{{ $control }} pr-9 {{ $error ? 'border-rose-400' : '' }}"
            >
                @if($placeholder)
                    <option value="">{{ $placeholder }}</option>
                @endif
                @foreach ($options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @selected($value === $optionValue)>{{ $optionLabel }}</option>
                @endforeach
            </select>

        @else
            <input
                type="{{ $type }}"
                id="{{ $id }}"
                name="{{ $name }}"
                value="{{ $value }}"
                @if($required) required @endif
                @if($placeholder) placeholder="{{ $placeholder }}" @endif
                @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
                @if($error) aria-invalid="true" @endif
                class="{{ $control }} {{ $error ? 'border-rose-400' : '' }}"
            >
        @endif
    </div>

    @if($hint)
        <p id="{{ $id }}-hint" class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif

    @if($error)
        <p id="{{ $id }}-error" role="alert" class="mt-1.5 text-sm text-rose-700">{{ $error }}</p>
    @endif

    {{ $slot }}
</div>
