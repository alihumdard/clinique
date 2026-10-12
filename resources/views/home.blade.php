
@extends('layouts.app')

@section('title', 'Accueil - ' . config('app.name'))

@section('meta_description', 'La Clinique Juridique de la FSJP-UAM offre des consultations juridiques gratuites, un accompagnement par des étudiants encadrés, et contribue à la promotion des droits humains.')

@section('content')

{{-- ============ HERO ============ --}}
<section class="relative overflow-hidden bg-primary-50">
    <div class="grid grid-cols-1 lg:grid-cols-2 lg:items-stretch">

        <div class="relative z-10 mx-auto flex w-full max-w-7xl flex-col justify-center px-4 py-14 sm:px-6 lg:px-8 lg:pr-0">
            <p class="text-xs font-bold uppercase tracking-widest text-primary-600">
                Clinique Juridique FSJP — Université Abdou Moumouni
            </p>

            <h1 class="mt-4 text-4xl font-extrabold leading-tight text-slate-900 sm:text-5xl">
                Un accès à la justice
                <span class="block text-primary-600">pour tous</span>
            </h1>

            <p class="mt-5 max-w-xl text-base leading-relaxed text-slate-600">
                La Clinique Juridique de la FSJP-UAM offre des consultations juridiques gratuites,
                un accompagnement par des étudiants encadrés,
                et contribue à la promotion des droits humains.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3">
                <x-button href="#presaisine" variant="primary">
                    <x-icon name="user-plus" class="h-4 w-4" />
                    Faire une pré-saisine
                </x-button>
                <x-button href="#services" variant="outline">Découvrir nos services</x-button>
            </div>
        </div>

        {{-- Photo (full-bleed to viewport edge, flush with section top) --}}
        <div class="relative hidden min-h-[420px] lg:block">
            <x-picture
                path="placeholders/hero-fsjp"
                alt=""
                width="842"
                height="752"
                eager
                class="absolute inset-0 h-full w-full object-cover object-center"
                aria-hidden="true"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-primary-50 via-primary-50/25 to-transparent"></div>
        </div>
    </div>

    {{-- Floating information card (overlaps photo bottom edge) --}}
    <div class="relative z-10 mx-auto -mt-10 mb-4 hidden max-w-7xl lg:block">
        <div class="ml-auto grid w-[640px] grid-cols-3 divide-x divide-slate-200 rounded-tl-xl bg-white shadow-xl">

            <div class="flex items-center gap-3 p-4">
                <x-icon-badge size="sm"><x-icon name="clock" class="h-5 w-5" /></x-icon-badge>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Horaires d'ouverture</p>
                    <p class="text-sm text-slate-500">Lun - Ven : 9h00 - 17h00</p>
                </div>
            </div>

            <div class="flex items-center gap-3 p-4">
                <x-icon-badge size="sm"><x-icon name="map-pin" class="h-5 w-5" /></x-icon-badge>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Adresse</p>
                    <p class="text-sm text-slate-500">Faculté des Sciences<br>Juridiques, Niamey</p>
                </div>
            </div>

            <div class="flex items-center gap-3 p-4">
                <x-icon-badge size="sm"><x-icon name="phone" class="h-5 w-5" /></x-icon-badge>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Contact</p>
                    <p class="text-sm text-slate-500">
                        <a href="tel:+227XXXXXXXX" class="hover:text-primary-600">+227 XX XX XX XX</a><br>
                        <a href="https://wa.me/227XXXXXXXX" target="_blank" rel="noopener" class="hover:text-primary-600">WhatsApp</a>
                    </p>
                </div>
            </div>

        </div>
    </div>

    <div class="mx-auto max-w-7xl lg:hidden">
        {{-- Mobile photo strip --}}
        <div class="relative h-44 sm:h-56">
            <x-picture
                path="placeholders/hero-fsjp"
                alt=""
                width="842"
                height="752"
                class="h-full w-full object-cover object-center"
                aria-hidden="true"
            />
        </div>

        {{-- Mobile information strip --}}
        <div class="grid grid-cols-1 divide-y divide-slate-200 border-t border-slate-200 sm:grid-cols-3 sm:divide-x sm:divide-y-0">

            <div class="flex items-center gap-3 px-4 py-3">
                <x-icon-badge size="sm"><x-icon name="clock" class="h-5 w-5" /></x-icon-badge>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Horaires d'ouverture</p>
                    <p class="text-sm text-slate-500">Lun - Ven : 9h00 - 17h00</p>
                </div>
            </div>

            <div class="flex items-center gap-3 px-4 py-3">
                <x-icon-badge size="sm"><x-icon name="map-pin" class="h-5 w-5" /></x-icon-badge>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Adresse</p>
                    <p class="text-sm text-slate-500">Faculté des Sciences Juridiques, Niamey</p>
                </div>
            </div>

            <div class="flex items-center gap-3 px-4 py-3">
                <x-icon-badge size="sm"><x-icon name="phone" class="h-5 w-5" /></x-icon-badge>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Contact</p>
                    <p class="text-sm text-slate-500">
                        <a href="tel:+227XXXXXXXX" class="hover:text-primary-600">+227 XX XX XX XX</a>
                        · <a href="https://wa.me/227XXXXXXXX" target="_blank" rel="noopener" class="hover:text-primary-600">WhatsApp</a>
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ============ SERVICE ICONS STRIP ============ --}}
<section id="services" class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        @foreach ([
            ['icon' => 'users', 'label' => 'Consultations juridiques gratuites'],
            ['icon' => 'book', 'label' => 'Information et orientation'],
            ['icon' => 'gavel', 'label' => 'Accompagnement juridique'],
            ['icon' => 'users', 'label' => 'Formations et sensibilisation'],
            ['icon' => 'document', 'label' => 'Ressources juridiques'],
            ['icon' => 'play-circle', 'label' => 'Web TV capsules juridiques'],
        ] as $item)
            <div class="flex flex-col items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-6 text-center">
                <x-icon :name="$item['icon']" class="h-10 w-10 text-primary-600" />
                <p class="text-sm font-semibold text-slate-800">{{ $item['label'] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ============ MISSION SPLIT ============ --}}
<section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:items-center">

        <div class="relative overflow-hidden rounded-xl">
            <x-picture
                path="placeholders/mission-consultation"
                alt="Avocat signant un document juridique, balance de la justice en arrière-plan"
                width="1672"
                height="941"
                class="aspect-[4/3] w-full object-cover"
            />

            <div class="absolute inset-x-0 bottom-0 flex divide-x divide-white/15 bg-navy-900/90 px-6 py-4">
                <x-stat variant="overlay" label="Étudiants encadrés par des professionnels" class="pr-6">
                    <x-icon name="users" class="h-6 w-6 text-white" />
                </x-stat>
                <x-stat variant="overlay" label="Au service de la communauté" class="pl-6">
                    <x-icon name="scale" class="h-6 w-6 text-white" />
                </x-stat>
            </div>
        </div>

        <div>
            <x-eyebrow color="muted">Notre mission</x-eyebrow>

            <h2 class="mt-2 text-3xl font-bold leading-tight text-slate-900">
                Former, accompagner
                <span class="block text-primary-600">et sensibiliser</span>
            </h2>

            <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">
                La Clinique Juridique FSJP-UAM vise à renforcer l'accès à la justice, à soutenir les personnes
                vulnérables et à offrir aux étudiants une expérience pratique encadrée, au service de la communauté.
            </p>

            <div class="mt-6">
                <x-button href="#" variant="outline">En savoir plus sur la Clinique</x-button>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <x-stat value="500+" label="Personnes accompagnées">
                    <x-icon-badge><x-icon name="users" class="h-6 w-6" /></x-icon-badge>
                </x-stat>
                <x-stat value="50+" label="Étudiants impliqués">
                    <x-icon-badge><x-icon name="user-plus" class="h-6 w-6" /></x-icon-badge>
                </x-stat>
                <x-stat value="15+" label="Activités et caravanes">
                    <x-icon-badge><x-icon name="document" class="h-6 w-6" /></x-icon-badge>
                </x-stat>
            </div>
        </div>

    </div>
</section>

{{-- ============ NEWS + VIDEOS ============ --}}
<section id="actualites-videos" class="bg-white py-8 sm:py-10">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 items-stretch gap-8 lg:grid-cols-12 lg:gap-8">

            {{-- RECENT NEWS --}}
            <div class="min-w-0 lg:col-span-7">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="text-xl font-bold leading-tight text-slate-900 sm:text-2xl">
                        Actualités récentes
                    </h2>

                    <a href="{{ url('/actualites') }}"
                       class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-blue-50 px-3 py-2 text-[11px] font-semibold text-blue-700 transition hover:bg-blue-100">
                        Voir toutes les actualités
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    @foreach ([
                        [
                            'date' => '12 oct. 2024',
                            'title' => "Conférence sur l'accès au droit des personnes vulnérables",
                            'img' => 'news-1',
                            'alt' => 'Conférence juridique'
                        ],
                        [
                            'date' => '28 sept. 2024',
                            'title' => "Caravane juridique à la région d'Ifrane",
                            'img' => 'news-2',
                            'alt' => 'Caravane juridique à Ifrane'
                        ],
                        [
                            'date' => '15 sept. 2024',
                            'title' => 'Lancement du guide pratique des droits familiaux',
                            'img' => 'news-3',
                            'alt' => 'Guide pratique des droits familiaux'
                        ],
                    ] as $article)
                        <article class="flex min-w-0 flex-col overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">

                            <a href="{{ url('/actualites') }}" class="block overflow-hidden">
                                <x-picture
                                    :path="'placeholders/' . $article['img']"
                                    :alt="$article['alt']"
                                    width="1536"
                                    height="1024"
                                    class="block aspect-[2/1] w-full object-cover transition duration-300 hover:scale-105"
                                />
                            </a>

                            <div class="flex flex-1 flex-col p-2.5">
                                <p class="text-[10px] font-medium leading-4 text-slate-500">
                                    {{ $article['date'] }}
                                </p>

                                <h3 class="mt-1 text-[11px] font-semibold leading-snug text-slate-900 sm:text-xs">
                                    {{ $article['title'] }}
                                </h3>

                                <a href="{{ url('/actualites') }}"
                                   aria-label="Lire la suite : {{ $article['title'] }}"
                                   class="mt-2.5 flex h-7 w-7 items-center justify-center self-end rounded-full bg-blue-800 text-white transition hover:bg-blue-700">
                                    <x-icon name="arrow-right" class="h-4 w-4" />
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            {{-- LATEST VIDEOS --}}
            <div class="flex min-w-0 flex-col lg:col-span-5">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="text-xl font-bold leading-tight text-slate-900 sm:text-2xl">
                        Nos dernières vidéos
                    </h2>

                    <a href="{{ url('/videos') }}"
                       class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-blue-50 px-3 py-2 text-[11px] font-semibold text-blue-700 transition hover:bg-blue-100">
                        Voir toutes les vidéos
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>

                <div class="grid flex-1 grid-cols-1 gap-3 sm:grid-cols-[minmax(0,1.7fr)_minmax(0,1fr)]">

                    {{-- FEATURED VIDEO --}}
                    <a href="{{ url('/videos') }}"
                       aria-label="Voir la présentation de la Clinique Juridique"
                       class="group relative block min-h-[180px] min-w-0 overflow-hidden rounded-lg bg-slate-200 shadow-sm sm:min-h-0">

                        <x-picture
                            path="placeholders/video-featured"
                            alt="Présentation de la Clinique Juridique"
                            width="1672"
                            height="941"
                            class="block h-full min-h-[180px] w-full object-cover transition duration-300 group-hover:scale-105 sm:min-h-0"
                        />

                        <span class="absolute inset-0 flex items-center justify-center">
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white/95 text-blue-700 shadow-md transition group-hover:scale-110">
                                <x-icon name="play" class="h-6 w-6" />
                            </span>
                        </span>

                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent px-3 pb-3 pt-9">
                            <p class="text-[11px] font-bold leading-snug text-white">
                                La Clinique Juridique FSJP-UAM
                            </p>
                            <p class="mt-1 text-[9px] leading-snug text-white/80">
                                Présentation de la Clinique et de ses missions
                            </p>
                        </div>

                        <span class="absolute bottom-2 right-2 rounded bg-black/75 px-1.5 py-0.5 text-[9px] text-white">
                            09:24
                        </span>
                    </a>

                    {{-- SMALL VIDEOS --}}
                    <div class="flex min-w-0 flex-col divide-y divide-slate-100">
                        @foreach ([
                            [
                                'title' => "L'accès à la justice pour tous",
                                'duration' => '03:12',
                                'img' => 'news-1'
                            ],
                            [
                                'title' => 'Les droits des femmes',
                                'duration' => '04:18',
                                'img' => 'news-2'
                            ],
                            [
                                'title' => 'La médiation juridique',
                                'duration' => '06:10',
                                'img' => 'news-3'
                            ],
                        ] as $video)
                            <a href="{{ url('/videos') }}"
                               class="group flex flex-1 min-w-0 items-center gap-2 py-2 px-1"
                               aria-label="Voir la vidéo : {{ $video['title'] }}">

                                <div class="relative w-[72px] shrink-0 overflow-hidden rounded-md bg-slate-200">
                                    <x-picture
                                        :path="'placeholders/' . $video['img']"
                                        alt=""
                                        width="160"
                                        height="100"
                                        class="block aspect-video w-full object-cover"
                                    />
                                    <span class="absolute bottom-1 right-1 rounded bg-black/80 px-1 py-0.5 text-[9px] leading-tight text-white">
                                        {{ $video['duration'] }}
                                    </span>
                                </div>

                                <div class="min-w-0 pt-0.5">
                                    <p class="text-[10px] font-semibold leading-snug text-slate-800 transition group-hover:text-blue-700 sm:text-[11px]">
                                        {{ $video['title'] }}
                                    </p>
                                    <p class="mt-1 text-[9px] text-slate-500">
                                        {{ $video['duration'] }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

{{-- ============ PARTNERS ============ --}}
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-900">Nos partenaires</h2>
        <a href="#" class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-blue-50 px-3 py-2 text-[11px] font-semibold text-blue-700 transition hover:bg-blue-100">
            Voir tous les partenaires
            <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>

    @include('partials.partners-grid')
</section>

@endsection
