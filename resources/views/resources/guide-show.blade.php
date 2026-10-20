@extends('layouts.app')

@section('title', 'Guide pratique : droits de la famille - ' . config('app.name'))
@section('meta_description', "Un guide simple pour comprendre vos droits et obligations au sein de la famille au Niger : mariage, divorce, filiation et protection des enfants.")

@section('content')

    <x-page-hero
        image="placeholders/news-guide"
        badge="Droit de la famille"
        title="Guide pratique : droits de la famille"
        subtitle="Un guide simple pour comprendre vos droits et obligations au sein de la famille (mariage, divorce, filiation, etc.)."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Ressources', 'url' => route('resources')],
            ['label' => 'Fiches pratiques juridiques', 'url' => route('resources.guides')],
            ['label' => 'Guide pratique : droits de la famille'],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">

            {{-- ============ ARTICLE ============ --}}
            <article class="lg:col-span-2">

                {{-- Meta bar: publication data on the left, sharing on the right --}}
                <div class="flex flex-col gap-4 border-b border-slate-100 pb-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <x-icon name="calendar" class="h-4 w-4 shrink-0" />
                            12 septembre 2024
                        </span>
                        <span class="flex items-center gap-1.5">
                            <x-icon name="users" class="h-4 w-4 shrink-0" />
                            Équipe de la Clinique
                        </span>
                        <span class="flex items-center gap-1.5">
                            <x-icon name="eye" class="h-4 w-4 shrink-0" />
                            2 450 vues
                        </span>
                    </div>

                    <x-share-buttons title="Guide pratique : droits de la famille" />
                </div>

                {{-- ============ INTRO ============ --}}
                <div class="mt-8">
                    <x-info-note-card icon="book">
                        Cette fiche pratique vous présente, de manière claire et accessible, les principaux droits
                        et devoirs au sein de la famille au Niger. Elle vise à informer et à accompagner les citoyens
                        dans leurs démarches juridiques liées au mariage, au divorce, à la filiation et à la
                        protection des enfants.
                    </x-info-note-card>
                </div>

                {{-- ============ NUMBERED SECTIONS ============ --}}
                @php
                    /*
                     * Each section pairs explanatory text with a supporting
                     * image and a key-points callout. Module 2 renders these
                     * from structured guide content.
                     */
                    $sections = [
                        [
                            'number' => 1,
                            'title' => 'Le mariage',
                            'body' => "Le mariage est une union légale entre deux personnes, reconnue par la loi. Il crée des droits et des obligations pour les époux, notamment en matière de vie commune, de fidélité, d'assistance et de contribution aux charges du ménage.",
                            'image' => 'placeholders/signing-document',
                            'alt' => "Alliances posées sur un acte de mariage",
                            'callout' => [
                                'icon' => 'gavel',
                                'title' => 'À retenir',
                                'points' => [
                                    "Le mariage doit être célébré devant l'officier d'état civil.",
                                    "L'âge légal du mariage est de 18 ans pour les deux sexes.",
                                    'Le consentement des deux époux est obligatoire.',
                                ],
                            ],
                        ],
                        [
                            'number' => 2,
                            'title' => 'Le divorce',
                            'body' => "Le divorce met fin au lien matrimonial. Il peut être demandé par l'un des époux pour des motifs prévus par la loi (désaccords, violences, abandon, etc.).",
                            'image' => 'placeholders/about-legal',
                            'alt' => "Marteau de juge et alliances",
                            'callout' => [
                                'icon' => 'scale',
                                'title' => 'Points essentiels',
                                'points' => [
                                    'Le divorce est prononcé par le tribunal.',
                                    "Il entraîne des conséquences sur la garde des enfants et la répartition des biens.",
                                    'Une tentative de conciliation est souvent recommandée.',
                                ],
                            ],
                        ],
                        [
                            'number' => 3,
                            'title' => 'La filiation et les droits des enfants',
                            'body' => "Tout enfant a le droit à une identité, à une protection et à une éducation. La filiation peut être établie par la naissance, la reconnaissance ou l'adoption.",
                            'image' => 'placeholders/news-guide',
                            'alt' => "Silhouettes représentant une famille",
                            'callout' => [
                                'icon' => 'users',
                                'title' => 'Points clés',
                                'points' => [
                                    "Chaque enfant doit être déclaré à l'état civil.",
                                    "Les parents ont l'obligation d'assurer l'entretien, l'éducation et la santé de leurs enfants.",
                                    "L'enfant a le droit de connaître ses origines.",
                                ],
                            ],
                        ],
                    ];
                @endphp

                @foreach ($sections as $section)
                    <section class="mt-10">
                        <x-section-label>{{ $section['number'] }}. {{ $section['title'] }}</x-section-label>

                        <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-5">
                            <div class="sm:col-span-3">
                                <p class="text-sm leading-relaxed text-slate-600">{{ $section['body'] }}</p>

                                <x-key-points
                                    class="mt-4"
                                    :icon="$section['callout']['icon']"
                                    :title="$section['callout']['title']"
                                    :points="$section['callout']['points']"
                                />
                            </div>

                            <div class="sm:col-span-2">
                                <x-picture
                                    :path="$section['image']"
                                    :alt="$section['alt']"
                                    width="600" height="600"
                                    class="aspect-square w-full rounded-lg object-cover"
                                />
                            </div>
                        </div>
                    </section>
                @endforeach

                {{-- ============ CONSEILS PRATIQUES ============ --}}
                <section class="mt-10">
                    <x-section-label>4. Conseils pratiques</x-section-label>

                    @php
                        $tips = [
                            ['icon' => 'document', 'color' => 'bg-primary-100 text-primary-600', 'text' => "Conservez toujours vos documents officiels (actes de naissance, de mariage, etc.)."],
                            ['icon' => 'chat', 'color' => 'bg-violet-100 text-violet-600', 'text' => "En cas de conflit, privilégiez d'abord le dialogue ou la médiation."],
                            ['icon' => 'shield', 'color' => 'bg-emerald-100 text-emerald-600', 'text' => "N'hésitez pas à vous rapprocher de la Clinique Juridique pour un accompagnement gratuit."],
                        ];
                    @endphp

                    <ul class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        @foreach ($tips as $tip)
                            <li class="flex items-start gap-3 rounded-lg bg-slate-50 p-5">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $tip['color'] }}">
                                    <x-icon :name="$tip['icon']" class="h-5 w-5" />
                                </span>
                                <p class="text-sm leading-relaxed text-slate-600">{{ $tip['text'] }}</p>
                            </li>
                        @endforeach
                    </ul>
                </section>

                {{-- ============ DOWNLOAD ============ --}}
                <div class="mt-10 flex flex-col gap-4 rounded-lg bg-primary-50/60 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-white text-rose-500">
                            <x-icon name="document" class="h-6 w-6" />
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900">Télécharger la fiche complète</p>
                            <p class="mt-0.5 text-sm text-slate-600">Guide pratique : droits de la famille (PDF, 2,1 Mo)</p>
                        </div>
                    </div>

                    <x-button href="#" variant="outline" :arrow="false" class="shrink-0">
                        <x-icon name="download" class="h-4 w-4" />
                        Télécharger
                    </x-button>
                </div>
            </article>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">

                <x-sidebar-card icon="folder" title="Catégories de fiches">
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
                                :href="route('resources.guides')"
                            >
                                {{ $category['label'] }}
                            </x-category-list-item>
                        @endforeach
                    </nav>
                </x-sidebar-card>

                <x-sidebar-card icon="document" title="Fiches similaires">
                    @php
                        $similar = [
                            ['image' => 'placeholders/news-1', 'title' => 'Le divorce : procédures et conséquences', 'category' => 'Droit de la famille'],
                            ['image' => 'placeholders/guide-civil', 'title' => "La procédure d'état civil au Niger", 'category' => 'Droit civil'],
                            ['image' => 'placeholders/news-3', 'title' => 'Protection des enfants et personnes vulnérables', 'category' => 'Droits humains'],
                            ['image' => 'placeholders/article-highlight-1', 'title' => 'La succession : démarches et droits des héritiers', 'category' => 'Droit des successions'],
                        ];
                    @endphp

                    <ul class="space-y-4">
                        @foreach ($similar as $item)
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
                                            <x-icon name="folder" class="h-3 w-3 shrink-0" />
                                            {{ $item['category'] }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-sidebar-card>

                <x-help-card title="Besoin d'un accompagnement ?">
                    Notre équipe est à votre écoute pour vous informer et vous orienter dans vos démarches juridiques.
                </x-help-card>

            </aside>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            title="Vous avez une question juridique ?"
            button-label="Faire une pré-saisine"
            :button-href="route('presaisine.index')"
        >
            Utilisez notre service de pré-saisine pour décrire votre situation et recevoir une orientation.
        </x-inline-cta>
    </section>

@endsection
