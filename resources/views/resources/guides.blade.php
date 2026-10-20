@extends('layouts.app')

@section('title', 'Fiches pratiques juridiques - ' . config('app.name'))
@section('meta_description', "Des guides simples et clairs pour comprendre vos droits : famille, civil, penal, foncier, travail et droits humains. Fiches pratiques telechargeables en PDF.")

@section('content')

    <x-page-hero
        image="placeholders/legal-scales-gavel"
        title="Fiches pratiques juridiques"
        subtitle="Des guides simples et clairs pour comprendre vos droits et vous accompagner dans vos démarches."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Ressources', 'url' => route('resources')],
            ['label' => 'Fiches pratiques juridiques'],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        {{-- ============ DOMAIN FILTERS ============ --}}
        {{-- Static pills until Module 2 wires them to real query filters. --}}
        <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-wrap lg:overflow-visible lg:pb-0">
            <x-filter-pill :active="true">Toutes les fiches</x-filter-pill>
            <x-filter-pill>Droit de la famille</x-filter-pill>
            <x-filter-pill>Droit civil</x-filter-pill>
            <x-filter-pill>Droit pénal</x-filter-pill>
            <x-filter-pill>Droits humains</x-filter-pill>
            <x-filter-pill>Droit administratif</x-filter-pill>
            <x-filter-pill>Droit du travail</x-filter-pill>
            <x-filter-pill>Autres</x-filter-pill>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-3">

            {{-- ============ GUIDE LIST ============ --}}
            <div class="lg:col-span-2">

                <div class="flex flex-wrap items-center justify-between gap-4">
                    <x-section-label>Toutes les fiches pratiques (24)</x-section-label>

                    <form class="flex items-center gap-2">
                        <label for="guides-sort" class="text-sm text-slate-500">Trier par :</label>
                        <select
                            id="guides-sort"
                            name="tri"
                            class="rounded-md border border-slate-200 py-2 pl-3 pr-8 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option value="recentes">Plus récentes</option>
                            <option value="consultees">Plus consultées</option>
                            <option value="alphabetique">Ordre alphabétique</option>
                        </select>
                    </form>
                </div>

                @php
                    /*
                     * Fiches shown on page 1. Module 2 replaces this with a
                     * paginated query; `file` points at the stored PDF.
                     */
                    $guides = [
                        [
                            'image' => 'placeholders/guide-family',
                            'category' => 'Droit de la famille',
                            'color' => 'primary',
                            'title' => 'Guide pratique : droits de la famille',
                            'excerpt' => "Un guide pour comprendre les droits et obligations au sein de la famille (mariage, divorce, filiation, etc.).",
                            'size' => '2,1 Mo',
                        ],
                        [
                            'image' => 'placeholders/guide-civil',
                            'category' => 'Droit civil',
                            'color' => 'green',
                            'title' => "La procédure d'état civil au Niger",
                            'excerpt' => "Les étapes pour obtenir un acte de naissance, de mariage ou de décès.",
                            'size' => '1,8 Mo',
                        ],
                        [
                            'image' => 'placeholders/guide-penal',
                            'category' => 'Droit pénal',
                            'color' => 'red',
                            'title' => 'Comprendre la garde à vue',
                            'excerpt' => "Vos droits et les démarches à suivre en cas de garde à vue.",
                            'size' => '1,4 Mo',
                        ],
                        [
                            'image' => 'placeholders/guide-succession-event',
                            'category' => 'Droit foncier',
                            'color' => 'amber',
                            'title' => 'Accès à la propriété foncière',
                            'excerpt' => "Les démarches administratives pour l'obtention d'un titre foncier.",
                            'size' => '1,9 Mo',
                        ],
                        [
                            'image' => 'placeholders/guide-labor',
                            'category' => 'Droit du travail',
                            'color' => 'purple',
                            'title' => 'Le contrat de travail',
                            'excerpt' => "Tout ce qu'il faut savoir sur les types de contrats, les droits et obligations du travailleur.",
                            'size' => '1,6 Mo',
                        ],
                        [
                            'image' => 'placeholders/guide-human-rights',
                            'category' => 'Droits humains',
                            'color' => 'primary',
                            'title' => 'Protection des personnes vulnérables',
                            'excerpt' => "Les droits des femmes, enfants, personnes handicapées et personnes déplacées.",
                            'size' => '2,3 Mo',
                        ],
                        [
                            'image' => 'placeholders/guide-succession',
                            'category' => 'Droit des successions',
                            'color' => 'red',
                            'title' => 'La succession : démarches et droits des héritiers',
                            'excerpt' => "Un guide pratique sur la transmission du patrimoine.",
                            'size' => '1,7 Mo',
                        ],
                        [
                            'image' => 'placeholders/guide-admin',
                            'category' => 'Droit administratif',
                            'color' => 'green',
                            'title' => 'Recours contre une décision administrative',
                            'excerpt' => "Comment contester une décision de l'administration auprès des autorités compétentes.",
                            'size' => '1,5 Mo',
                        ],
                        [
                            'image' => 'placeholders/guide-practical',
                            'category' => 'Vie pratique',
                            'color' => 'amber',
                            'title' => 'Modèles de courriers juridiques',
                            'excerpt' => "Des exemples de courriers pour vos démarches administratives et judiciaires.",
                            'size' => '1,2 Mo',
                        ],
                    ];
                @endphp

                <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($guides as $guide)
                        <x-guide-card
                            :image="$guide['image']"
                            :category="$guide['category']"
                            :category-color="$guide['color']"
                            :title="$guide['title']"
                            :excerpt="$guide['excerpt']"
                            :filesize="$guide['size']"
                            :href="route('resources.guides.show')"
                        />
                    @endforeach
                </div>

                <x-pagination :current="1" :total="4" />
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">

                <form role="search" class="relative">
                    <label for="guides-search" class="sr-only">Rechercher une fiche pratique</label>
                    <input
                        type="search"
                        id="guides-search"
                        name="q"
                        placeholder="Rechercher une fiche..."
                        class="w-full rounded-md border border-slate-200 py-2.5 pl-4 pr-10 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    >
                    <button type="submit" aria-label="Rechercher" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary-600">
                        <x-icon name="search" class="h-4 w-4" />
                    </button>
                </form>

                <x-sidebar-card icon="folder" title="Catégories" :tint="true">
                    @php
                        $categories = [
                            ['label' => 'Toutes les fiches', 'count' => 24, 'icon' => 'folder', 'color' => 'amber', 'active' => true],
                            ['label' => 'Droit de la famille', 'count' => 6, 'icon' => 'users', 'color' => 'blue'],
                            ['label' => 'Droit civil', 'count' => 4, 'icon' => 'scale', 'color' => 'green'],
                            ['label' => 'Droit pénal', 'count' => 4, 'icon' => 'gavel', 'color' => 'pink'],
                            ['label' => 'Droits humains', 'count' => 4, 'icon' => 'heart', 'color' => 'purple'],
                            ['label' => 'Droit administratif', 'count' => 3, 'icon' => 'building', 'color' => 'green'],
                            ['label' => 'Droit du travail', 'count' => 3, 'icon' => 'handshake', 'color' => 'purple'],
                            ['label' => 'Droit foncier', 'count' => 2, 'icon' => 'map-pin', 'color' => 'amber'],
                            ['label' => 'Droit des successions', 'count' => 2, 'icon' => 'document', 'color' => 'red'],
                            ['label' => 'Vie pratique', 'count' => 2, 'icon' => 'info', 'color' => 'blue'],
                        ];
                    @endphp

                    <nav class="space-y-1">
                        @foreach ($categories as $category)
                            <x-category-list-item
                                :active="$category['active'] ?? false"
                                :count="$category['count']"
                                :icon="$category['icon']"
                                :color="$category['color']"
                            >
                                {{ $category['label'] }}
                            </x-category-list-item>
                        @endforeach
                    </nav>
                </x-sidebar-card>

                <x-sidebar-card icon="heart" title="Fiches les plus consultées" :tint="true">
                    @php
                        $popular = [
                            ['image' => 'placeholders/guide-family', 'title' => 'Guide pratique : droits de la famille', 'downloads' => '2 450'],
                            ['image' => 'placeholders/guide-penal', 'title' => 'Comprendre la garde à vue', 'downloads' => '1 980'],
                            ['image' => 'placeholders/guide-civil', 'title' => "La procédure d'état civil au Niger", 'downloads' => '1 750'],
                            ['image' => 'placeholders/guide-labor', 'title' => 'Le contrat de travail', 'downloads' => '1 420'],
                            ['image' => 'placeholders/guide-succession-event', 'title' => 'Accès à la propriété foncière', 'downloads' => '1 280'],
                        ];
                    @endphp

                    <ul class="space-y-4">
                        @foreach ($popular as $item)
                            <li>
                                <a href="{{ route('resources.guides.show') }}" class="group flex items-start gap-3">
                                    <x-picture
                                        :path="$item['image']"
                                        alt=""
                                        width="80" height="80"
                                        class="h-12 w-12 shrink-0 rounded-md object-cover"
                                    />
                                    <span class="min-w-0">
                                        <span class="block text-sm font-medium leading-snug text-slate-800 group-hover:text-primary-600">{{ $item['title'] }}</span>
                                        <span class="mt-1 flex items-center gap-1 text-xs text-slate-400">
                                            <x-icon name="download" class="h-3 w-3 shrink-0" />
                                            {{ $item['downloads'] }} téléchargements
                                        </span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-sidebar-card>

                <x-help-card icon="document" title="Une question sur une fiche ?">
                    Notre équipe est à votre disposition pour vous orienter et vous fournir des informations complémentaires.
                </x-help-card>

            </aside>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            title="Vous ne trouvez pas la fiche dont vous avez besoin ?"
            button-label="Faire une pré-saisine"
            :button-href="route('presaisine.index')"
        >
            Contactez-nous pour obtenir un accompagnement personnalisé ou une documentation spécifique.
        </x-inline-cta>
    </section>

@endsection
