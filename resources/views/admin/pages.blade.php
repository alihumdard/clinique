@extends('layouts.admin')

@section('title', 'Gestion des pages')
@section('search_placeholder', 'Rechercher une page, un titre, un mot-clé...')

@section('content')

    <x-admin.page-header
        title="Gestion des pages"
        lead="Gérez toutes les pages de votre site : contenu, visibilité et ordre d'affichage."
        action-label="Ajouter une page"
        :action-href="route('admin.pages.create')"
    />

    {{-- ============ KPIs ============ --}}
    {{-- Counts are illustrative until Module 2 supplies real queries. --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-admin.stat-card icon="document" color="green" value="13" label="Pages publiées" />
        <x-admin.stat-card icon="eye-off"  color="amber" value="2"  label="Pages en brouillon" />
        <x-admin.stat-card icon="trash"    color="red"   value="1"  label="Page archivée" />
    </div>

    {{-- ============ TABLE ============ --}}
    <section class="mt-6 rounded-xl border border-slate-200 bg-white">

        <div class="flex flex-col gap-4 p-5 lg:flex-row lg:items-center lg:justify-between">
            <x-admin.filter-tabs
                class="lg:border-b-0"
                :tabs="[
                    ['label' => 'Toutes les pages', 'count' => 16, 'active' => true],
                    ['label' => 'Publiées',         'count' => 13],
                    ['label' => 'Brouillons',       'count' => 2],
                    ['label' => 'Archivées',        'count' => 1],
                ]"
            />

            <div class="flex shrink-0 flex-wrap items-center gap-3">
                <form role="search" class="relative min-w-0 flex-1 sm:w-64">
                    <label for="pages-search" class="sr-only">Rechercher une page</label>
                    <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <x-icon name="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="search"
                        id="pages-search"
                        name="q"
                        placeholder="Rechercher une page..."
                        class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    >
                </form>

                <button type="button" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50">
                    <x-icon name="filter" class="h-4 w-4" />
                    Filtrer
                </button>
            </div>
        </div>

        @php
            /*
             * Site pages. Module 2 replaces this with a query; `slug` is the
             * public path each row maps to.
             */
            $pages = [
                ['image' => 'placeholders/about-hero',      'title' => 'Accueil',                     'slug' => '/',                          'category' => 'Principale',  'tone' => 'blue',   'date' => '10 juin 2025', 'time' => '14:32'],
                ['image' => 'placeholders/about-legal',     'title' => 'La Clinique',                 'slug' => '/la-clinique',               'category' => 'Institution', 'tone' => 'purple', 'date' => '8 juin 2025',  'time' => '11:20'],
                ['image' => 'placeholders/news-partenariat','title' => 'Nos services',                'slug' => '/services',                  'category' => 'Services',    'tone' => 'blue',   'date' => '7 juin 2025',  'time' => '16:45'],
                ['image' => 'placeholders/news-1',          'title' => 'Actualités',                  'slug' => '/actualites',                'category' => 'Actualités',  'tone' => 'red',    'date' => '9 juin 2025',  'time' => '09:15'],
                ['image' => 'placeholders/news-guide',      'title' => 'Ressources',                  'slug' => '/ressources',                'category' => 'Ressources',  'tone' => 'amber',  'date' => '5 juin 2025',  'time' => '13:10'],
                ['image' => 'placeholders/video-featured',  'title' => 'Web TV',                      'slug' => '/web-tv',                    'category' => 'Média',       'tone' => 'green',  'date' => '3 juin 2025',  'time' => '10:22'],
                ['image' => 'placeholders/about-hero',      'title' => 'Contact',                     'slug' => '/contact',                   'category' => 'Principale',  'tone' => 'blue',   'date' => '6 juin 2025',  'time' => '15:40'],
                ['image' => null,                           'title' => 'Mentions légales',            'slug' => '/mentions-legales',          'category' => 'Informations','tone' => 'slate',  'date' => '1 juin 2025',  'time' => '12:18'],
                ['image' => null,                           'title' => "Conditions d'utilisation",    'slug' => '/conditions-utilisation',    'category' => 'Informations','tone' => 'slate',  'date' => '1 juin 2025',  'time' => '12:18'],
                ['image' => null,                           'title' => 'Politique de confidentialité','slug' => '/politique-confidentialite', 'category' => 'Informations','tone' => 'slate',  'date' => '1 juin 2025',  'time' => '12:18'],
            ];
        @endphp

        <p class="flex items-center gap-1.5 px-5 pt-1 text-xs text-slate-500 lg:hidden">
            <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0" />
            Faites glisser le tableau pour voir toutes les colonnes
        </p>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[60rem] text-left">
                <thead>
                    <tr class="border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th scope="col" class="w-12 px-5 py-3">
                            <input
                                type="checkbox"
                                aria-label="Sélectionner toutes les pages"
                                class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                            >
                        </th>
                        <th scope="col" class="w-12 px-2 py-3">#</th>
                        <th scope="col" class="w-20 px-2 py-3"><span class="sr-only">Aperçu</span></th>
                        <th scope="col" class="px-3 py-3">Titre de la page</th>
                        <th scope="col" class="px-3 py-3">Slug (URL)</th>
                        <th scope="col" class="px-3 py-3">Catégorie</th>
                        <th scope="col" class="px-3 py-3">Dernière modification</th>
                        <th scope="col" class="px-3 py-3">Statut</th>
                        <th scope="col" class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @foreach ($pages as $i => $page)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3">
                                <input
                                    type="checkbox"
                                    aria-label="Sélectionner : {{ $page['title'] }}"
                                    class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                >
                            </td>

                            <td class="px-2 py-3 text-sm text-slate-500">{{ $i + 1 }}</td>

                            <td class="px-2 py-3">
                                @if($page['image'])
                                    <x-picture
                                        :path="$page['image']"
                                        alt=""
                                        width="96" height="72"
                                        class="h-11 w-14 rounded-md object-cover"
                                    />
                                @else
                                    {{-- Text-only page: a neutral glyph rather than a stretched photo --}}
                                    <span class="flex h-11 w-14 items-center justify-center rounded-md bg-slate-100 text-slate-400">
                                        <x-icon name="document" class="h-5 w-5" />
                                    </span>
                                @endif
                            </td>

                            <td class="px-3 py-3 text-sm font-semibold text-slate-900">{{ $page['title'] }}</td>

                            <td class="px-3 py-3 font-mono text-sm text-slate-500">{{ $page['slug'] }}</td>

                            <td class="px-3 py-3">
                                <x-admin.badge :tone="$page['tone']">{{ $page['category'] }}</x-admin.badge>
                            </td>

                            <td class="px-3 py-3 text-sm leading-tight text-slate-500">
                                {{ $page['date'] }}<br>
                                <span class="text-xs">{{ $page['time'] }}</span>
                            </td>

                            <td class="px-3 py-3">
                                <x-admin.badge tone="green">Publié</x-admin.badge>
                            </td>

                            <td class="px-5 py-3">
                                <x-admin.row-actions :label="$page['title']" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-admin.table-footer
            :from="1" :to="10" :total="16" unit="pages"
            :current="1" :pages="2"
        />
    </section>

@endsection
