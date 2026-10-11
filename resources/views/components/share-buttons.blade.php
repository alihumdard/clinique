{{--
    Share links for actualités and fiches pratiques (Phase 2 requirement:
    WhatsApp, Facebook, LinkedIn).

    Plain anchors, no SDK: the performance plan forbids third-party scripts,
    and the official share widgets each pull >100 Ko plus tracking cookies.
    These links cost nothing and leak no visitor data until clicked.
--}}
@props([
    'url' => null,
    'title' => '',
])

@php
    $shareUrl = $url ?? url()->current();
    $encodedUrl = rawurlencode($shareUrl);
    $encodedTitle = rawurlencode($title);

    $targets = [
        [
            'label' => 'Partager sur WhatsApp',
            'short' => 'WhatsApp',
            'href' => "https://wa.me/?text={$encodedTitle}%20{$encodedUrl}",
            'class' => 'hover:bg-green-50 hover:text-green-700 hover:border-green-200',
            'path' => 'M20.52 3.48A11.93 11.93 0 0012.04 0C5.47 0 .14 5.33.14 11.9c0 2.1.55 4.14 1.6 5.94L0 24l6.3-1.65a11.86 11.86 0 005.74 1.46h.01c6.57 0 11.9-5.33 11.9-11.9 0-3.18-1.24-6.17-3.43-8.43zM12.05 21.8h-.01a9.9 9.9 0 01-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 01-1.51-5.27c0-5.45 4.44-9.89 9.9-9.89a9.83 9.83 0 016.99 2.9 9.82 9.82 0 012.9 6.99c0 5.46-4.44 9.9-9.9 9.9z',
        ],
        [
            'label' => 'Partager sur Facebook',
            'short' => 'Facebook',
            'href' => "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}",
            'class' => 'hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200',
            'path' => 'M22 12a10 10 0 10-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.1 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0022 12z',
        ],
        [
            'label' => 'Partager sur LinkedIn',
            'short' => 'LinkedIn',
            'href' => "https://www.linkedin.com/sharing/share-offsite/?url={$encodedUrl}",
            'class' => 'hover:bg-sky-50 hover:text-sky-700 hover:border-sky-200',
            'path' => 'M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.03-1.85-3.03-1.85 0-2.14 1.45-2.14 2.94v5.66H9.34V9h3.41v1.56h.05c.47-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 110-4.13 2.06 2.06 0 010 4.13zM7.12 20.45H3.56V9h3.56v11.45z',
        ],
    ];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2']) }}>
    <span class="text-sm font-medium text-slate-500">Partager :</span>

    @foreach ($targets as $target)
        <a
            href="{{ $target['href'] }}"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="{{ $target['label'] }}"
            class="inline-flex items-center gap-1.5 rounded-md border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition-colors {{ $target['class'] }}"
        >
            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="{{ $target['path'] }}" />
            </svg>
            {{ $target['short'] }}
        </a>
    @endforeach
</div>
