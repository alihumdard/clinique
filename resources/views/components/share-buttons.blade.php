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
            'path' => 'M20.52 3.48A11.93 11.93 0 0012.04 0C5.47 0 .14 5.33.14 11.9c0 2.1.55 4.14 1.6 5.94L0 24l6.3-1.65a11.86 11.86 0 005.74 1.46h.01c6.57 0 11.9-5.33 11.9-11.9 0-3.18-1.24-6.17-3.43-8.43zm-8.47 18.3h-.01a9.9 9.9 0 01-5.04-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.87 9.87 0 01-1.51-5.27c0-5.46 4.44-9.9 9.91-9.9a9.85 9.85 0 017 2.9 9.85 9.85 0 012.9 7.01c0 5.46-4.45 9.9-9.91 9.9zm5.43-7.42c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.48-1.76-1.66-2.06-.17-.3-.02-.46.13-.61.14-.14.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.61-.91-2.21-.24-.58-.49-.5-.67-.51-.17-.01-.37-.01-.57-.01-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.87 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.42.25-.7.25-1.29.17-1.42-.07-.12-.27-.2-.57-.35z',
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
