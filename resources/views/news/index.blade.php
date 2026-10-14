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

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[2fr_1fr] lg:gap-10">
            {{-- ============ FILTER BAR ============ --}}
            <div class="flex min-w-0 gap-2 overflow-x-auto pb-1 lg:flex-wrap lg:overflow-visible lg:pb-0">
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

        <div class="mt-4 grid grid-cols-1 gap-10 lg:grid-cols-[2fr_1fr]">

            {{-- ============ NEWS GRID ============ --}}
            <div class="min-w-0">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <x-news-card
                        image="placeholders/news-1"
                        category="Événement"
                        category-color="primary"
                        date="12 octobre 2024"
                        title="Conférence sur l'accès au droit des personnes vulnérables"
                        excerpt="La Clinique Juridique FSJP-UAM a organisé une conférence-débat sur l'accès au droit des personnes en situation de vulnérabilité, avec la participation d'experts et d'étudiants."
                        :href="route('news.show')"
                    />
                    <x-news-card
                        image="placeholders/news-caravan"
                        category="Caravane juridique"
                        category-color="amber"
                        date="28 septembre 2024"
                        title="Caravane juridique à Tillabéri : une forte mobilisation"
                        excerpt="La Clinique Juridique FSJP-UAM a mené une caravane d'information juridique dans la région de Tillabéri pour sensibiliser les populations sur leurs droits."
                    />
                    <x-news-card
                        image="placeholders/news-guide"
                        category="Publication"
                        category-color="green"
                        date="15 septembre 2024"
                        title="Nouveau guide pratique : droits de la famille"
                        excerpt="La Clinique Juridique FSJP-UAM publie un guide pratique pour mieux comprendre les droits de la famille au Niger, destiné au grand public."
                    />
                    <x-news-card
                        image="placeholders/news-formation"
                        category="Formation"
                        category-color="purple"
                        date="5 septembre 2024"
                        title="Formation des étudiants sur la médiation"
                        excerpt="Une session de formation a été organisée pour les étudiants de la FSJP-UAM sur les techniques de médiation et de résolution des conflits."
                    />
                    <x-news-card
                        image="placeholders/news-candidatures"
                        category="Communiqué"
                        category-color="red"
                        date="20 août 2024"
                        title="Ouverture des candidatures pour la nouvelle promotion"
                        excerpt="La Clinique Juridique FSJP-UAM lance un appel à candidatures pour intégrer sa nouvelle promotion d'étudiants encadrés."
                    />
                    <x-news-card
                        image="placeholders/news-partenariat"
                        category="Actualité"
                        category-color="primary"
                        date="10 août 2024"
                        title="Signature d'un partenariat avec une ONG"
                        excerpt="Une convention de partenariat a été signée avec une ONG locale pour renforcer l'accès au droit dans les communautés rurales."
                    />
                </div>

                <x-pagination :current="1" :total="5" />
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

                <x-sidebar-card icon="clock" title="Actualités récentes" color="amber">
                    <div class="space-y-4">
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
                    </div>
                </x-sidebar-card>

                <div class="rounded-xl bg-primary-50/60 p-5">
                    <div class="flex items-center justify-between">
                        <x-icon-badge size="sm"><x-icon name="mail" class="h-5 w-5" /></x-icon-badge>
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
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
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
