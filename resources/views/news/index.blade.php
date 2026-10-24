@extends('layouts.app')

@section('title', 'Actualités - ' . config('app.name'))
@section('meta_description', "Suivez les dernieres actualites, evenements et initiatives de la Clinique Juridique FSJP-UAM en faveur de l'acces au droit.")

@section('content')

    <x-page-hero
        image="placeholders/about-hero"
        title="Actualités"
        subtitle="Suivez nos dernières actualités, événements et initiatives en faveur de l'accès au droit."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Actualités'],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[2fr_1fr] lg:gap-8">
            {{-- ============ FILTER BAR ============ --}}
            <div class="scrollbar-hide flex min-w-0 gap-1.5 overflow-x-auto">
                <x-filter-pill :active="true">Toutes les actualités</x-filter-pill>
                <x-filter-pill>Événements</x-filter-pill>
                <x-filter-pill>Formations</x-filter-pill>
                <x-filter-pill>Caravanes juridiques</x-filter-pill>
                <x-filter-pill>Communiqués</x-filter-pill>
                <x-filter-pill>Publications</x-filter-pill>
            </div>

            <form role="search" class="relative self-start">
                <label for="news-search" class="sr-only">Rechercher une actualité</label>
                <input
                    type="search"
                    id="news-search"
                    placeholder="Rechercher une actualité..."
                    class="w-full rounded-md border border-slate-200 py-2.5 pl-4 pr-10 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                >
                <button type="submit" aria-label="Rechercher" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary-600">
                    <x-icon name="search" class="h-4 w-4" />
                </button>
            </form>
        </div>

        <div class="mt-6 grid grid-cols-1 items-start gap-8 lg:grid-cols-[2fr_1fr]">

            {{-- ============ NEWS GRID ============ --}}
            <div class="min-w-0">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    @forelse ($articles as $article)
                        <x-news-card
                            :image-url="$article->imageUrl()"
                            :category="$article->category?->name"
                            :category-color="$article->category?->tone ?? 'primary'"
                            :date="$article->published_at?->translatedFormat('j F Y')"
                            :title="$article->title"
                            :excerpt="$article->excerpt"
                            :href="route('news.show', $article)"
                        />
                    @empty
                        <p class="sm:col-span-2 rounded-xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500">
                            Aucune actualité ne correspond à votre recherche.
                        </p>
                    @endforelse
                </div>

                <x-pagination :paginator="$articles" class="!mt-6" />
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6">
                <x-sidebar-card icon="folder" title="Catégories" color="amber" :tint="true">
                    <nav class="space-y-1">
                        <x-category-list-item :active="true" icon="folder" color="amber" count="24">Toutes les actualités</x-category-list-item>
                        <x-category-list-item icon="calendar" color="amber" count="6">Événements</x-category-list-item>
                        <x-category-list-item icon="graduation-cap" color="amber" count="5">Formations</x-category-list-item>
                        <x-category-list-item icon="map-pin" color="amber" count="4">Caravanes juridiques</x-category-list-item>
                        <x-category-list-item icon="megaphone" color="amber" count="4">Communiqués</x-category-list-item>
                        <x-category-list-item icon="document" color="amber" count="5">Publications</x-category-list-item>
                    </nav>
                </x-sidebar-card>

                {{--
                    Archives par année — required by the Phase 2 plan:
                    "actualités avec catégories et archives par année".
                    Module 2 replaces the array with a grouped query.
                --}}
                <x-sidebar-card icon="calendar" title="Archives" color="amber" :tint="true">
                    <nav class="space-y-1">
                        @forelse ($archives as $archive)
                            <x-category-list-item
                                icon="calendar"
                                color="amber"
                                :count="$archive->total"
                                :active="(int) ($filters['annee'] ?? 0) === (int) $archive->year"
                                :href="route('news.index', ['annee' => $archive->year])"
                            >
                                {{ $archive->year }}
                            </x-category-list-item>
                        @empty
                            <p class="px-3 py-2 text-sm text-slate-500">Aucune archive pour le moment.</p>
                        @endforelse
                    </nav>
                </x-sidebar-card>

                <x-sidebar-card icon="clock" title="Actualités récentes" color="amber" :tint="true">
                    <div class="space-y-5">
                        <x-mini-article-row
                            image="placeholders/news-1"
                            title="Conférence sur l'accès au droit des personnes vulnérables"
                            date="12 octobre 2024"
                        />
                        <x-mini-article-row
                            image="placeholders/news-caravan"
                            title="Caravane juridique à Tillabéri"
                            date="28 septembre 2024"
                        />
                        <x-mini-article-row
                            image="placeholders/news-guide"
                            title="Nouveau guide pratique : droits de la famille"
                            date="15 septembre 2024"
                        />
                        <x-mini-article-row
                            image="placeholders/news-formation"
                            title="Formation des étudiants sur la médiation"
                            date="5 septembre 2024"
                        />
                        <x-mini-article-row
                            image="placeholders/news-candidatures"
                            title="Ouverture des candidatures pour la nouvelle promotion"
                            date="20 août 2024"
                        />
                    </div>
                </x-sidebar-card>

                <div class="rounded-xl bg-primary-50/60 p-6">
                    <div class="flex items-center justify-between">
                        <x-icon-badge size="sm" class="!bg-amber-50 !text-amber-500"><x-icon name="mail" class="h-5 w-5" /></x-icon-badge>
                        <x-icon name="paper-plane" class="h-6 w-6 text-primary-300" />
                    </div>
                    <h3 class="mt-3 text-base font-bold text-slate-900">Restez informé</h3>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600">
                        Abonnez-vous à notre newsletter pour recevoir nos dernières actualités et événements.
                    </p>
                    <form class="mt-4 space-y-2.5">
                        <label for="newsletter-email" class="sr-only">Votre adresse e-mail</label>
                        <input
                            type="email"
                            id="newsletter-email"
                            placeholder="Votre adresse e-mail"
                            class="w-full rounded-md border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                        <x-button variant="primary" class="w-full justify-center">S'abonner</x-button>
                    </form>
                </div>
            </aside>
        </div>
    </section>

    {{-- ============ CTA (dark) ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="scale"
            title="Une question juridique ?"
            button-label="Faire une pré-saisine"
            :button-href="route('presaisine.index')"
        >
            Nos étudiants encadrés sont à votre écoute pour vous informer et vous orienter dans vos démarches.
        </x-inline-cta>
    </section>

@endsection
