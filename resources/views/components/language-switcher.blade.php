{{--
    FR / EN switch.

    The site is written in French; English is produced by Google Translate.
    Two things follow from that, and both are deliberate.

    The script is loaded only once the visitor has asked for English. The
    performance plan forbids remote scripts, and a French visitor — which
    is nearly all of them — still loads none: no request leaves for Google
    until the flag is clicked.

    Anything confidential is marked notranslate and stays in French. The
    pré-saisine and the contact form carry what someone typed about their
    own situation, and translating a page sends its text to Google. That
    is not a trade worth making for a legal clinic, so those fields are
    excluded from the translation rather than from the feature.
--}}
@props(['dark' => true])

@php
    // Google's own cookie. It is what the widget reads on load, so the
    // choice survives a page change without any state of our own.
    $current = str_contains((string) request()->cookie('googtrans'), '/en') ? 'en' : 'fr';

    $languages = [
        'fr' => ['label' => 'FR', 'title' => 'Français', 'flag' => '🇫🇷'],
        'en' => ['label' => 'EN', 'title' => 'English', 'flag' => '🇬🇧'],
    ];

    $base = $dark
        ? 'text-slate-300 hover:text-white'
        : 'text-slate-500 hover:text-slate-900';

    $active = $dark ? 'text-white' : 'text-slate-900';
@endphp

<div class="notranslate flex items-center gap-1" translate="no" data-language-switcher>
    @foreach ($languages as $code => $language)
        <button
            type="button"
            data-language="{{ $code }}"
            lang="{{ $code }}"
            title="{{ $language['title'] }}"
            @if($current === $code) aria-current="true" @endif
            class="flex items-center gap-1 rounded px-1.5 py-1 text-xs font-semibold transition {{ $current === $code ? $active : $base }}"
        >
            <span aria-hidden="true">{{ $language['flag'] }}</span>
            <span>{{ $language['label'] }}</span>
            <span class="sr-only">— afficher le site en {{ $language['title'] }}</span>
        </button>
    @endforeach
</div>

@if($current === 'en')
    {{-- Only reached once English has been chosen. --}}
    {{-- The callback is defined in the bundled script, not inline: the
         Content-Security-Policy allows scripts from this origin only, and
         an inline block would be refused. --}}
    <div id="google_translate_element" class="hidden" aria-hidden="true"></div>

    <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" defer></script>
@endif
