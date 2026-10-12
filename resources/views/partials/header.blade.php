@php
    $navItems = [
        ['label' => 'Accueil', 'route' => 'home'],
        ['label' => 'La Clinique', 'route' => 'about'],
        ['label' => 'Services', 'route' => 'services'],
        ['label' => 'Actualités', 'route' => 'news.index'],
        ['label' => 'Ressources', 'route' => 'resources.guides'],
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
            {{-- Visual placeholder only: site is French-only per project scope, no i18n wired up --}}
            <span class="hidden items-center gap-1 text-slate-300 lg:flex">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z M3.6 9h16.8M3.6 15h16.8M12 3a15 15 0 014 9 15 15 0 01-4 9 15 15 0 01-4-9 15 15 0 014-9z" /></svg>
                FR
            </span>
        </div>
    </div>
</div>

<header class="sticky top-0 z-40 border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5">
            <img src="{{ asset('images/brand/logo.png') }}" alt="Clinique Juridique FSJP - UAM" class="h-10 w-10 shrink-0 rounded-full object-cover" width="40" height="40">
            <span class="leading-tight">
                <span class="block text-sm font-bold text-slate-900">Clinique Juridique</span>
                <span class="block text-xs font-medium text-slate-500">FSJP - UAM</span>
            </span>
        </a>

        <nav class="hidden items-center gap-6 lg:flex" aria-label="Navigation principale">
            @foreach ($navItems as $item)
                @php
                    $isActive = Route::is($item['route'])
                        || Route::is($item['route'] . '.*')
                        || ($item['route'] === 'resources.guides' && Route::is('resources.*'));
                @endphp
                <a
                    href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                    class="pb-0.5 text-sm font-medium {{ $isActive ? 'border-b-2 border-primary-600 font-semibold text-primary-600' : 'text-slate-600 hover:text-primary-600' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-4 lg:flex">
            <a href="{{ route('search') }}" aria-label="Rechercher" class="text-slate-500 hover:text-primary-600">
                <x-icon name="search" class="h-5 w-5" />
            </a>
            <x-button :href="route('presaisine.index')" variant="primary" class="!rounded-full">Pré-saisine</x-button>
        </div>

        <button
            type="button"
            id="mobile-menu-button"
            class="inline-flex items-center justify-center rounded-md p-2 text-slate-700 lg:hidden"
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
            @php $isActive = Route::is($item['route']) || Route::is($item['route'] . '.*'); @endphp
            <a
                href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                class="rounded-md px-3 py-2 text-sm font-medium {{ $isActive ? 'font-semibold text-primary-600' : 'text-slate-600 hover:bg-slate-50' }}"
            >
                {{ $item['label'] }}
            </a>
        @endforeach
        <x-button :href="route('presaisine.index')" variant="primary" class="mt-1 w-full justify-center !rounded-full">Pré-saisine</x-button>
    </nav>
</header>
