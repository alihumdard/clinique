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
        'fr' => ['label' => 'FR', 'title' => 'Français'],
        'en' => ['label' => 'EN', 'title' => 'English'],
    ];

    $base = $dark ? 'text-slate-300 hover:text-white' : 'text-slate-500 hover:text-slate-900';
    $active = $dark ? 'text-white' : 'text-slate-900';
@endphp

{{--
    A globe rather than two flag emoji.

    The flags were 🇫🇷 and 🇬🇧, which Windows does not draw: it has no flag
    glyphs, so both came out as the bare letters FR and GB beside the
    labels — "FR FR", "GB EN". A flag is the wrong sign for a language in
    any case (English is not Britain's alone, and French is the language
    of Niger's courts), and the icon here is drawn by us, so it looks the
    same on every machine.
--}}
<div class="flex items-center gap-1">
    <x-icon name="globe" class="{{ $dark ? 'text-slate-400' : 'text-slate-400' }} h-3.5 w-3.5 shrink-0" aria-hidden="true" />

    @foreach ($languages as $code => $language)
        @if($current === $code)
            <span
                lang="{{ $code }}"
                aria-current="true"
                title="{{ $language['title'] }}"
                class="flex items-center gap-1 rounded px-1.5 py-1 text-xs font-semibold {{ $active }}"
            >
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
                <span>{{ $language['label'] }}</span>
                <span class="sr-only">{{ __('Afficher le site en :langue', ['langue' => $language['title']]) }}</span>
            </a>
        @endif
    @endforeach
</div>
