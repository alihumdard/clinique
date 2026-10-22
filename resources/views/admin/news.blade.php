@extends('layouts.admin')

@section('title', 'Gestion des actualités')
@section('search_placeholder', 'Rechercher une actualité, un mot-clé, une catégorie...')

@section('content')

    <x-admin.page-header
        title="Gestion des actualités"
        lead="Créez, modifiez et gérez toutes les actualités publiées sur votre site."
        action-label="Ajouter une actualité"
        :action-href="route('admin.news')"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Actualités'],
        ]"
    />

    {{-- ============ KPIs ============ --}}
    {{-- Counts are illustrative until Module 2 supplies real queries. --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card icon="document" color="blue"  value="24" label="Actualités publiées" trend="+12%" note="ce mois-ci" />
        <x-admin.stat-card icon="eye"      color="green" value="3"  label="Brouillons"          trend="+50%" note="depuis le mois dernier" />
        <x-admin.stat-card icon="clock"    color="amber" value="1"  label="Programmée"          note="Prévue cette semaine" />
        <x-admin.stat-card icon="trash"    color="red"   value="2"  label="À la corbeille"      note="Non publiées" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-4">

        {{-- ============ TABLE ============ --}}
        <section class="rounded-xl border border-slate-200 bg-white xl:col-span-3">

            <div class="flex flex-wrap items-center gap-3 p-5">
                <form role="search" class="relative min-w-0 flex-1 sm:max-w-xs">
                    <label for="news-search" class="sr-only">Rechercher une actualité</label>
                    <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <x-icon name="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="search"
                        id="news-search"
                        name="q"
                        placeholder="Rechercher une actualité..."
                        class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    >
                </form>

                @php
                    $selects = [
                        ['id' => 'filter-category', 'label' => 'Filtrer par catégorie', 'options' => ['Toutes les catégories', 'Actualités', 'Événements', 'Formation', 'Ressources', 'Web TV', 'Rapports']],
                        ['id' => 'filter-status',   'label' => 'Filtrer par statut',    'options' => ['Tous les statuts', 'Publié', 'Brouillon', 'Programmée', 'Corbeille']],
                        ['id' => 'filter-date',     'label' => 'Filtrer par date',      'options' => ['Toutes les dates', 'Ce mois-ci', 'Ce trimestre', 'Cette année']],
                    ];
                @endphp

                @foreach ($selects as $select)
                    <div class="shrink-0">
                        <label for="{{ $select['id'] }}" class="sr-only">{{ $select['label'] }}</label>
                        <select
                            id="{{ $select['id'] }}"
                            class="rounded-lg border border-slate-200 py-2.5 pl-3.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            @foreach ($select['options'] as $option)
                                <option>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach

                <button type="button" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50">
                    <x-icon name="filter" class="h-4 w-4" />
                    Filtrer
                </button>
            </div>

            @php
                $articles = [
                    ['image' => 'placeholders/news-caravan',     'title' => 'Lancement de la caravane juridique à Zinder', 'category' => 'Événements',           'tone' => 'blue',   'date' => '10 juin 2025', 'time' => '14:32', 'status' => 'Publié',     'statusTone' => 'green'],
                    ['image' => 'placeholders/about-legal',      'title' => 'Formation sur les droits des femmes',          'category' => 'Formation',            'tone' => 'purple', 'date' => '8 juin 2025',  'time' => '11:20', 'status' => 'Publié',     'statusTone' => 'green'],
                    ['image' => 'placeholders/news-guide',       'title' => 'Nouvelle fiche pratique disponible',           'category' => 'Ressources',           'tone' => 'amber',  'date' => '5 juin 2025',  'time' => '09:15', 'status' => 'Publié',     'statusTone' => 'green'],
                    ['image' => 'placeholders/about-team',       'title' => 'Appel à candidatures – Stagiaires 2025',       'category' => 'Appels à candidatures','tone' => 'pink',   'date' => '1 juin 2025',  'time' => '12:10', 'status' => 'Publié',     'statusTone' => 'green'],
                    ['image' => 'placeholders/about-hero',       'title' => 'La Clinique FSJP-UAM renforce ses partenariats','category' => 'Institution',         'tone' => 'blue',   'date' => '28 mai 2025',  'time' => '16:45', 'status' => 'Publié',     'statusTone' => 'green'],
                    ['image' => 'placeholders/news-partenariat', 'title' => 'Publication du rapport annuel 2024',           'category' => 'Rapports',             'tone' => 'green',  'date' => '25 mai 2025',  'time' => '10:22', 'status' => 'Publié',     'statusTone' => 'green'],
                    ['image' => 'placeholders/video-featured',   'title' => 'Nouvelle capsule Web TV : Accès à la justice', 'category' => 'Web TV',               'tone' => 'amber',  'date' => '20 mai 2025',  'time' => '15:30', 'status' => 'Brouillon',  'statusTone' => 'amber'],
                    ['image' => 'placeholders/news-3',           'title' => 'Clinique juridique présente au forum universitaire', 'category' => 'Événements',     'tone' => 'blue',   'date' => '16 mai 2025',  'time' => '13:18', 'status' => 'Publié',     'statusTone' => 'green'],
                    ['image' => 'placeholders/article-highlight-1','title' => 'Guide pratique : droits des étudiants',      'category' => 'Ressources',           'tone' => 'amber',  'date' => '12 mai 2025',  'time' => '09:40', 'status' => 'Publié',     'statusTone' => 'green'],
                    ['image' => 'placeholders/news-1',           'title' => 'Conférence sur la justice sociale',            'category' => 'Actualités',           'tone' => 'purple', 'date' => '8 mai 2025',   'time' => '11:05', 'status' => 'Programmée', 'statusTone' => 'blue'],
                ];
            @endphp

            <p class="flex items-center gap-1.5 px-5 pt-1 text-xs text-slate-500 lg:hidden">
                <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0" />
                Faites glisser le tableau pour voir toutes les colonnes
            </p>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[58rem] text-left">
                    <thead>
                        <tr class="border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th scope="col" class="w-12 px-5 py-3">
                                <input
                                    type="checkbox"
                                    aria-label="Sélectionner toutes les actualités"
                                    class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                >
                            </th>
                            <th scope="col" class="w-12 px-2 py-3">#</th>
                            <th scope="col" class="w-20 px-2 py-3">Image</th>
                            <th scope="col" class="px-3 py-3">Titre de l'actualité</th>
                            <th scope="col" class="px-3 py-3">Catégorie</th>
                            <th scope="col" class="px-3 py-3">Auteur</th>
                            <th scope="col" class="px-3 py-3">Date</th>
                            <th scope="col" class="px-3 py-3">Statut</th>
                            <th scope="col" class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($articles as $i => $article)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3">
                                    <input
                                        type="checkbox"
                                        aria-label="Sélectionner : {{ $article['title'] }}"
                                        class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                    >
                                </td>

                                <td class="px-2 py-3 text-sm text-slate-500">{{ $i + 1 }}</td>

                                <td class="px-2 py-3">
                                    <x-picture
                                        :path="$article['image']"
                                        alt=""
                                        width="96" height="72"
                                        class="h-11 w-14 rounded-md object-cover"
                                    />
                                </td>

                                <td class="px-3 py-3">
                                    <span class="block max-w-[16rem] text-sm font-semibold leading-snug text-slate-900">
                                        {{ $article['title'] }}
                                    </span>
                                </td>

                                <td class="px-3 py-3">
                                    <x-admin.badge :tone="$article['tone']">{{ $article['category'] }}</x-admin.badge>
                                </td>

                                <td class="px-3 py-3 text-sm text-slate-500">Admin</td>

                                <td class="whitespace-nowrap px-3 py-3 text-sm leading-tight text-slate-500">
                                    {{ $article['date'] }}<br>
                                    <span class="text-xs">{{ $article['time'] }}</span>
                                </td>

                                <td class="px-3 py-3">
                                    <x-admin.badge :tone="$article['statusTone']">{{ $article['status'] }}</x-admin.badge>
                                </td>

                                <td class="px-5 py-3">
                                    <x-admin.row-actions :label="$article['title']" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-admin.table-footer
                :from="1" :to="10" :total="24" unit="actualités"
                :current="1" :pages="3"
            />
        </section>

        {{-- ============ SIDEBAR ============ --}}
        <aside class="space-y-5">

            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                        <x-icon name="folder" class="h-5 w-5 text-primary-600" />
                        Catégories
                    </h2>

                    <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary-700 transition-colors hover:bg-primary-100">
                        <x-icon name="plus" class="h-4 w-4" />
                        Ajouter
                    </button>
                </div>

                @php
                    $categories = [
                        ['label' => 'Toutes les catégories', 'count' => 24, 'dot' => 'bg-primary-600'],
                        ['label' => 'Actualités',            'count' => 6,  'dot' => 'bg-primary-500'],
                        ['label' => 'Événements',            'count' => 5,  'dot' => 'bg-primary-400'],
                        ['label' => 'Formation',             'count' => 4,  'dot' => 'bg-violet-500'],
                        ['label' => 'Ressources',            'count' => 4,  'dot' => 'bg-amber-400'],
                        ['label' => 'Web TV',                'count' => 2,  'dot' => 'bg-amber-500'],
                        ['label' => 'Appels à candidatures', 'count' => 2,  'dot' => 'bg-pink-400'],
                        ['label' => 'Rapports',              'count' => 1,  'dot' => 'bg-emerald-500'],
                    ];
                @endphp

                <ul class="mt-4 space-y-1">
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ route('admin.news') }}" class="group flex items-center gap-2.5 rounded-md px-2 py-2 hover:bg-slate-50">
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $category['dot'] }}" aria-hidden="true"></span>
                                <span class="min-w-0 flex-1 truncate text-sm text-slate-600 group-hover:text-primary-600">{{ $category['label'] }}</span>
                                <span class="shrink-0 text-sm font-semibold text-slate-500">{{ $category['count'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{--
                Quick publish. Presentation only: Module 2 adds the POST
                route, server-side validation and upload handling. The
                accept/size hints below are conveniences, never the
                security boundary — the server must re-check both.
            --}}
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="document" class="h-5 w-5 text-primary-600" />
                    Publication rapide
                </h2>

                <form method="POST" action="#" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label for="quick-title" class="block text-sm font-medium text-slate-700">
                            Titre de l'actualité
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <input
                            type="text"
                            id="quick-title"
                            name="titre"
                            required
                            placeholder="Saisissez le titre..."
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="quick-category" class="block text-sm font-medium text-slate-700">
                            Catégorie
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <select
                            id="quick-category"
                            name="categorie"
                            required
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option value="">Sélectionner une catégorie</option>
                            <option>Actualités</option>
                            <option>Événements</option>
                            <option>Formation</option>
                            <option>Ressources</option>
                            <option>Web TV</option>
                            <option>Rapports</option>
                        </select>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-slate-700">Image à la une</span>

                        <label
                            for="quick-image"
                            class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
                        >
                            <x-icon name="upload" class="h-7 w-7 text-primary-500" />
                            <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour télécharger</span>
                            <span class="mt-0.5 text-xs text-slate-500">ou glissez une image ici</span>
                            <span class="mt-1 text-xs text-slate-400">(JPG, PNG - Max 2MB)</span>

                            <input
                                type="file"
                                id="quick-image"
                                name="image"
                                accept="image/jpeg,image/png"
                                class="sr-only"
                            >
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-primary-700"
                    >
                        <x-icon name="paper-plane" class="h-4 w-4" />
                        Publier l'actualité
                    </button>
                </form>
            </section>
        </aside>
    </div>

@endsection
