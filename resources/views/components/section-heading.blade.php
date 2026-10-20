{{--
    Section heading with the amber/primary rule, plus an optional "see all"
    link aligned to the right. Wraps x-section-label so the heading style
    stays defined in exactly one place.

    Used by: Activités, Galerie, Fiches pratiques, Web TV, Homepage.
--}}
@props([
    'color' => 'amber',
    'link' => null,
    'linkLabel' => 'Voir tout',
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between sm:gap-3']) }}>
    <x-section-label :color="$color">{{ $slot }}</x-section-label>

    @if($link)
        <a href="{{ $link }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700">
            {{ $linkLabel }}
            <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    @endif
</div>
