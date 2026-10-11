@extends('layouts.app')

@section('title', 'Accueil - ' . config('app.name'))
@section('meta_description', 'La Clinique Juridique de la FSJP-UAM offre des consultations juridiques gratuites, un accompagnement par des étudiants encadrés, et contribue à la promotion des droits humains.')

@section('content')

    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden bg-gradient-to-b from-primary-50/50 to-white">
        <div class="mx-auto max-w-7xl px-4 pt-10 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-start gap-10 lg:grid-cols-2">
                <div>
                    <x-eyebrow>Clinique Juridique FSJP - Université Abdou Moumouni</x-eyebrow>
                    <h1 class="mt-3 text-4xl font-bold leading-[1.1] text-slate-900 sm:text-5xl">
                        Un accès à la justice
                        <span class="block text-primary-600">pour tous</span>
                    </h1>
                    <p class="mt-5 max-w-xl text-base leading-relaxed text-slate-600">
                        La Clinique Juridique de la FSJP-UAM offre des consultations juridiques gratuites,
                        un accompagnement par des étudiants encadrés, et contribue à la promotion des droits humains.
                    </p>
                    <div class="mt-7 flex flex-wrap items-center gap-3">
                        <x-button :href="route('presaisine.index')" variant="primary">
                            <x-icon name="user-plus" class="h-4 w-4" />
                            Faire une pré-saisine
                        </x-button>
                        <x-button :href="route('services')" variant="outline">Découvrir nos services</x-button>
                    </div>
                </div>

                <div>
                    <x-picture
                        path="placeholders/hero-building"
                        alt="Bâtiment de la Faculté des Sciences Juridiques et Politiques"
                        width="1000" height="653"
                        :eager="true"
                        class="aspect-[16/10] w-full rounded-xl object-cover"
                    />
                </div>
            </div>

            {{-- Floating info card: same width as the hero image column, overlapping its bottom edge --}}
            <div class="relative hidden lg:block">
                <div class="absolute -top-16 right-0 z-10 grid w-full max-w-[calc(50%-1.25rem)] grid-cols-3 gap-6 rounded-xl border border-slate-200 bg-white p-6 shadow-lg">
                    <div class="flex items-start gap-3">
                        <x-icon-badge size="sm"><x-icon name="clock" class="h-5 w-5" /></x-icon-badge>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Horaires d'ouverture</p>
                            <p class="text-sm text-slate-500">{{ config('clinique.hours.days') }} : {{ config('clinique.hours.time') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <x-icon-badge size="sm"><x-icon name="map-pin" class="h-5 w-5" /></x-icon-badge>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Adresse</p>
                            <p class="text-sm text-slate-500">Faculté des Sciences<br>Juridiques, Niamey</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <x-icon-badge size="sm"><x-icon name="phone" class="h-5 w-5" /></x-icon-badge>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Contact</p>
                            <p class="text-sm text-slate-500">
                                <a href="tel:{{ config('clinique.contact.phone.tel') }}" class="hover:text-primary-600">{{ config('clinique.contact.phone.display') }}</a><br>
                                <a href="https://wa.me/{{ config('clinique.contact.whatsapp.number') }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary-600">WhatsApp</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile/tablet: info card stacks normally below the image, no overlap --}}
            <div class="mt-6 grid grid-cols-1 gap-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:grid-cols-3 lg:hidden">
                <div class="flex items-start gap-3">
                    <x-icon-badge size="sm"><x-icon name="clock" class="h-5 w-5" /></x-icon-badge>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Horaires d'ouverture</p>
                        <p class="text-sm text-slate-500">{{ config('clinique.hours.days') }} : {{ config('clinique.hours.time') }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <x-icon-badge size="sm"><x-icon name="map-pin" class="h-5 w-5" /></x-icon-badge>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Adresse</p>
                        <p class="text-sm text-slate-500">Faculté des Sciences Juridiques, Niamey</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <x-icon-badge size="sm"><x-icon name="phone" class="h-5 w-5" /></x-icon-badge>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Contact</p>
                        <p class="text-sm text-slate-500">
                            <a href="tel:{{ config('clinique.contact.phone.tel') }}" class="hover:text-primary-600">{{ config('clinique.contact.phone.display') }}</a><br>
                            <a href="https://wa.me/{{ config('clinique.contact.whatsapp.number') }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary-600">WhatsApp</a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="pb-16 lg:pb-24"></div>
        </div>
    </section>

    {{-- ============ SERVICE ICONS STRIP ============ --}}
    <section id="services" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ([
                ['icon' => 'users', 'label' => 'Consultations juridiques gratuites'],
                ['icon' => 'book', 'label' => 'Information et orientation'],
                ['icon' => 'gavel', 'label' => 'Accompagnement juridique'],
                ['icon' => 'users', 'label' => 'Formations et sensibilisation'],
                ['icon' => 'document', 'label' => 'Ressources juridiques'],
                ['icon' => 'play-circle', 'label' => 'Web TV capsules juridiques'],
            ] as $item)
                <div class="flex flex-col items-center gap-3 rounded-lg border border-slate-200 bg-white p-5 text-center">
                    <x-icon-badge>
                        <x-icon :name="$item['icon']" class="h-6 w-6" />
                    </x-icon-badge>
                    <p class="text-sm font-semibold text-slate-800">{{ $item['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============ MISSION SPLIT ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:items-start">
            <div class="relative overflow-hidden rounded-xl">
                <x-picture
                    path="placeholders/mission-consultation"
                    alt="Consultation juridique entre un étudiant et un bénéficiaire"
                    width="900" height="600"
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
                    <span class="block">et sensibiliser</span>
                </h2>
                <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">
                    La Clinique Juridique FSJP-UAM vise à renforcer l'accès à la justice, à soutenir les personnes
                    vulnérables et à offrir aux étudiants une expérience pratique encadrée, au service de la communauté.
                </p>
                <div class="mt-6">
                    <x-button :href="route('about')" variant="primary">En savoir plus sur la Clinique</x-button>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <x-stat value="500+" label="Personnes accompagnées">
                        <x-icon-badge size="sm"><x-icon name="users" class="h-5 w-5" /></x-icon-badge>
                    </x-stat>
                    <x-stat value="50+" label="Étudiants impliqués">
                        <x-icon-badge size="sm"><x-icon name="user-plus" class="h-5 w-5" /></x-icon-badge>
                    </x-stat>
                    <x-stat value="15+" label="Activités et caravanes">
                        <x-icon-badge size="sm"><x-icon name="document" class="h-5 w-5" /></x-icon-badge>
                    </x-stat>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ NEWS + VIDEOS ============ --}}
    <section class="bg-slate-50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-10 lg:grid-cols-5">

                {{-- News: 3/5 width --}}
                <div class="flex flex-col lg:col-span-3">
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-slate-900">Actualités récentes</h2>
                        <x-button :href="route('news.index')" variant="text">Voir toutes les actualités</x-button>
                    </div>

                    <div class="grid flex-1 grid-cols-1 gap-5 sm:grid-cols-3">
                        @foreach ([
                            ['date' => '12 oct. 2024', 'title' => "Conférence sur l'accès au droit des personnes vulnérables", 'img' => 'news-1', 'alt' => 'Salle de conférence'],
                            ['date' => '28 sept. 2024', 'title' => 'Caravane juridique à Niamey', 'img' => 'news-2', 'alt' => 'Équipe lors d\'une caravane juridique'],
                            ['date' => '15 sept. 2024', 'title' => 'Lancement du guide pratique des droits familiaux', 'img' => 'news-3', 'alt' => 'Statue de la justice'],
                        ] as $article)
                            <article class="flex flex-col overflow-hidden rounded-lg border border-slate-200 bg-white">
                                <x-picture
                                    :path="'placeholders/' . $article['img']"
                                    :alt="$article['alt']"
                                    width="600" height="400"
                                    class="aspect-[4/3] w-full object-cover"
                                />
                                <div class="flex flex-1 flex-col p-4">
                                    <p class="text-xs text-slate-400">{{ $article['date'] }}</p>
                                    <h3 class="mt-1 flex-1 text-sm font-semibold leading-snug text-slate-900">{{ $article['title'] }}</h3>
                                    <a href="{{ route('news.show') }}" aria-label="Lire la suite" class="mt-3 flex h-8 w-8 items-center justify-center self-start rounded-full bg-primary-50 text-primary-600 hover:bg-primary-100">
                                        <x-icon name="arrow-right" class="h-4 w-4" />
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                {{-- Videos: 2/5 width --}}
                <div class="lg:col-span-2">
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-slate-900">Nos dernières vidéos</h2>
                        <x-button :href="route('webtv.index')" variant="text">Voir toutes les vidéos</x-button>
                    </div>

                    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                        <div class="relative">
                            <x-picture
                                path="placeholders/video-featured"
                                alt="Vignette vidéo : présentation de la Clinique"
                                width="900" height="506"
                                class="aspect-video w-full object-cover"
                            />
                            <button type="button" aria-label="Lire la vidéo" class="absolute inset-0 flex items-center justify-center">
                                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/90 text-primary-600 shadow-md">
                                    <x-icon name="play-circle" class="h-7 w-7" />
                                </span>
                            </button>
                            <span class="absolute bottom-2 right-2 rounded bg-black/70 px-1.5 py-0.5 text-xs text-white">08:24</span>
                        </div>
                        <div class="p-3">
                            <p class="text-sm font-semibold text-slate-900">La Clinique Juridique FSJP-UAM</p>
                            <p class="text-xs text-slate-500">Présentation de la Clinique et de ses missions</p>
                        </div>
                    </div>

                    <ul class="mt-4 space-y-3">
                        @foreach ([
                            ['title' => "L'accès à la justice pour tous", 'duration' => '03:12', 'img' => 'news-1'],
                            ['title' => 'Les droits des femmes', 'duration' => '04:18', 'img' => 'news-2'],
                            ['title' => 'La médiation juridique', 'duration' => '06:10', 'img' => 'news-3'],
                        ] as $video)
                            <li class="flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-2.5">
                                <div class="relative shrink-0">
                                    <x-picture
                                        :path="'placeholders/' . $video['img']"
                                        alt=""
                                        width="80" height="56"
                                        class="h-14 w-20 rounded-md object-cover"
                                    />
                                    <span class="absolute bottom-1 right-1 rounded bg-black/70 px-1 text-[10px] leading-tight text-white">{{ $video['duration'] }}</span>
                                </div>
                                <p class="text-sm font-medium leading-snug text-slate-800">{{ $video['title'] }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ PARTNERS ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-slate-900">Nos partenaires</h2>
            <x-button :href="route('partners')" variant="text">Voir tous les partenaires</x-button>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            @foreach (['UAM', 'Ministère de la Justice', 'ANAJJ', 'Barreau du Niger', 'ONU Femmes', 'PNUD'] as $partner)
                <x-logo-placeholder :name="$partner" />
            @endforeach
        </div>
    </section>

@endsection
