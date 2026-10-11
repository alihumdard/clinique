{{--
    Click-to-load video embed.

    Performance plan: "Lecteur ne se chargeant qu'au clic, une vignette
    d'apercu legere etant affichee tant que l'utilisateur n'a pas clique" —
    an idle YouTube iframe costs roughly 1 Mo, so nothing third-party is
    requested until the visitor actually presses play.

    Progressive enhancement: without JavaScript the overlay is a plain link
    to the video on its host channel, so the page stays usable.
--}}
@props([
    'poster',
    'title',
    'videoId' => null,
    'duration' => null,
    'provider' => 'youtube',
])

@php
    // youtube-nocookie avoids setting tracking cookies before consent.
    $embed = $provider === 'vimeo'
        ? 'https://player.vimeo.com/video/' . $videoId . '?autoplay=1'
        : 'https://www.youtube-nocookie.com/embed/' . $videoId . '?autoplay=1&rel=0';

    $watch = $provider === 'vimeo'
        ? 'https://vimeo.com/' . $videoId
        : 'https://www.youtube.com/watch?v=' . $videoId;
@endphp

<div
    class="relative aspect-video w-full overflow-hidden rounded-xl bg-slate-900"
    data-video-embed="{{ $embed }}"
    data-video-title="{{ $title }}"
>
    <x-picture
        :path="$poster"
        :alt="$title"
        width="1280" height="720"
        class="h-full w-full object-cover"
    />

    <a
        href="{{ $watch }}"
        target="_blank"
        rel="noopener noreferrer"
        data-video-play
        class="group absolute inset-0 flex items-center justify-center bg-slate-900/20 transition-colors hover:bg-slate-900/30 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
    >
        <span class="sr-only">Lire la vidéo : {{ $title }}</span>

        <span class="flex h-16 w-16 items-center justify-center rounded-full bg-white/25 ring-2 ring-white/80 backdrop-blur-sm transition-transform group-hover:scale-110" aria-hidden="true">
            <svg class="ml-1 h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z" />
            </svg>
        </span>
    </a>

    @if($duration)
        <span class="pointer-events-none absolute bottom-3 right-3 rounded bg-slate-900/80 px-2 py-0.5 text-xs font-medium text-white">
            {{ $duration }}
        </span>
    @endif
</div>
