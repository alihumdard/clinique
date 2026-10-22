@extends('layouts.app')

@section('title', 'Page introuvable - ' . config('app.name'))
@section('meta_description', "La page que vous recherchez n'existe pas ou a ete deplacee.")

@section('content')

    <section class="relative overflow-hidden bg-primary-50/40">

        {{--
            Decorative backdrop as inline SVG rather than an image: it costs
            no extra request, scales to any screen and stays crisp. Hidden
            from assistive tech since it carries no information.
        --}}
        <svg
            class="pointer-events-none absolute inset-0 h-full w-full text-primary-100"
            viewBox="0 0 1200 500"
            preserveAspectRatio="xMidYMid slice"
            fill="currentColor"
            aria-hidden="true"
            focusable="false"
        >
            <path d="M0 360c120-40 200 20 320-10s180-90 300-70 200 110 330 80 180-70 250-60v160H0z" opacity=".55" />
            <circle cx="150" cy="120" r="46" opacity=".5" />
            <circle cx="1040" cy="150" r="34" opacity=".5" />
            <circle cx="930" cy="90" r="18" opacity=".45" />
            <ellipse cx="300" cy="95" rx="60" ry="22" opacity=".45" />

            {{-- Scales of justice, bottom-left --}}
            <g transform="translate(70 300)" opacity=".6" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round">
                <line x1="40" y1="10" x2="40" y2="140" />
                <line x1="0" y1="140" x2="80" y2="140" />
                <line x1="0" y1="30" x2="80" y2="30" />
                <line x1="0" y1="30" x2="-24" y2="75" />
                <line x1="0" y1="30" x2="24" y2="75" />
                <path d="M-24 75a24 20 0 0048 0z" />
                <line x1="80" y1="30" x2="56" y2="75" />
                <line x1="80" y1="30" x2="104" y2="75" />
                <path d="M56 75a24 20 0 0048 0z" />
                <circle cx="40" cy="10" r="8" fill="currentColor" stroke="none" />
            </g>
            <rect x="60" y="440" width="100" height="18" rx="3" opacity=".6" />
            <rect x="68" y="420" width="84" height="18" rx="3" opacity=".6" />

            {{-- Paper plane with dashed trail, top-right --}}
            <path d="M980 160c40-30 90-55 140-70" fill="none" stroke="currentColor" stroke-width="3" stroke-dasharray="2 10" stroke-linecap="round" opacity=".7" />
            <path d="M1120 80l-34 10 6 16 8-10 4 22z" opacity=".7" />
        </svg>

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
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-primary-600">
                            <x-icon :name="$shortcut['icon']" class="h-5 w-5" />
                        </span>

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
