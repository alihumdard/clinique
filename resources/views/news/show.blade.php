@extends('layouts.app')

@section('title', "Conférence sur l'accès au droit des personnes vulnérables - " . config('app.name'))
@section('meta_description', "La Clinique Juridique FSJP-UAM a organise, le 10 octobre 2024, une conference-debat sur l'acces au droit des personnes vulnerables.")

@section('content')

    {{-- ============ ARTICLE HERO ============ --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0">
            <x-picture
                path="placeholders/news-1"
                alt=""
                width="1600" height="500"
                :eager="true"
                class="h-full w-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-navy-950/90 via-navy-900/70 to-navy-900/20"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <nav aria-label="Fil d'Ariane" class="mb-4 flex flex-wrap items-center gap-2 text-sm text-slate-300">
                <a href="{{ route('home') }}" class="hover:text-white">Accueil</a>
                <span aria-hidden="true">&rsaquo;</span>
                <a href="{{ route('news.index') }}" class="hover:text-white">Actualités</a>
                <span aria-hidden="true">&rsaquo;</span>
                <span class="text-white">Conférence sur l'accès au droit des personnes vulnérables</span>
            </nav>

            <span class="mb-4 inline-block rounded-md bg-amber-500 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-white">
                Événement
            </span>

            <h1 class="max-w-2xl text-3xl font-bold leading-tight text-white sm:text-4xl">
                Conférence sur l'accès au droit des personnes vulnérables
            </h1>
            <p class="mt-3 max-w-xl text-base text-slate-200">
                Une rencontre riche en échanges sur les défis et les solutions pour une meilleure protection
                des droits des personnes vulnérables au Niger.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">

            {{-- ============ ARTICLE BODY ============ --}}
            <div class="lg:col-span-2">
                <x-article-meta-bar date="12 octobre 2024" author="Équipe de la Clinique" category="Événement" />

                <p class="mt-6 text-base leading-relaxed text-slate-700">
                    La Clinique Juridique FSJP-UAM a organisé, le 10 octobre 2024, une conférence-débat sur
                    l'accès au droit des personnes vulnérables, avec la participation d'experts, d'enseignants,
                    d'étudiants et de représentants d'organisations de la société civile.
                </p>

                <x-picture
                    path="placeholders/news-1"
                    alt="Panel de la conférence sur l'accès au droit"
                    width="1000" height="600"
                    class="mt-6 aspect-[5/3] w-full rounded-xl object-cover"
                />

                <div class="mt-8 space-y-3">
                    <x-section-label>Objectifs de la conférence</x-section-label>
                    <p class="text-base leading-relaxed text-slate-600">
                        Cette conférence avait pour objectif de sensibiliser sur les enjeux liés à l'accès au
                        droit des personnes vulnérables (femmes, enfants, personnes handicapées, personnes
                        déplacées, etc.) et de proposer des pistes de solutions pour renforcer leur protection
                        juridique au Niger.
                    </p>
                </div>

                <div class="mt-10 space-y-5">
                    <x-section-label>Les temps forts</x-section-label>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <x-media-text-item image="placeholders/article-highlight-1" title="Intervention des experts">
                            Des spécialistes ont partagé leurs analyses sur les défis et les opportunités en matière d'accès au droit.
                        </x-media-text-item>
                        <x-media-text-item image="placeholders/article-highlight-2" title="Échanges avec les étudiants">
                            Les étudiants ont activement participé aux discussions et posé de nombreuses questions.
                        </x-media-text-item>
                        <x-media-text-item image="placeholders/article-highlight-3" title="Recommandations">
                            Des recommandations concrètes ont été formulées pour améliorer l'accès au droit des personnes vulnérables.
                        </x-media-text-item>
                    </div>
                </div>

                <div class="mt-10 space-y-3">
                    <x-section-label>Conclusion</x-section-label>
                    <p class="text-base leading-relaxed text-slate-600">
                        La Clinique Juridique FSJP-UAM réaffirme son engagement à promouvoir une justice plus
                        inclusive et accessible à tous, en particulier aux personnes les plus vulnérables.
                        Cette conférence s'inscrit dans sa mission de service public et de formation pratique
                        des étudiants en droit.
                    </p>
                </div>

                {{-- Documents liés --}}
                <div class="mt-10 flex flex-col items-start gap-4 rounded-lg border border-slate-200 bg-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <x-icon-badge size="sm"><x-icon name="download" class="h-5 w-5" /></x-icon-badge>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Documents liés</p>
                            <p class="mt-0.5 flex items-center gap-1.5 text-xs text-slate-500">
                                <x-icon name="document" class="h-3.5 w-3.5" />
                                Compte rendu de la conférence (PDF, 2.1 Mo)
                            </p>
                        </div>
                    </div>
                    <x-button href="#" variant="outline" :arrow="false" class="shrink-0">Télécharger</x-button>
                </div>
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6">
                <x-sidebar-card icon="clock" title="Actualités récentes" color="amber" :tint="true">
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
                        <x-mini-article-row
                            image="placeholders/news-candidatures"
                            title="Ouverture des candidatures pour la nouvelle promotion"
                            date="20 août 2024"
                        />
                    </div>
                </x-sidebar-card>

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
            </aside>
        </div>
    </section>

    {{-- ============ CTA (dark) ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            title="Besoin d'une assistance juridique ?"
            button-label="Faire une pré-saisine"
            :button-href="route('presaisine.index')"
        >
            Remplissez notre formulaire de pré-saisine pour être rapidement orienté vers le bon service.
        </x-inline-cta>
    </section>

@endsection
