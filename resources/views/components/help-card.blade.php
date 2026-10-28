{{--
    Tinted sidebar prompt: icon, heading, short text, full-width button.
    Repeats on every resource page, so the markup lives here once.
--}}
@props([
    'icon' => 'headset',
    'title',
    'buttonLabel' => 'Nous contacter',
    'buttonHref' => null,
    // Usually a sidebar panel sitting after the sections of the page, so
    // h3 is right. Where it comes first in the document — the left column
    // of the privacy page — the caller raises it, or the page reads as
    // h1 then h3 and a level is skipped.
    'level' => 3,
])

<div {{ $attributes->merge(['class' => 'rounded-xl bg-primary-50/60 p-5']) }}>
    <x-icon-badge size="sm"><x-icon :name="$icon" class="h-5 w-5" /></x-icon-badge>

    <h{{ $level }} class="mt-3 text-base font-bold text-slate-900">{{ $title }}</h{{ $level }}>
    <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $slot }}</p>

    <x-button :href="$buttonHref ?? route('contact')" variant="primary" class="mt-4 w-full justify-center">
        {{ $buttonLabel }}
    </x-button>
</div>
