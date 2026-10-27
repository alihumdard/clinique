{{--
    FR / EN switch.

    The site is written in French and translated by the site itself, not
    by a third party. Google's translate widget was the obvious shortcut
    and was rejected for two reasons: it rewrites the page with inline
    scripts, which means loosening the Content-Security-Policy the
    security checklist asks for, and translating a page sends its text to
    Google — on a legal clinic's site, some of that text is someone's
    account of their own situation.

    So the English comes from lang/en.json. A string with no entry there
    is served in French rather than as a missing key, which is what lets
    the pages be translated one at a time without any of them breaking.
--}}
@props(['dark' => true])

@php
    $current = app()->getLocale();

    $languages = [
        'fr' => ['label' => 'FR', 'title' => 'Français', 'flag' => '🇫🇷'],
        'en' => ['label' => 'EN', 'title' => 'English', 'flag' => '🇬🇧'],
    ];

    $base = $dark ? 'text-slate-300 hover:text-white' : 'text-slate-500 hover:text-slate-900';
    $active = $dark ? 'text-white' : 'text-slate-900';
@endphp

<div class="flex items-center gap-1">
    @foreach ($languages as $code => $language)
        @if($current === $code)
            <span
                lang="{{ $code }}"
                aria-current="true"
                title="{{ $language['title'] }}"
                class="flex items-center gap-1 rounded px-1.5 py-1 text-xs font-semibold {{ $active }}"
            >
                <span aria-hidden="true">{{ $language['flag'] }}</span>
                <span>{{ $language['label'] }}</span>
            </span>
        @else
            {{-- A link, not a form: changing language is a navigation, and
                 it has to work with scripts off like the rest of the site. --}}
            <a
                href="{{ route('locale', $code) }}"
                lang="{{ $code }}"
                title="{{ $language['title'] }}"
                data-language="{{ $code }}"
                class="flex items-center gap-1 rounded px-1.5 py-1 text-xs font-semibold transition {{ $base }}"
            >
                <span aria-hidden="true">{{ $language['flag'] }}</span>
                <span>{{ $language['label'] }}</span>
                <span class="sr-only">{{ __('Afficher le site en :langue', ['langue' => $language['title']]) }}</span>
            </a>
        @endif
    @endforeach
</div>
