@extends('layouts.app')

@section('title', 'Web TV - ' . config('app.name'))
@section('meta_description', "Des videos pour informer, sensibiliser et promouvoir l'acces au droit pour tous : capsules, emissions, temoignages et formations de la Clinique Juridique FSJP-UAM.")

@section('content')

    <x-page-hero
        image="placeholders/webtv-hero"
        image-position="object-right"
        title="Web TV"
        subtitle="Des vidéos pour informer, sensibiliser et promouvoir l'accès au droit pour tous."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Web TV'],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        {{-- ============ CATEGORY FILTERS ============ --}}
        {{-- Static pills until Module 2 wires them to real rubrique queries. --}}
        <div class="scrollbar-hide flex gap-1.5 overflow-x-auto">
            <x-filter-pill :active="true">Toutes les vidéos</x-filter-pill>
            <x-filter-pill>Émissions</x-filter-pill>
            <x-filter-pill>Caravanes juridiques</x-filter-pill>
            <x-filter-pill>Sensibilisations</x-filter-pill>
            <x-filter-pill>Formations</x-filter-pill>
            <x-filter-pill>Témoignages</x-filter-pill>
            <x-filter-pill>Interviews</x-filter-pill>
            <x-filter-pill>Événements officiels</x-filter-pill>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-3">

            {{-- ============ MAIN ============ --}}
            <div class="lg:col-span-2">

                {{-- Featured capsule --}}
                <x-section-label>Vidéo à la une</x-section-label>

                <div class="mt-5 grid grid-cols-1 gap-6 lg:grid-cols-[3fr_2fr] lg:items-stretch">
                    <x-video-player
                        poster="placeholders/video-featured"
                        video-id="dQw4w9WgXcQ"
                        duration="12:35"
                        title="Lancement des activités de la Clinique Juridique FSJP-UAM"
                        class="!aspect-auto lg:h-full lg:max-h-80"
                    />

                    <div class="flex flex-col">
                        <x-category-badge color="primary" position="inline" class="self-start">Événement officiel</x-category-badge>

                        <h3 class="mt-3 text-xl font-bold leading-snug text-slate-900">
                            Lancement des activités de la Clinique Juridique FSJP-UAM
                        </h3>

                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-500">
                            <span class="inline-flex items-center gap-1.5">
                                <x-icon name="calendar" class="h-3.5 w-3.5 shrink-0" />
                                15 mars 2024
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <x-icon name="eye" class="h-3.5 w-3.5 shrink-0" />
                                2 450 vues
                            </span>
                        </div>

                        <p class="mt-3 text-sm leading-relaxed text-slate-600">
                            Revivez les temps forts de la cérémonie de lancement des activités de la Clinique
                            Juridique FSJP-UAM, avec les interventions des autorités universitaires et des partenaires.
                        </p>

                        <div class="mt-5">
                            <x-button :href="route('webtv.show', ['poster' => 'placeholders/video-featured'])" variant="primary">
                                Voir la vidéo
                            </x-button>
                        </div>
                    </div>
                </div>

                {{-- Latest capsules --}}
                <div class="mt-12">
                    <x-section-heading :link="route('webtv.index')" link-label="Voir toutes les vidéos">
                        Dernières vidéos
                    </x-section-heading>

                    @php
                        /*
                         * Capsule grid. Module 2 replaces this with a query;
                         * each record carries its own external video address.
                         */
                        $videos = [
                            ['image' => 'placeholders/news-caravan', 'category' => 'Caravane juridique', 'color' => 'amber', 'duration' => '8:24', 'title' => "Caravane juridique à Tillabéri : accès au droit pour les populations", 'date' => '28 septembre 2024', 'views' => '1 250'],
                            ['image' => 'placeholders/news-3', 'category' => 'Sensibilisation', 'color' => 'purple', 'duration' => '6:18', 'title' => "Les droits des femmes : paroles d'étudiantes", 'date' => '12 octobre 2024', 'views' => '980'],
                            ['image' => 'placeholders/news-formation', 'category' => 'Formation', 'color' => 'green', 'duration' => '15:42', 'title' => 'Session de formation sur la médiation', 'date' => '5 septembre 2024', 'views' => '1 420'],
                            ['image' => 'placeholders/webtv-interview', 'category' => 'Interview', 'color' => 'slate', 'duration' => '10:36', 'title' => 'Entretien avec le Doyen de la FSJP sur la Clinique Juridique', 'date' => '20 août 2024', 'views' => '2 100'],
                            ['image' => 'placeholders/news-2', 'category' => 'Témoignage', 'color' => 'red', 'duration' => '7:50', 'title' => "Témoignages : l'impact des caravanes juridiques", 'date' => '15 juillet 2024', 'views' => '1 780'],
                            ['image' => 'placeholders/news-1', 'category' => 'Émission', 'color' => 'primary', 'duration' => '9:12', 'title' => 'Accès au droit : enjeux et perspectives', 'date' => '3 juin 2024', 'views' => '1 560'],
                            ['image' => 'placeholders/about-hero', 'category' => 'Événement officiel', 'color' => 'primary', 'duration' => '12:20', 'title' => 'Table ronde : justice et développement local', 'date' => '25 mai 2024', 'views' => '1 320'],
                            ['image' => 'placeholders/news-candidatures', 'category' => 'Sensibilisation', 'color' => 'purple', 'duration' => '6:45', 'title' => 'Protection des enfants : que dit la loi ?', 'date' => '18 avril 2024', 'views' => '940'],
                            ['image' => 'placeholders/news-partenariat', 'category' => 'Formation', 'color' => 'green', 'duration' => '11:08', 'title' => 'Les mécanismes de règlement des conflits', 'date' => '10 mars 2024', 'views' => '1 110'],
                        ];
                    @endphp

                    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($videos as $video)
                            <x-video-card
                                :image="$video['image']"
                                :category="$video['category']"
                                :category-color="$video['color']"
                                :duration="$video['duration']"
                                :title="$video['title']"
                                :date="$video['date']"
                                :views="$video['views']"
                                :href="route('webtv.show', ['poster' => $video['image']])"
                            />
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">

                <div class="rounded-xl bg-primary-50/60 p-5">
                    <div class="flex items-center gap-2.5">
                        <x-icon-badge size="sm">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M23.5 6.2a3.02 3.02 0 00-2.12-2.14C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.38.56A3.02 3.02 0 00.5 6.2 31.6 31.6 0 000 12a31.6 31.6 0 00.5 5.8 3.02 3.02 0 002.12 2.14c1.88.56 9.38.56 9.38.56s7.5 0 9.38-.56a3.02 3.02 0 002.12-2.14A31.6 31.6 0 0024 12a31.6 31.6 0 00-.5-5.8zM9.75 15.5v-7l6.5 3.5-6.5 3.5z"/></svg>
                        </x-icon-badge>
                        <h2 class="text-base font-bold text-slate-900">Web TV en chiffres</h2>
                    </div>

                    <x-figure-list
                        class="mt-4"
                        :items="[
                            ['icon' => 'youtube', 'value' => '120+', 'label' => 'Vidéos publiées', 'color' => 'red'],
                            ['icon' => 'users', 'value' => '50 000+', 'label' => 'Vues cumulées', 'color' => 'blue'],
                            ['icon' => 'graduation-cap', 'value' => '15', 'label' => 'Émissions', 'color' => 'green'],
                            ['icon' => 'folder', 'value' => '8', 'label' => 'Séries thématiques', 'color' => 'purple'],
                        ]"
                    />
                </div>

                <x-sidebar-card icon="folder" title="Playlists thématiques" :tint="true">
                    @php
                        $playlists = [
                            ['image' => 'placeholders/news-caravan', 'title' => 'Caravanes juridiques', 'count' => 12],
                            ['image' => 'placeholders/news-formation', 'title' => 'Formations', 'count' => 18],
                            ['image' => 'placeholders/news-3', 'title' => 'Sensibilisations', 'count' => 20],
                            ['image' => 'placeholders/about-team', 'title' => 'Interviews', 'count' => 10],
                            ['image' => 'placeholders/about-hero', 'title' => 'Événements officiels', 'count' => 8],
                        ];
                    @endphp

                    <ul class="space-y-3">
                        @foreach ($playlists as $playlist)
                            <li>
                                <a href="{{ route('webtv.index') }}" class="group flex items-center gap-3">
                                    <x-picture
                                        :path="$playlist['image']"
                                        alt=""
                                        width="80" height="80"
                                        class="h-12 w-12 shrink-0 rounded-md object-cover"
                                    />
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-semibold text-slate-900 group-hover:text-primary-600">{{ $playlist['title'] }}</span>
                                        <span class="block text-xs text-slate-500">{{ $playlist['count'] }} vidéos</span>
                                    </span>
                                    <x-icon name="arrow-right" class="h-4 w-4 shrink-0 text-slate-400 group-hover:text-primary-600" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-sidebar-card>

                <x-sidebar-card icon="youtube" title="Nos chaînes" :tint="true">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M23.5 6.2a3.02 3.02 0 00-2.12-2.14C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.38.56A3.02 3.02 0 00.5 6.2 31.6 31.6 0 000 12a31.6 31.6 0 00.5 5.8 3.02 3.02 0 002.12 2.14c1.88.56 9.38.56 9.38.56s7.5 0 9.38-.56a3.02 3.02 0 002.12-2.14A31.6 31.6 0 0024 12a31.6 31.6 0 00-.5-5.8zM9.75 15.5v-7l6.5 3.5-6.5 3.5z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900">YouTube</p>
                            <p class="mt-0.5 text-sm leading-relaxed text-slate-600">
                                Suivez toutes nos vidéos sur notre chaîne officielle.
                            </p>
                        </div>
                    </div>

                    <x-button href="#" variant="outline" class="mt-4 w-full justify-center">
                        Voir la chaîne
                    </x-button>
                </x-sidebar-card>

                <x-help-card icon="bell" title="Vous avez une vidéo à proposer ?">
                    Partagez vos initiatives, témoignages ou événements pour une diffusion sur notre Web TV.
                </x-help-card>

            </aside>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            title="Abonnez-vous à notre Web TV"
            button-label="Voir la chaîne YouTube"
            button-href="#"
        >
            Restez informé de nos dernières vidéos, émissions et événements.
        </x-inline-cta>
    </section>

@endsection
