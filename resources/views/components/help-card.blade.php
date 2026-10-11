{{--
    Tinted sidebar prompt: icon, heading, short text, full-width button.
    Repeats on every resource page, so the markup lives here once.
--}}
@props([
    'icon' => 'ear',
    'title',
    'buttonLabel' => 'Nous contacter',
    'buttonHref' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-xl bg-primary-50/60 p-5']) }}>
    <x-icon-badge size="sm"><x-icon :name="$icon" class="h-5 w-5" /></x-icon-badge>

    <h3 class="mt-3 text-base font-bold text-slate-900">{{ $title }}</h3>
    <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $slot }}</p>

    <x-button :href="$buttonHref ?? route('contact')" variant="primary" class="mt-4 w-full justify-center">
        {{ $buttonLabel }}
    </x-button>
</div>
