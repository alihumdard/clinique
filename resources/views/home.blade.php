
@extends('layouts.app')

@section('title', 'Accueil - ' . config('app.name'))

@section('meta_description', 'La Clinique Juridique de la FSJP-UAM offre des consultations juridiques gratuites, un accompagnement par des étudiants encadrés, et contribue à la promotion des droits humains.')

@section('content')

{{-- ============ SERVICE STATUS ============ --}}
{{-- Module 2 drives the state and message from CMS settings. --}}
<x-service-status-banner state="open" />

{{-- ============ HERO ============ --}}
<section class="relative overflow-hidden bg-primary-50">
    {{-- Photo (full-bleed to viewport edge, independent of the content max-width) --}}
    <div class="absolute inset-y-0 right-0 hidden w-1/2 lg:block">
        <x-picture
            path="placeholders/hero-fsjp"
            alt=""
            width="921"
            height="759"
            eager
            class="absolute inset-0 h-full w-full object-cover object-center"
            aria-hidden="true"
        />
        <div class="absolute inset-0 bg-gradient-to-r from-primary-50 via-primary-50/25 to-transparent"></div>
    </div>

    <div class="relative mx-auto grid max-w-7xl grid-cols-1 lg:flex lg:items-center">

        {{-- Mobile/tablet: photo as the hero's own background, behind the text --}}
        <div class="absolute inset-0 lg:hidden">
            <x-picture
                path="placeholders/hero-fsjp"
                alt=""
                width="921"
                height="759"
                eager
                class="absolute inset-0 h-full w-full object-cover object-center"
                aria-hidden="true"
            />
            <div class="absolute inset-0 bg-primary-50/85"></div>
        </div>

        <div class="relative z-10 mx-auto flex w-full max-w-xl flex-col justify-center px-4 py-14 sm:px-6 lg:w-1/2 lg:max-w-2xl lg:px-8 lg:pr-0 xl:ml-auto xl:mr-0">
            <p class="text-sm font-bold uppercase tracking-widest text-primary-600">
                Clinique Juridique FSJP — Université Abdou Moumouni
            </p>

            <h1 class="mt-4 text-4xl font-extrabold leading-tight text-slate-900 sm:text-5xl">
                Un accès à la justice
                <span class="block text-primary-600">pour tous</span>
            </h1>

            <p class="mt-5 max-w-xl text-base leading-relaxed text-slate-600 sm:text-lg">
                La Clinique Juridique de la FSJP-UAM offre des consultations juridiques gratuites,
                un accompagnement par des étudiants encadrés,
                et contribue à la promotion des droits humains.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3">
                <x-button href="#presaisine" variant="primary" class="!px-6 !py-3.5 !text-base">
                    <x-icon name="user-plus" class="h-5 w-5" />
                    Faire une pré-saisine
                </x-button>
                <x-button href="#services" variant="outline" class="!px-6 !py-3.5 !text-base">Découvrir nos services</x-button>
            </div>
        </div>

        {{-- Spacer column reserving space for the full-bleed photo behind --}}
        <div class="hidden min-h-[420px] lg:block lg:w-1/2"></div>
    </div>

    {{-- Floating information card (overlaps photo bottom edge) --}}
    <div class="relative z-10 mx-auto -mt-10 mb-4 hidden max-w-7xl px-4 sm:px-6 lg:block lg:px-8">
        <div class="ml-auto grid w-[640px] grid-cols-3 divide-x divide-slate-200 rounded-xl bg-white shadow-xl">

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
                        <a href="tel:{{ config('clinique.contact.phone.tel') }}" class="hover:text-primary-600">{{ config('clinique.contact.phone.display') }}</a><br>
                        <a href="{{ 'https://wa.me/' . config('clinique.contact.whatsapp.number') }}" target="_blank" rel="noopener" class="hover:text-primary-600">WhatsApp</a>
                    </p>
                </div>
            </div>

        </div>
    </div>

    <div class="mx-auto max-w-7xl lg:hidden">
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
                        <a href="tel:{{ config('clinique.contact.phone.tel') }}" class="hover:text-primary-600">{{ config('clinique.contact.phone.display') }}</a>
                        · <a href="{{ 'https://wa.me/' . config('clinique.contact.whatsapp.number') }}" target="_blank" rel="noopener" class="hover:text-primary-600">WhatsApp</a>
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ============ SERVICE ICONS STRIP ============ --}}
<section id="services" class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        @foreach ([
            ['icon' => 'users', 'label' => 'Consultations juridiques gratuites'],
            ['icon' => 'book', 'label' => 'Information et orientation'],
            ['icon' => 'gavel', 'label' => 'Accompagnement juridique'],
            ['icon' => 'users', 'label' => 'Formations et sensibilisation'],
            ['icon' => 'document', 'label' => 'Ressources juridiques'],
            ['icon' => 'play-circle', 'label' => 'Web TV capsules juridiques'],
        ] as $item)
            <div class="flex cursor-pointer flex-col items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3 py-5 text-center transition hover:shadow-md">
                <x-icon :name="$item['icon']" class="h-10 w-10 text-primary-600" />
                <p class="text-sm font-semibold text-slate-800">{{ $item['label'] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ============ LEGAL DOMAINS ============ --}}
{{--
    The plan asks for each academic heading to carry a plain-language
    restatement, so a visitor can tell whether their problem is covered
    without knowing the legal vocabulary.
--}}
<section class="bg-slate-50 py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-eyebrow>Nos domaines d'intervention</x-eyebrow>
        <h2 class="mt-3 text-2xl font-bold text-slate-900 sm:text-3xl">Dans quels domaines pouvons-nous vous aider ?</h2>
        <p class="mt-3 max-w-2xl text-base leading-relaxed text-slate-600">
            Nos étudiants encadrés vous informent et vous orientent dans les principaux domaines du droit.
            Si votre situation n'apparaît pas ici, contactez-nous : nous vous orienterons vers le bon interlocuteur.
        </p>

        @php
            // The swatch map stays here: Tailwind only generates classes it
            // can see as literal strings, so these cannot be built from a
            // colour name at runtime.
            $domainSwatches = [
                'blue'   => 'bg-primary-50 text-primary-600',
                'green'  => 'bg-emerald-50 text-emerald-600',
                'amber'  => 'bg-amber-50 text-amber-600',
                'red'    => 'bg-rose-50 text-rose-600',
                'purple' => 'bg-violet-50 text-violet-600',
                'pink'   => 'bg-pink-50 text-pink-600',
                'slate'  => 'bg-slate-100 text-slate-600',
            ];
        @endphp

        <ul class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($domains as $domain)
                <li class="rounded-xl border border-slate-200 bg-white p-5 transition hover:shadow-md">
                    <span class="flex h-11 w-11 items-center justify-center rounded-full {{ $domainSwatches[$domain->tone] ?? $domainSwatches['blue'] }}">
                        <x-icon :name="$domain->icon ?? 'scale'" class="h-5 w-5" />
                    </span>
                    <h3 class="mt-4 text-base font-semibold text-slate-900">{{ $domain->name }}</h3>
                    {{-- The plain-language line is the point of this card. --}}
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $domain->plain_language }}</p>
                </li>
            @endforeach
        </ul>

        <div class="mt-8">
            <x-button :href="route('services')" variant="outline">Découvrir tous nos services</x-button>
        </div>
    </div>
</section>

{{-- ============ HOW IT WORKS ============ --}}
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <x-eyebrow>Comment ça marche</x-eyebrow>
    <h2 class="mt-3 text-2xl font-bold text-slate-900 sm:text-3xl">Quatre étapes, de votre demande à la réponse</h2>

    @php
        $steps = [
            ['icon' => 'document',    'title' => 'Vous décrivez votre situation', 'text' => "En ligne via la pré-saisine, ou directement à l'accueil pendant les permanences."],
            ['icon' => 'search',      'title' => 'Nous étudions votre demande',   'text' => "Un étudiant clinicien encadré examine votre dossier et vérifie sa recevabilité."],
            ['icon' => 'users',       'title' => 'Vous êtes reçu en consultation', 'text' => "Un rendez-vous est fixé dans nos locaux à Niamey pour approfondir votre situation."],
            ['icon' => 'check-circle','title' => 'Vous recevez une orientation',  'text' => "Une réponse écrite, validée par un encadrant, vous informe de vos droits et démarches."],
        ];
    @endphp

    <ol class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($steps as $i => $step)
            <li class="relative flex flex-col rounded-xl border border-slate-200 bg-white p-6">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white">
                    {{ $i + 1 }}
                </span>
                <x-icon :name="$step['icon']" class="mt-4 h-6 w-6 text-primary-600" />
                <h3 class="mt-3 text-base font-semibold text-slate-900">{{ $step['title'] }}</h3>
                <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $step['text'] }}</p>
            </li>
        @endforeach
    </ol>

    <div class="mt-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            title="Prêt à nous soumettre votre situation ?"
            button-label="Faire une pré-saisine"
            :button-href="route('presaisine.index')"
        >
            La pré-saisine est gratuite et vous oriente en quelques minutes vers la bonne démarche.
        </x-inline-cta>
    </div>
</section>

{{-- ============ MISSION SPLIT ============ --}}
<section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:items-stretch">

        <div class="relative overflow-hidden rounded-xl">
            <x-picture
                path="placeholders/mission-consultation"
                alt="Avocat signant un document juridique, balance de la justice en arrière-plan"
                width="1456"
                height="1080"
                class="aspect-[4/3] w-full object-cover"
            />

            <div class="absolute inset-x-0 bottom-0 flex divide-x divide-white/15 bg-navy-900/90 px-6 py-4">
                <x-stat variant="overlay" label="Étudiants encadrés par des professionnels" class="pr-6">
                    <x-icon name="users" class="h-7 w-7 text-white" />
                </x-stat>
                <x-stat variant="overlay" label="Au service de la communauté" class="pl-6">
                    <x-icon name="scale" class="h-7 w-7 text-white" />
                </x-stat>
            </div>
        </div>

        <div class="flex flex-col lg:h-full lg:justify-between">
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
                    <x-icon name="users" class="h-8 w-8 shrink-0 text-primary-600" />
                </x-stat>
                <x-stat value="50+" label="Étudiants impliqués">
                    <x-icon name="user-plus" class="h-8 w-8 shrink-0 text-primary-600" />
                </x-stat>
                <x-stat value="15+" label="Activités et caravanes">
                    <x-icon name="document" class="h-8 w-8 shrink-0 text-primary-600" />
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
                        <x-icon name="arrow-right" class="h-5 w-5" />
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

                                <div class="mt-auto flex justify-end pt-2.5">
                                    <a href="{{ url('/actualites') }}"
                                       aria-label="Lire la suite : {{ $article['title'] }}"
                                       class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-800 text-white transition hover:bg-blue-700">
                                        <x-icon name="arrow-right" class="h-5 w-5" />
                                    </a>
                                </div>
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
                        <x-icon name="arrow-right" class="h-5 w-5" />
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
                            width="1536"
                            height="1024"
                            class="block h-full min-h-[180px] w-full object-cover transition duration-300 group-hover:scale-105 sm:min-h-0"
                        />

                        <span class="absolute inset-0 flex items-center justify-center">
                            <svg class="h-14 w-14 text-white drop-shadow-lg transition group-hover:scale-110" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M8 5v14l11-7-11-7z" />
                            </svg>
                        </span>

                        <div class="absolute inset-x-0 bottom-0 flex items-end justify-between bg-gradient-to-t from-black/90 via-black/50 to-transparent px-3 pb-3 pt-9">
                            <div>
                                <p class="text-[11px] font-bold leading-snug text-white">
                                    La Clinique Juridique FSJP-UAM
                                </p>
                                <p class="mt-1 text-[9px] leading-snug text-white/80">
                                    Présentation de la Clinique et de ses missions
                                </p>
                            </div>

                            <span class="shrink-0 rounded bg-black/75 px-1.5 py-0.5 text-[9px] text-white">
                                09:24
                            </span>
                        </div>
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
                                    <span class="absolute inset-0 flex items-center justify-center">
                                        <svg class="h-7 w-7 text-white drop-shadow" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path d="M8 5v14l11-7-11-7z" />
                                        </svg>
                                    </span>
                                    <span class="absolute right-1 top-1 rounded bg-black/80 px-1 py-0.5 text-[9px] leading-tight text-white">
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

{{-- ============ ACTIVITIES ============ --}}
<section class="bg-slate-50 py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-section-heading :link="route('activities.index')" link-label="Voir toutes les activités">
            Activités et caravanes juridiques
        </x-section-heading>

        <p class="mt-3 max-w-2xl text-base leading-relaxed text-slate-600">
            Nous allons à la rencontre des populations, y compris dans les zones rurales et périurbaines.
        </p>

        @php
            $homeActivities = [
                [
                    'image' => 'placeholders/news-caravan',
                    'category' => 'Caravane juridique',
                    'color' => 'amber',
                    'date' => '28 septembre 2024',
                    'location' => 'Tillabéri',
                    'title' => 'Caravane juridique à Tillabéri',
                    'excerpt' => "Une journée d'information et de conseil juridique pour les populations locales.",
                ],
                [
                    'image' => 'placeholders/news-3',
                    'category' => 'Sensibilisation',
                    'color' => 'purple',
                    'date' => '12 octobre 2024',
                    'location' => 'Niamey',
                    'title' => 'Session de sensibilisation des jeunes',
                    'excerpt' => "Sensibilisation des étudiants sur l'accès au droit et la prévention des conflits.",
                ],
                [
                    'image' => 'placeholders/news-formation',
                    'category' => 'Formation',
                    'color' => 'green',
                    'date' => '5 septembre 2024',
                    'location' => 'Niamey',
                    'title' => 'Formation sur la médiation',
                    'excerpt' => "Renforcement des capacités des étudiants sur les techniques de médiation.",
                ],
            ];
        @endphp

        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($homeActivities as $activity)
                <x-activity-card
                    :image="$activity['image']"
                    :category="$activity['category']"
                    :category-color="$activity['color']"
                    :date="$activity['date']"
                    :location="$activity['location']"
                    :title="$activity['title']"
                    :excerpt="$activity['excerpt']"
                    :href="route('activities.index')"
                />
            @endforeach
        </div>
    </div>
</section>

{{-- ============ PRACTICAL INFO ============ --}}
{{--
    The plan requires the homepage itself to carry the permanence hours,
    the address of the local and a clickable phone and WhatsApp link —
    a visitor should not have to reach the contact page for those.
--}}
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <x-eyebrow>Infos pratiques</x-eyebrow>
    <h2 class="mt-3 text-2xl font-bold text-slate-900 sm:text-3xl">Nous rencontrer, nous joindre</h2>

    <div class="mt-8 grid grid-cols-1 gap-5 lg:grid-cols-3">

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <x-icon-badge size="sm"><x-icon name="clock" class="h-5 w-5" /></x-icon-badge>
            <h3 class="mt-4 text-base font-bold text-slate-900">Horaires des permanences</h3>

            <dl class="mt-3 space-y-1.5 text-sm">
                @foreach (config('clinique.hours.schedule') as $slot)
                    <div class="flex items-baseline justify-between gap-4">
                        <dt class="text-slate-600">{{ $slot['days'] }}</dt>
                        <dd class="font-semibold text-slate-900">{{ $slot['time'] }}</dd>
                    </div>
                @endforeach
            </dl>

            <p class="mt-3 text-sm text-slate-500">{{ config('clinique.hours.closed') }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <x-icon-badge size="sm"><x-icon name="map-pin" class="h-5 w-5" /></x-icon-badge>
            <h3 class="mt-4 text-base font-bold text-slate-900">Adresse du local</h3>

            <address class="mt-3 text-sm not-italic leading-relaxed text-slate-600">
                {{ config('clinique.contact.address.line1') }}<br>
                {{ config('clinique.contact.address.line2') }}<br>
                {{ config('clinique.contact.address.line3') }}, {{ config('clinique.contact.address.city') }}
            </address>

            <p class="mt-3 text-sm text-slate-500">{{ config('clinique.hours.note') }}</p>
        </div>

        <div class="rounded-xl bg-primary-50/60 p-6">
            <x-icon-badge size="sm" class="!bg-white"><x-icon name="phone" class="h-5 w-5" /></x-icon-badge>
            <h3 class="mt-4 text-base font-bold text-slate-900">Nous joindre</h3>

            <ul class="mt-3 space-y-2.5 text-sm">
                <li>
                    <a href="tel:{{ config('clinique.contact.phone.tel') }}" class="inline-flex items-center gap-2 font-semibold text-primary-700 hover:text-primary-800">
                        <x-icon name="phone" class="h-4 w-4 shrink-0" />
                        {{ config('clinique.contact.phone.display') }}
                    </a>
                </li>
                <li>
                    <a href="https://wa.me/{{ config('clinique.contact.whatsapp.number') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 font-semibold text-primary-700 hover:text-primary-800">
                        <x-icon name="whatsapp" class="h-4 w-4 shrink-0" />
                        WhatsApp
                    </a>
                </li>
                <li>
                    <a href="mailto:{{ config('clinique.contact.email') }}" class="inline-flex items-center gap-2 break-all font-semibold text-primary-700 hover:text-primary-800">
                        <x-icon name="mail" class="h-4 w-4 shrink-0" />
                        {{ config('clinique.contact.email') }}
                    </a>
                </li>
            </ul>

            <x-button :href="route('contact')" variant="primary" class="mt-5 w-full justify-center">
                Nous contacter
            </x-button>
        </div>
    </div>
</section>

{{-- ============ FAQ ============ --}}
<section class="bg-slate-50 py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

            <div>
                <x-eyebrow>Questions fréquentes</x-eyebrow>
                <h2 class="mt-3 text-2xl font-bold text-slate-900 sm:text-3xl">Vous vous posez une question ?</h2>
                <p class="mt-3 text-base leading-relaxed text-slate-600">
                    Les réponses aux questions que l'on nous pose le plus souvent.
                </p>

                <x-button :href="route('services.eligibility')" variant="outline" class="mt-5">
                    Conditions d'accès
                </x-button>
            </div>

            {{-- Native <details>: keyboard-accessible, works without JavaScript. --}}
            <div class="space-y-3 lg:col-span-2">
                <x-faq-item question="Les consultations sont-elles gratuites ?">
                    Oui. Les consultations juridiques de la Clinique sont gratuites. Seuls certains frais
                    administratifs, fixés et affichés à l'avance, peuvent s'appliquer pour des démarches
                    particulières — ils sont détaillés sur la page des tarifs administratifs.
                </x-faq-item>

                <x-faq-item question="Qui peut bénéficier de nos services ?">
                    Toute personne confrontée à une difficulté juridique peut nous solliciter, avec une
                    attention particulière aux personnes en situation de vulnérabilité. L'accueil se tient
                    uniquement à Niamey ; si vous ne pouvez pas vous y déplacer, contactez-nous et nous vous
                    orienterons vers un partenaire proche de chez vous.
                </x-faq-item>

                <x-faq-item question="Qui va traiter mon dossier ?">
                    Des étudiants en droit de la FSJP, systématiquement encadrés par des enseignants et des
                    praticiens. Aucune réponse ne vous est transmise sans avoir été validée par un encadrant.
                </x-faq-item>

                <x-faq-item question="Quels documents dois-je apporter ?">
                    Une pièce d'identité et tout document utile à votre situation : contrat, acte, courrier
                    reçu, décision administrative. Si vous n'avez aucun document, venez tout de même : nous
                    vous indiquerons les pièces à réunir.
                </x-faq-item>

                <x-faq-item question="Combien de temps avant d'obtenir une réponse ?">
                    Après une pré-saisine, vous recevez une réponse sur la recevabilité de votre demande dans
                    un délai de cinq jours ouvrables. Le délai de traitement complet dépend ensuite de la
                    complexité de votre situation.
                </x-faq-item>
            </div>
        </div>
    </div>
</section>

{{-- ============ PARTNERS ============ --}}
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-900">Nos partenaires</h2>
        <a href="{{ route('partners') }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-blue-50 px-3 py-2 text-[11px] font-semibold text-blue-700 transition hover:bg-blue-100">
            Voir tous les partenaires
            <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>

    @include('partials.partners-grid')
</section>

@endsection
