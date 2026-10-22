@extends('layouts.app')

@section('title', 'Page introuvable - ' . config('app.name'))
@section('meta_description', "La page que vous recherchez n'existe pas ou a ete deplacee.")

@section('content')

    <section class="relative overflow-hidden">

        {{--
            Decorative illustration backdrop. The source is a very wide
            (2170x725) illustration whose important elements — the scales
            and the paper plane — sit near its left/right edges. On phones
            (section taller than wide) a plain object-cover <img> crops
            the sides, which is fine since the art is purely decorative
            there. From sm: up the section is wide enough that we instead
            scale the image to full width and let any vertical overflow
            sit on a matching light background, so nothing gets cropped
            off the sides and no gap shows if the image is shorter than
            the section.
        --}}
        <div class="pointer-events-none absolute inset-0 hidden bg-white sm:block" aria-hidden="true"></div>
        <div
            class="pointer-events-none absolute inset-x-0 top-1/2 hidden aspect-[2170/725] w-full -translate-y-1/2 bg-cover bg-center sm:block"
            style="background-image: url('{{ asset('images/placeholders/404-illustration.jpg') }}')"
            aria-hidden="true"
        ></div>
        <x-picture
            path="placeholders/404-illustration"
            alt=""
            width="2170" height="725"
            class="pointer-events-none absolute inset-0 h-full w-full object-cover object-center sm:hidden"
        />
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-16 bg-gradient-to-b from-transparent to-white" aria-hidden="true"></div>

        <div class="relative mx-auto flex max-w-3xl flex-col items-center px-4 py-20 text-center sm:px-6 lg:py-24">

            {{-- 404 lockup: the middle zero is a document glyph --}}
            <p class="flex items-center justify-center gap-0 text-[5.5rem] font-extrabold leading-none text-primary-600 sm:text-[8rem]">
                <span aria-hidden="true">4</span>

                <span class="relative inline-flex h-[0.9em] w-[0.9em] shrink-0 items-center justify-center rounded-full bg-primary-600">
                    <x-icon name="document" class="h-[0.45em] w-[0.45em] text-white" />
                    <span class="absolute -bottom-1 -right-1 flex h-[0.3em] w-[0.3em] items-center justify-center rounded-full bg-amber-400 text-[0.18em] font-bold text-white" aria-hidden="true">!</span>
                </span>

                <span aria-hidden="true">4</span>
                <span class="sr-only">Erreur 404</span>
            </p>

            <h1 class="mt-6 text-3xl font-bold text-navy-900 sm:text-4xl">Page introuvable</h1>

            <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">
                Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
                Vérifiez l'adresse ou retournez à l'accueil pour continuer votre navigation.
            </p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <x-button :href="route('home')" variant="primary" :arrow="false" class="justify-center !px-7 !py-3.5">
                    <x-icon name="home" class="h-5 w-5" />
                    Retour à l'accueil
                </x-button>

                <x-button :href="route('search')" variant="outline" :arrow="false" class="justify-center !px-7 !py-3.5">
                    <x-icon name="search" class="h-5 w-5" />
                    Rechercher sur le site
                </x-button>
            </div>
        </div>
    </section>

    {{-- ============ USEFUL PAGES ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        <h2 class="flex items-center justify-center gap-3 text-xl font-bold text-navy-900">
            <span class="h-0.5 w-8 bg-amber-400" aria-hidden="true"></span>
            Pages utiles
            <span class="h-0.5 w-8 bg-amber-400" aria-hidden="true"></span>
        </h2>

        @php
            $shortcuts = [
                ['label' => 'La Clinique',  'icon' => 'building',    'route' => 'about'],
                ['label' => 'Nos services', 'icon' => 'document',    'route' => 'services'],
                ['label' => 'Actualités',   'icon' => 'book',        'route' => 'news.index'],
                ['label' => 'Ressources',   'icon' => 'folder',      'route' => 'resources'],
                ['label' => 'Web TV',       'icon' => 'play-circle', 'route' => 'webtv.index'],
                ['label' => 'Contact',      'icon' => 'phone',       'route' => 'contact'],
            ];
        @endphp

        <ul class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            @foreach ($shortcuts as $shortcut)
                <li>
                    <a
                        href="{{ route($shortcut['route']) }}"
                        class="group flex items-center gap-3 rounded-lg bg-primary-50/60 p-4 transition-colors hover:bg-primary-50"
                    >
                        @if($shortcut['icon'] === 'play-circle')
                            {{-- Filled play button: the shared play-circle path is outline-only and used by admin pages, so this one stays local --}}
                            <svg class="h-6 w-6 shrink-0 text-primary-600" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M10 8.5l6 3.5-6 3.5z" fill="white" />
                            </svg>
                        @else
                            <x-icon :name="$shortcut['icon']" class="h-6 w-6 shrink-0 text-primary-600" />
                        @endif

                        <span class="min-w-0 flex-1 truncate text-sm font-semibold text-slate-900 group-hover:text-primary-600">
                            {{ $shortcut['label'] }}
                        </span>

                        <x-icon name="arrow-right" class="h-4 w-4 shrink-0 text-slate-400 group-hover:text-primary-600" />
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

@endsection
