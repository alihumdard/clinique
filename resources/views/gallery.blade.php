@extends('layouts.app')

@section('title', 'Galerie photos - ' . config('app.name'))
@section('meta_description', "Decouvrez en images les activites, caravanes juridiques, formations et rencontres communautaires de la Clinique Juridique FSJP-UAM.")

@section('content')

    <x-page-hero
        image="placeholders/gallery-hero"
        image-position="object-left-top"
        title="Galerie photos"
        subtitle="Découvrez en images nos activités, caravanes juridiques, formations et rencontres avec les communautés."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Galerie photos'],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        {{-- ============ CATEGORY FILTERS ============ --}}
        {{-- Static pills until Module 2 wires them to real album queries. --}}
        <div class="scrollbar-hide flex gap-1.5 overflow-x-auto">
            <x-filter-pill :active="true">Toutes les photos</x-filter-pill>
            <x-filter-pill>Caravanes juridiques</x-filter-pill>
            <x-filter-pill>Formations</x-filter-pill>
            <x-filter-pill>Sensibilisations</x-filter-pill>
            <x-filter-pill>Ateliers</x-filter-pill>
            <x-filter-pill>Rencontres</x-filter-pill>
            <x-filter-pill>Événements officiels</x-filter-pill>
        </div>

        @php
            /*
             * Album groups rendered on this page. Each album shows a slice of
             * its photos; the heading links through to the full album.
             * Module 2 replaces this array with a query over gallery records.
             */
            $albums = [
                [
                    'title' => 'Caravanes juridiques',
                    'photos' => [
                        ['path' => 'placeholders/news-caravan', 'alt' => 'Caravane juridique en plein air à Tillabéri'],
                        ['path' => 'placeholders/news-1', 'alt' => "Séance d'information avec les habitants"],
                        ['path' => 'placeholders/news-3', 'alt' => 'Échange avec les participantes à la caravane'],
                        ['path' => 'placeholders/gallery-caravan-team', 'alt' => "Équipe de la Clinique devant le véhicule de la caravane"],
                        ['path' => 'placeholders/gallery-caravan-meeting', 'alt' => "Entretien individuel lors d'une permanence mobile"],
                        ['path' => 'placeholders/news-candidatures', 'alt' => "Équipe de la Clinique en déplacement"],
                    ],
                ],
                [
                    'title' => 'Formations',
                    'photos' => [
                        ['path' => 'placeholders/news-formation', 'alt' => 'Formation des étudiants cliniciens en amphithéâtre'],
                        ['path' => 'placeholders/gallery-conference', 'alt' => 'Atelier pratique encadré par un enseignant'],
                        ['path' => 'placeholders/news-guide', 'alt' => 'Travaux de groupe pendant une session de formation'],
                    ],
                ],
                [
                    'title' => 'Sensibilisations',
                    'photos' => [
                        ['path' => 'placeholders/news-3', 'alt' => 'Campagne de sensibilisation devant un public rural'],
                        ['path' => 'placeholders/news-caravan', 'alt' => 'Sensibilisation des jeunes sur leurs droits'],
                        ['path' => 'placeholders/news-2', 'alt' => "Équipe de sensibilisation de la Clinique"],
                    ],
                ],
                [
                    'title' => 'Événements officiels',
                    'photos' => [
                        ['path' => 'placeholders/about-hero', 'alt' => 'Cérémonie officielle en présence des autorités'],
                        ['path' => 'placeholders/news-1', 'alt' => "Conférence sur l'accès au droit"],
                        ['path' => 'placeholders/news-partenariat', 'alt' => 'Signature officielle de partenariat'],
                    ],
                ],
            ];
        @endphp

        <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-3">

            {{-- ============ ALBUM GROUPS ============ --}}
            <div class="space-y-10 lg:col-span-2">
                @foreach ($albums as $album)
                    <section>
                        <x-section-heading :link="route('gallery')" link-label="Voir toutes les photos">
                            {{ $album['title'] }}
                        </x-section-heading>

                        <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3">
                            @foreach ($album['photos'] as $photo)
                                <x-photo-tile :path="$photo['path']" :alt="$photo['alt']" />
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">

                <x-sidebar-card icon="camera" title="Albums photos">
                    @php
                        $albumIndex = [
                            ['path' => 'placeholders/news-caravan', 'title' => 'Caravanes juridiques', 'count' => 124],
                            ['path' => 'placeholders/news-formation', 'title' => 'Formations', 'count' => 86],
                            ['path' => 'placeholders/news-3', 'title' => 'Sensibilisations', 'count' => 72],
                            ['path' => 'placeholders/gallery-conference', 'title' => 'Ateliers', 'count' => 48],
                            ['path' => 'placeholders/news-2', 'title' => 'Rencontres communautaires', 'count' => 65],
                            ['path' => 'placeholders/about-hero', 'title' => 'Événements officiels', 'count' => 39],
                        ];
                    @endphp

                    <ul class="space-y-3">
                        @foreach ($albumIndex as $entry)
                            <li>
                                <a href="{{ route('gallery') }}" class="group flex items-center gap-3">
                                    <x-picture
                                        :path="$entry['path']"
                                        alt=""
                                        width="80" height="80"
                                        class="h-12 w-12 shrink-0 rounded-md object-cover"
                                    />
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-semibold text-slate-900 group-hover:text-primary-600">{{ $entry['title'] }}</span>
                                        <span class="block text-xs text-slate-500">{{ $entry['count'] }} photos</span>
                                    </span>
                                    <x-icon name="arrow-right" class="h-4 w-4 shrink-0 text-slate-400 group-hover:text-primary-600" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-sidebar-card>

                <div class="rounded-xl bg-primary-50/60 p-5">
                    <div class="flex items-center gap-2.5">
                        <x-icon-badge size="sm"><x-icon name="image" class="h-5 w-5" /></x-icon-badge>
                        <h3 class="text-base font-bold text-slate-900">Notre galerie en chiffres</h3>
                    </div>

                    <x-figure-list
                        class="mt-4"
                        :items="[
                            ['icon' => 'image', 'value' => '450+', 'label' => 'Photos publiées', 'color' => 'blue'],
                            ['icon' => 'folder', 'value' => '6', 'label' => 'Albums thématiques', 'color' => 'amber'],
                            ['icon' => 'calendar', 'value' => '25+', 'label' => 'Événements couverts', 'color' => 'purple'],
                            ['icon' => 'map-pin', 'value' => '5', 'label' => 'Régions visitées', 'color' => 'green'],
                        ]"
                    />
                </div>

                <x-help-card icon="paper-plane" title="Vous avez des photos à partager ?">
                    Participez à notre galerie en nous envoyant vos photos d'activités.
                </x-help-card>

            </aside>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            title="Une activité à partager ?"
            button-label="Nous contacter"
            :button-href="route('contact')"
        >
            Contactez-nous pour que vos photos soient publiées dans notre galerie.
        </x-inline-cta>
    </section>

@endsection
