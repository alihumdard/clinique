@extends('layouts.app')

@section('title', "Caravane juridique à Tillabéri - " . config('app.name'))
@section('meta_description', "Une capsule video qui presente les objectifs, les activites et l'impact de la caravane juridique organisee par la Clinique Juridique FSJP-UAM a Tillaberi.")

@section('content')

    <x-page-hero
        image="placeholders/webtv-show-hero"
        title="Caravane juridique à Tillabéri : accès au droit pour les populations"
        subtitle="Une capsule vidéo qui présente les objectifs, les activités et l'impact de la caravane juridique organisée par la Clinique Juridique FSJP-UAM."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Web TV', 'url' => route('webtv.index')],
            ['label' => 'Caravane juridique à Tillabéri'],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">

            {{-- ============ MAIN ============ --}}
            <div class="lg:col-span-2">

                <x-video-player
                    poster="placeholders/webtv-show-hero"
                    video-id="dQw4w9WgXcQ"
                    duration="12:35"
                    title="Caravane juridique à Tillabéri : accès au droit pour les populations"
                />

                <div class="mt-5">
                    <x-category-badge color="primary" position="inline">Caravane juridique</x-category-badge>

                    <h2 class="mt-3 text-xl font-bold leading-snug text-slate-900 sm:text-2xl">
                        Caravane juridique à Tillabéri : accès au droit pour les populations
                    </h2>

                    {{-- Meta strip: publication data left, actions right --}}
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-x-5 gap-y-2 border-b border-slate-100 pb-5 text-sm text-slate-500">
                        <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                            <span class="inline-flex items-center gap-1.5">
                                <x-icon name="calendar" class="h-4 w-4 shrink-0" />
                                28 septembre 2024
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <x-icon name="eye" class="h-4 w-4 shrink-0" />
                                1 250 vues
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <x-icon name="thumb-up" class="h-4 w-4 shrink-0" />
                                42
                            </span>
                        </div>

                        <div class="flex items-center gap-x-5 gap-y-2">
                            <button type="button" class="inline-flex items-center gap-1.5 hover:text-primary-600">
                                <x-icon name="share" class="h-4 w-4 shrink-0" />
                                Partager
                            </button>
                            <button type="button" class="inline-flex items-center gap-1.5 hover:text-primary-600">
                                <x-icon name="bookmark" class="h-4 w-4 shrink-0" />
                                Enregistrer
                            </button>
                            <button type="button" aria-label="Plus d'options" class="text-slate-400 hover:text-slate-600">
                                <x-icon name="dots-vertical" class="h-4 w-4 shrink-0" />
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ============ TABBED DETAIL ============ --}}
                <x-tabs
                    class="mt-6"
                    name="capsule"
                    :tabs="[
                        'description' => 'Description',
                        'intervenants' => 'Intervenants',
                        'chapitres' => 'Chapitres',
                        'documents' => 'Documents associés',
                    ]"
                >
                    <x-slot:description>
                        <div class="rounded-lg bg-primary-50/40 p-5">
                            <p class="text-sm leading-relaxed text-slate-600">
                                Cette capsule présente la caravane juridique organisée par la Clinique Juridique
                                FSJP-UAM dans la région de Tillabéri. L'objectif est de rapprocher le droit des
                                populations, d'informer sur leurs droits et de les accompagner dans leurs
                                démarches juridiques.
                            </p>

                            <p class="mt-5 text-sm font-semibold text-slate-900">Thématiques abordées :</p>
                            <div class="mt-3 space-y-2">
                                <x-check-item variant="filled">Accès à la justice pour les populations rurales</x-check-item>
                                <x-check-item variant="filled">Sensibilisation sur les droits de la famille, de la femme et de l'enfant</x-check-item>
                                <x-check-item variant="filled">Échanges avec les autorités locales et la société civile</x-check-item>
                                <x-check-item variant="filled">Témoignages des bénéficiaires</x-check-item>
                            </div>
                        </div>
                    </x-slot:description>

                    <x-slot:intervenants>
                        @php
                            $speakers = [
                                ['name' => 'Dr. Amadou Issoufou', 'role' => 'Coordonnateur de la Clinique Juridique', 'image' => 'placeholders/about-team'],
                                ['name' => 'Me Fatima Souley', 'role' => 'Encadrante, Droit de la famille', 'image' => 'placeholders/news-3'],
                                ['name' => 'Ibrahim Moussa', 'role' => 'Étudiant clinicien, Master 2', 'image' => 'placeholders/news-candidatures'],
                            ];
                        @endphp

                        <ul class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            @foreach ($speakers as $speaker)
                                <li class="flex items-center gap-3 rounded-lg border border-slate-200 p-4">
                                    <x-picture
                                        :path="$speaker['image']"
                                        alt=""
                                        width="80" height="80"
                                        class="h-12 w-12 shrink-0 rounded-full object-cover"
                                    />
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-900">{{ $speaker['name'] }}</p>
                                        <p class="mt-0.5 text-xs leading-snug text-slate-500">{{ $speaker['role'] }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </x-slot:intervenants>

                    <x-slot:chapitres>
                        @php
                            $chapters = [
                                ['time' => '00:00', 'title' => 'Introduction et contexte de la caravane'],
                                ['time' => '02:15', 'title' => 'Arrivée dans la région de Tillabéri'],
                                ['time' => '04:40', 'title' => 'Séance de sensibilisation collective'],
                                ['time' => '07:20', 'title' => 'Consultations juridiques individuelles'],
                                ['time' => '10:05', 'title' => 'Témoignages des bénéficiaires'],
                                ['time' => '11:30', 'title' => 'Bilan et perspectives'],
                            ];
                        @endphp

                        <ol class="space-y-1">
                            @foreach ($chapters as $chapter)
                                <li>
                                    <a href="#" class="group flex items-center gap-3 rounded-md px-3 py-2 hover:bg-slate-50">
                                        <span class="shrink-0 rounded bg-slate-100 px-2 py-0.5 font-mono text-xs text-slate-600 group-hover:bg-primary-50 group-hover:text-primary-700">
                                            {{ $chapter['time'] }}
                                        </span>
                                        <span class="text-sm text-slate-700 group-hover:text-primary-600">{{ $chapter['title'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                    </x-slot:chapitres>

                    <x-slot:documents>
                        @php
                            $documents = [
                                ['title' => 'Rapport de la caravane juridique de Tillabéri', 'size' => '1,8 Mo'],
                                ['title' => 'Fiche pratique : droits de la famille', 'size' => '2,1 Mo'],
                            ];
                        @endphp

                        <ul class="space-y-3">
                            @foreach ($documents as $document)
                                <li class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-200 p-4">
                                    <span class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-500">
                                            <x-icon name="document" class="h-5 w-5" />
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block text-sm font-semibold text-slate-900">{{ $document['title'] }}</span>
                                            <span class="block text-xs text-slate-500">PDF ({{ $document['size'] }})</span>
                                        </span>
                                    </span>

                                    <a href="#" class="inline-flex items-center gap-1.5 rounded-md border border-primary-200 bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary-700 hover:bg-primary-100">
                                        <x-icon name="download" class="h-4 w-4" />
                                        Télécharger
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </x-slot:documents>
                </x-tabs>

                {{-- ============ SHARE ============ --}}
                <div class="mt-8 flex flex-wrap items-center justify-between gap-4 rounded-lg border border-primary-100 bg-primary-50/60 p-5">
                    <x-share-buttons title="Caravane juridique à Tillabéri : accès au droit pour les populations" />

                    <x-button href="#" variant="outline" :arrow="false">
                        <x-icon name="download" class="h-4 w-4" />
                        Télécharger la vidéo
                    </x-button>
                </div>

                {{-- ============ COMMENTS ============ --}}
                {{-- Presentation only: submission is wired up in Module 2 with
                     validation, anti-spam and moderation before publication. --}}
                <div class="mt-8 rounded-lg border border-primary-100 bg-primary-50/60 p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                            <x-icon name="chat" class="h-5 w-5 text-primary-600" />
                            Commentaires (0)
                        </h2>

                        <div class="flex items-center gap-2">
                            <label for="comments-sort" class="text-sm text-slate-500">Trier par :</label>
                            <select
                                id="comments-sort"
                                class="rounded-md border border-slate-200 bg-white py-2 pl-3 pr-8 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                                <option>Plus récents</option>
                                <option>Plus anciens</option>
                            </select>
                        </div>
                    </div>

                    <form class="mt-4 flex flex-wrap items-start gap-3 rounded-lg border border-slate-200 bg-white p-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <x-icon name="user" class="h-5 w-5" />
                        </span>

                        <label for="comment-body" class="sr-only">Ajouter un commentaire</label>
                        <input
                            type="text"
                            id="comment-body"
                            placeholder="Ajouter un commentaire..."
                            class="min-w-0 flex-1 rounded-md border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >

                        <x-button variant="primary" :arrow="false" class="shrink-0">Publier</x-button>
                    </form>
                </div>

                {{-- ============ MORE VIDEOS ============ --}}
                <div class="mt-12">
                    <x-section-heading :link="route('webtv.index')" link-label="Voir toutes les vidéos">
                        Plus de vidéos
                    </x-section-heading>

                    @php
                        $more = [
                            ['image' => 'placeholders/about-team', 'duration' => '10:36', 'title' => 'Entretien avec le Doyen de la FSJP sur la Clinique Juridique', 'date' => '20 août 2024', 'views' => '2 100'],
                            ['image' => 'placeholders/news-3', 'duration' => '6:18', 'title' => "Les droits des femmes : paroles d'étudiantes", 'date' => '12 octobre 2024', 'views' => '980'],
                            ['image' => 'placeholders/news-formation', 'duration' => '15:42', 'title' => 'Session de formation sur la médiation', 'date' => '5 septembre 2024', 'views' => '1 420'],
                        ];
                    @endphp

                    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                        @foreach ($more as $video)
                            <x-video-card
                                :image="$video['image']"
                                :duration="$video['duration']"
                                :title="$video['title']"
                                :date="$video['date']"
                                :views="$video['views']"
                                :href="route('webtv.show')"
                            />
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">

                <x-sidebar-card icon="folder" title="Playlist : Caravanes juridiques" :tint="true">
                    @php
                        $playlist = [
                            ['image' => 'placeholders/news-caravan', 'title' => "Caravane juridique à Tillabéri : accès au droit...", 'duration' => '12:35', 'views' => '1 250', 'active' => true],
                            ['image' => 'placeholders/news-1', 'title' => 'Caravane juridique à Dosso : sensibilisation...', 'duration' => '10:20', 'views' => '980'],
                            ['image' => 'placeholders/news-3', 'title' => 'Caravane juridique à Maradi : droits des femmes', 'duration' => '8:15', 'views' => '1 420'],
                            ['image' => 'placeholders/news-2', 'title' => 'Caravane juridique à Zinder : justice de proximité', 'duration' => '11:08', 'views' => '950'],
                            ['image' => 'placeholders/news-partenariat', 'title' => "Caravane juridique à Agadez : accès à l'état civil", 'duration' => '9:45', 'views' => '870'],
                        ];
                    @endphp

                    <ul class="space-y-2">
                        @foreach ($playlist as $item)
                            <li>
                                <a
                                    href="{{ route('webtv.show') }}"
                                    @if($item['active'] ?? false) aria-current="true" @endif
                                    class="group flex items-start gap-3 rounded-md p-2 {{ ($item['active'] ?? false) ? 'bg-primary-50' : 'hover:bg-slate-50' }}"
                                >
                                    <span class="relative shrink-0">
                                        <x-picture
                                            :path="$item['image']"
                                            alt=""
                                            width="160" height="90"
                                            class="h-12 w-20 rounded object-cover"
                                        />
                                        <span class="absolute bottom-0.5 right-0.5 rounded bg-slate-900/80 px-1 text-[10px] font-medium text-white">
                                            {{ $item['duration'] }}
                                        </span>
                                    </span>

                                    <span class="min-w-0">
                                        <span class="block text-sm font-medium leading-snug text-slate-800 group-hover:text-primary-600">{{ $item['title'] }}</span>
                                        <span class="mt-0.5 block text-xs text-slate-500">{{ $item['views'] }} vues</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <a href="{{ route('webtv.index') }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700">
                        Voir la playlist
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </x-sidebar-card>

                <x-sidebar-card icon="play-circle" title="Autres vidéos similaires" :tint="true">
                    @php
                        $similar = [
                            ['image' => 'placeholders/news-3', 'title' => "Les droits des femmes : paroles d'étudiantes", 'duration' => '6:18', 'views' => '1 250'],
                            ['image' => 'placeholders/news-formation', 'title' => 'Session de formation sur la médiation', 'duration' => '15:42', 'views' => '1 420'],
                            ['image' => 'placeholders/news-2', 'title' => "Témoignages : l'impact des caravanes juridiques", 'duration' => '7:50', 'views' => '1 780'],
                        ];
                    @endphp

                    <ul class="space-y-3">
                        @foreach ($similar as $item)
                            <li>
                                <a href="{{ route('webtv.show') }}" class="group flex items-start gap-3">
                                    <span class="relative shrink-0">
                                        <x-picture
                                            :path="$item['image']"
                                            alt=""
                                            width="160" height="90"
                                            class="h-12 w-20 rounded object-cover"
                                        />
                                        <span class="absolute bottom-0.5 right-0.5 rounded bg-slate-900/80 px-1 text-[10px] font-medium text-white">
                                            {{ $item['duration'] }}
                                        </span>
                                    </span>

                                    <span class="min-w-0">
                                        <span class="block text-sm font-medium leading-snug text-slate-800 group-hover:text-primary-600">{{ $item['title'] }}</span>
                                        <span class="mt-0.5 block text-xs text-slate-500">{{ $item['views'] }} vues</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-sidebar-card>

                <x-sidebar-card icon="youtube" title="Notre chaîne YouTube" :tint="true">
                    <p class="text-sm leading-relaxed text-slate-600">
                        Suivez toutes nos vidéos sur notre chaîne officielle.
                    </p>
                    <x-button href="#" variant="outline" class="mt-4 w-full justify-center">
                        Voir la chaîne
                    </x-button>
                </x-sidebar-card>

                <x-help-card icon="chat" title="Vous avez une vidéo à proposer ?">
                    Partagez vos initiatives, témoignages ou événements pour une diffusion sur notre Web TV.
                </x-help-card>

            </aside>
        </div>
    </section>

@endsection
