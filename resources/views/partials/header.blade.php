@php
    $navItems = [
        ['label' => 'Accueil', 'route' => 'home'],
        ['label' => 'La Clinique', 'route' => 'about', 'children' => [
            ['label' => 'À propos de la Clinique', 'route' => 'about'],
            ['label' => 'Notre équipe', 'route' => 'about.team'],
            ['label' => 'Nos partenaires', 'route' => 'partners'],
        ]],
        ['label' => 'Services', 'route' => 'services', 'children' => [
            ['label' => 'Nos services', 'route' => 'services'],
            ['label' => 'Processus de consultation', 'route' => 'services.process'],
            ['label' => "Conditions d'accès et gratuité", 'route' => 'services.eligibility'],
            ['label' => 'Tarifs administratifs', 'route' => 'services.fees'],
        ]],
        ['label' => 'Actualités', 'route' => 'news.index'],
        ['label' => 'Ressources', 'route' => 'resources.guides', 'children' => [
            ['label' => 'Fiches pratiques juridiques', 'route' => 'resources.guides'],
            ['label' => 'Rapports annuels', 'route' => 'resources.reports'],
            ['label' => 'Galerie photos', 'route' => 'gallery'],
        ]],
        ['label' => 'Web TV', 'route' => 'webtv.index'],
        ['label' => 'Contact', 'route' => 'contact'],
    ];
@endphp

{{-- Top utility bar: contact strip, shown site-wide --}}
<div class="hidden bg-navy-900 text-white sm:block">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 text-xs sm:px-6 lg:px-8">
        <div class="flex items-center gap-4">
            <span class="text-slate-300">Université Abdou Moumouni de Niamey</span>
            <span class="hidden text-slate-500 md:inline">|</span>
            <span class="hidden text-slate-300 md:inline">Clinique Juridique FSJP</span>
        </div>
        <div class="flex items-center gap-4">
            <a href="tel:{{ config('clinique.contact.phone.tel') }}" class="flex items-center gap-1.5 text-slate-300 hover:text-white">
                <x-icon name="phone" class="h-3.5 w-3.5" />
                {{ config('clinique.contact.phone.display') }}
            </a>
            <a href="mailto:{{ config('clinique.contact.email') }}" class="hidden items-center gap-1.5 text-slate-300 hover:text-white md:flex">
                <x-icon name="mail" class="h-3.5 w-3.5" />
                {{ config('clinique.contact.email') }}
            </a>
            <x-language-switcher />
        </div>
    </div>
</div>

<header class="sticky top-0 z-40 border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5">
            {{-- 96px, not the 256px file: that one is the favicon and the
                 Open Graph image, which want the resolution. This slot is
                 48px and the larger file was 57 Ko on every page. --}}
            <img
                src="{{ \App\Support\SiteSettings::headerLogoUrl() }}"
                alt="{{ config('clinique.name') }}"
                class="h-12 w-12 shrink-0 rounded-full object-cover"
                width="48" height="48"
            >
            <span class="leading-tight">
                <span class="block text-base font-bold text-slate-900">Clinique Juridique</span>
                <span class="block text-sm font-medium text-slate-500">FSJP - UAM</span>
            </span>
        </a>

        <nav class="hidden items-center gap-6 lg:flex" aria-label="Navigation principale">
            @foreach ($navItems as $item)
                @php
                    $isActive = Route::is($item['route'])
                        || Route::is($item['route'] . '.*')
                        || ($item['route'] === 'resources.guides' && (Route::is('resources.*') || Route::is('gallery')));
                @endphp
                @if(!empty($item['children']))
                    <div class="group relative">
                        <button
                            type="button"
                            class="flex items-center gap-1 pb-0.5 text-sm font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 {{ $isActive ? 'border-b-2 border-primary-600 font-semibold text-primary-600' : 'text-slate-600 hover:text-primary-600' }}"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            {{ $item['label'] }}
                            <svg class="h-3.5 w-3.5 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div class="invisible absolute left-0 top-full z-50 w-56 rounded-lg border border-slate-200 bg-white py-2 opacity-0 shadow-lg transition group-hover:visible group-hover:opacity-100">
                            @foreach ($item['children'] as $child)
                                <a
                                    href="{{ Route::has($child['route']) ? route($child['route']) : '#' }}"
                                    class="block px-4 py-2 text-sm {{ Route::is($child['route']) ? 'font-semibold text-primary-600' : 'text-slate-600 hover:bg-slate-50 hover:text-primary-600' }}"
                                >
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a
                        href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                        class="pb-0.5 text-sm font-medium {{ $isActive ? 'border-b-2 border-primary-600 font-semibold text-primary-600' : 'text-slate-600 hover:text-primary-600' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endif

                @if($item['route'] === 'contact')
                    <a href="{{ route('search') }}" aria-label="Rechercher" class="text-slate-500 hover:text-primary-600">
                        <x-icon name="search" class="h-5 w-5" />
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="hidden items-center lg:flex">
            <x-button :href="route('presaisine.index')" variant="primary" class="!rounded-full">Pré-saisine</x-button>
        </div>

        <button
            type="button"
            id="mobile-menu-button"
            class="inline-flex items-center justify-center rounded-md p-2 text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 lg:hidden"
            aria-expanded="false"
            aria-controls="mobile-menu"
            aria-label="Ouvrir le menu"
        >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <nav id="mobile-menu" class="hidden flex-col gap-1 border-t border-slate-200 px-4 py-3 lg:hidden" aria-label="Navigation mobile">
        @foreach ($navItems as $item)
            @php
                $isActive = Route::is($item['route'])
                    || Route::is($item['route'] . '.*')
                    || ($item['route'] === 'resources.guides' && (Route::is('resources.*') || Route::is('gallery')));
            @endphp
            @if(!empty($item['children']))
                <div data-mobile-dropdown>
                    <button
                        type="button"
                        data-mobile-dropdown-toggle
                        class="flex w-full items-center justify-between rounded-md px-3 py-2 text-sm font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 {{ $isActive ? 'font-semibold text-primary-600' : 'text-slate-600 hover:bg-slate-50' }}"
                        aria-expanded="false"
                    >
                        {{ $item['label'] }}
                        <svg class="h-4 w-4 shrink-0 transition-transform" data-mobile-dropdown-chevron fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div data-mobile-dropdown-panel class="mb-1 ml-3 hidden flex-col gap-0.5 rounded-lg border border-slate-200 bg-slate-50 p-1.5">
                        @foreach ($item['children'] as $child)
                            <a
                                href="{{ Route::has($child['route']) ? route($child['route']) : '#' }}"
                                class="rounded-md px-3 py-1.5 text-sm {{ Route::is($child['route']) ? 'bg-white font-semibold text-primary-600 shadow-sm' : 'text-slate-500 hover:bg-white hover:text-primary-600' }}"
                            >
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <a
                    href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                    class="rounded-md px-3 py-2 text-sm font-medium {{ $isActive ? 'font-semibold text-primary-600' : 'text-slate-600 hover:bg-slate-50' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endif
        @endforeach
        <a
            href="{{ route('search') }}"
            class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50"
        >
            <x-icon name="search" class="h-4 w-4" />
            Rechercher
        </a>
        <x-button :href="route('presaisine.index')" variant="primary" class="mt-1 w-full justify-center !rounded-full">Pré-saisine</x-button>
    </nav>
</header>
