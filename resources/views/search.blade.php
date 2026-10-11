@extends('layouts.app')

@section('title', 'Résultats de recherche - ' . config('app.name'))
@section('meta_description', "Trouvez rapidement des actualites, guides pratiques, videos et informations sur les services de la Clinique Juridique FSJP-UAM.")

@section('content')

    <x-page-hero
        image="placeholders/about-legal"
        title="Résultats de recherche"
        subtitle="Trouvez rapidement des actualités, guides pratiques, vidéos et informations sur nos services."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Recherche'],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        @php
            // Echoed back into the search field and the result heading.
            // Module 2 replaces this with the real request value.
            $query = 'droit des femmes';
            $resultCount = 25;
        @endphp

        {{-- ============ SEARCH BAR ============ --}}
        <form role="search" method="get" action="{{ route('search') }}" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <label for="search-q" class="sr-only">Rechercher sur le site</label>

                <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <x-icon name="search" class="h-5 w-5" />
                </span>

                <input
                    type="search"
                    id="search-q"
                    name="q"
                    value="{{ $query }}"
                    placeholder="Rechercher une actualité, une fiche pratique, une vidéo..."
                    class="w-full rounded-lg border border-slate-200 py-3 pl-12 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                >
            </div>

            <x-button variant="primary" :arrow="false" class="shrink-0 justify-center !px-8 !py-3">
                Rechercher
            </x-button>
        </form>

        {{--
            Three-column layout: filters, results, contextual sidebar.
            Both side columns collapse below the results on narrow screens.
        --}}
        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-4 xl:grid-cols-5">

            {{-- ============ FILTERS ============ --}}
            <aside class="lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-xl border border-slate-200 p-5">
                    <h2 class="flex items-center gap-2 text-base font-bold text-slate-900">
                        <x-icon name="folder" class="h-5 w-5 text-primary-600" />
                        Filtrer les résultats
                    </h2>

                    <form method="get" action="{{ route('search') }}" class="mt-5 space-y-6">
                        <input type="hidden" name="q" value="{{ $query }}">

                        <fieldset>
                            <legend class="text-sm font-semibold text-slate-700">Type de contenu</legend>

                            @php
                                $types = [
                                    ['value' => 'tous', 'label' => 'Tous les résultats', 'count' => 25, 'checked' => true],
                                    ['value' => 'actualites', 'label' => 'Actualités', 'count' => 8],
                                    ['value' => 'fiches', 'label' => 'Fiches pratiques', 'count' => 7],
                                    ['value' => 'webtv', 'label' => 'Web TV', 'count' => 5],
                                    ['value' => 'pages', 'label' => 'Pages', 'count' => 5],
                                ];
                            @endphp

                            <div class="mt-3 space-y-2.5">
                                @foreach ($types as $type)
                                    <div class="flex items-center gap-2.5">
                                        <input
                                            type="checkbox"
                                            id="type-{{ $type['value'] }}"
                                            name="type[]"
                                            value="{{ $type['value'] }}"
                                            @checked($type['checked'] ?? false)
                                            class="h-4 w-4 shrink-0 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                        <label for="type-{{ $type['value'] }}" class="min-w-0 flex-1 truncate text-sm text-slate-600">
                                            {{ $type['label'] }}
                                        </label>
                                        <span class="shrink-0 text-xs text-slate-400">{{ $type['count'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>

                        @php
                            $searchCategories = [
                                '' => 'Toutes les catégories',
                                'famille' => 'Droit de la famille',
                                'civil' => 'Droit civil',
                                'penal' => 'Droit pénal',
                                'humains' => 'Droits humains',
                                'administratif' => 'Droit administratif',
                            ];
                        @endphp

                        <x-form-field
                            name="categorie"
                            type="select"
                            label="Catégorie"
                            :options="$searchCategories"
                        />

                        <fieldset>
                            <legend class="text-sm font-semibold text-slate-700">Date de publication</legend>

                            <div class="mt-3 grid grid-cols-2 gap-3">
                                <div>
                                    <label for="date-du" class="sr-only">Date de début</label>
                                    <input
                                        type="date"
                                        id="date-du"
                                        name="du"
                                        placeholder="Du"
                                        class="w-full rounded-md border border-slate-200 px-3 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                    >
                                </div>
                                <div>
                                    <label for="date-au" class="sr-only">Date de fin</label>
                                    <input
                                        type="date"
                                        id="date-au"
                                        name="au"
                                        placeholder="Au"
                                        class="w-full rounded-md border border-slate-200 px-3 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                    >
                                </div>
                            </div>
                        </fieldset>

                        <div class="space-y-2.5">
                            <x-button variant="primary" :arrow="false" class="w-full justify-center">
                                <x-icon name="folder" class="h-4 w-4" />
                                Appliquer les filtres
                            </x-button>

                            <a
                                href="{{ route('search') }}"
                                class="flex w-full items-center justify-center gap-2 rounded-md bg-slate-50 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-100"
                            >
                                <x-icon name="arrow-up" class="h-4 w-4" />
                                Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </aside>

            {{-- ============ RESULTS ============ --}}
            <div class="lg:col-span-2 xl:col-span-3">

                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 pb-4">
                    <p class="text-lg text-slate-700">
                        <strong class="font-bold text-primary-600">{{ $resultCount }} résultats</strong>
                        pour « {{ $query }} »
                    </p>

                    <div class="flex items-center gap-2">
                        <label for="search-sort" class="text-sm text-slate-500">Trier par :</label>
                        <select
                            id="search-sort"
                            class="rounded-md border border-slate-200 py-2 pl-3 pr-8 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option>Pertinence</option>
                            <option>Plus récents</option>
                            <option>Plus consultés</option>
                        </select>
                    </div>
                </div>

                @php
                    $results = [
                        [
                            'image' => 'placeholders/news-3',
                            'type' => 'Actualité',
                            'title' => "Journée internationale des droits des femmes : la Clinique Juridique FSJP-UAM se mobilise",
                            'excerpt' => "À l'occasion de la Journée internationale des droits des femmes, la Clinique Juridique organise une série d'activités de sensibilisation sur l'accès à la justice...",
                            'date' => '8 mars 2025',
                            'views' => '256',
                            'href' => route('news.show'),
                        ],
                        [
                            'image' => 'placeholders/news-guide',
                            'type' => 'Fiche pratique',
                            'title' => 'Guide pratique : Droits des femmes en milieu universitaire',
                            'excerpt' => "Ce guide présente de manière simple et pratique les droits des femmes dans le milieu universitaire, les mécanismes de protection et les recours disponibles.",
                            'date' => '12 février 2025',
                            'views' => '420',
                            'href' => route('resources.guides.show'),
                        ],
                        [
                            'image' => 'placeholders/news-2',
                            'type' => 'Web TV',
                            'title' => 'Les droits des femmes : entretien avec une experte juridique',
                            'excerpt' => "Dans cette capsule, notre invitée aborde les principaux droits des femmes et les défis liés à leur mise en œuvre au Niger.",
                            'date' => '15 janvier 2025',
                            'views' => '1,2K',
                            'href' => route('webtv.show'),
                            'video' => true,
                        ],
                        [
                            'image' => 'placeholders/news-caravan',
                            'type' => 'Activité',
                            'title' => 'Caravane juridique : sensibilisation sur les droits des femmes',
                            'excerpt' => "La Clinique Juridique FSJP-UAM a organisé une caravane juridique à Zinder pour sensibiliser les femmes sur leurs droits et les recours en cas de discrimination.",
                            'date' => '28 novembre 2024',
                            'views' => '312',
                            'href' => route('activities.index'),
                        ],
                        [
                            'image' => 'placeholders/about-legal',
                            'type' => 'Page',
                            'title' => 'Nos services juridiques',
                            'excerpt' => "La Clinique Juridique offre des services d'information, d'orientation et d'assistance juridique, notamment en matière de droits des femmes...",
                            'href' => route('services'),
                        ],
                    ];
                @endphp

                <div>
                    @foreach ($results as $result)
                        <x-search-result
                            :image="$result['image']"
                            :type="$result['type']"
                            :title="$result['title']"
                            :excerpt="$result['excerpt']"
                            :date="$result['date'] ?? null"
                            :views="$result['views'] ?? null"
                            :href="$result['href']"
                            :video="$result['video'] ?? false"
                        />
                    @endforeach
                </div>

                <x-pagination :current="1" :total="4" :edges="true" />
            </div>

            {{-- ============ CONTEXT SIDEBAR ============ --}}
            <aside class="space-y-6 lg:col-span-4 xl:col-span-1 xl:sticky xl:top-24 xl:self-start">

                <x-sidebar-card icon="clock" title="Recherches récentes">
                    @php
                        $recent = ['droit des femmes', 'accès à la justice', 'violences basées sur le genre', 'caravane juridique', 'bourse universitaire'];
                    @endphp

                    <ul class="space-y-1">
                        @foreach ($recent as $term)
                            <li class="flex items-center gap-2.5 rounded-md px-2 py-1.5 hover:bg-slate-50">
                                <x-icon name="search" class="h-4 w-4 shrink-0 text-slate-400" />
                                <a href="{{ route('search') }}?q={{ urlencode($term) }}" class="min-w-0 flex-1 truncate text-sm text-slate-600 hover:text-primary-600">
                                    {{ $term }}
                                </a>
                                <button type="button" aria-label="Retirer « {{ $term }} » de l'historique" class="shrink-0 text-slate-300 hover:text-rose-500">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <button type="button" class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700">
                        <x-icon name="info" class="h-4 w-4" />
                        Effacer l'historique
                    </button>
                </x-sidebar-card>

                <x-sidebar-card icon="folder" title="Catégories populaires">
                    @php
                        $popular = [
                            ['label' => 'Droits des femmes', 'count' => 8, 'color' => 'bg-primary-50 text-primary-700'],
                            ['label' => 'Accès à la justice', 'count' => 6, 'color' => 'bg-emerald-50 text-emerald-700'],
                            ['label' => 'Étudiants', 'count' => 5, 'color' => 'bg-amber-50 text-amber-700'],
                            ['label' => 'Protection des mineurs', 'count' => 4, 'color' => 'bg-violet-50 text-violet-700'],
                            ['label' => 'Droit administratif', 'count' => 4, 'color' => 'bg-rose-50 text-rose-700'],
                        ];
                    @endphp

                    <ul class="space-y-1">
                        @foreach ($popular as $category)
                            <li>
                                <a href="{{ route('search') }}" class="group flex items-center gap-2.5 rounded-md px-2 py-2 hover:bg-slate-50">
                                    <x-icon name="folder" class="h-4 w-4 shrink-0 text-slate-400" />
                                    <span class="min-w-0 flex-1 truncate text-sm text-slate-600 group-hover:text-primary-600">{{ $category['label'] }}</span>
                                    <span class="shrink-0 rounded px-1.5 py-0.5 text-xs font-medium {{ $category['color'] }}">{{ $category['count'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-sidebar-card>

                <x-help-card title="Besoin d'aide ?">
                    Vous ne trouvez pas l'information que vous cherchez ?
                </x-help-card>

            </aside>
        </div>
    </section>

@endsection
