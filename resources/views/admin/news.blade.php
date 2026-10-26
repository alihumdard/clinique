@extends('layouts.admin')

@section('title', 'Gestion des actualités')
@section('search_placeholder', 'Rechercher une actualité, un mot-clé, une catégorie...')

@section('content')

    <x-admin.flash />

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
        <x-admin.stat-card icon="document" color="blue"  :value="$counts['published']" label="Actualités publiées" />
        <x-admin.stat-card icon="eye"      color="green" :value="$counts['drafts']"    label="Brouillons" />
        <x-admin.stat-card icon="clock"    color="amber" :value="$counts['scheduled']" label="Programmées" note="Publication à venir" />
        <x-admin.stat-card icon="trash"    color="red"   :value="$counts['trashed']"   label="À la corbeille" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-4">

        {{-- ============ TABLE ============ --}}
        <section class="rounded-xl border border-slate-200 bg-white xl:col-span-3">

            <div class="space-y-3 p-5">
                <form role="search" class="relative w-full lg:max-w-xs">
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

                <div class="grid grid-cols-2 gap-3 sm:flex sm:flex-wrap sm:items-center">
                    @foreach ($selects as $select)
                        <div class="min-w-0 sm:shrink-0">
                            <label for="{{ $select['id'] }}" class="sr-only">{{ $select['label'] }}</label>
                            <select
                                id="{{ $select['id'] }}"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-3.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-auto"
                            >
                                @foreach ($select['options'] as $option)
                                    <option>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach

                    <button type="button" class="col-span-2 inline-flex w-full shrink-0 items-center justify-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50 sm:col-span-1 sm:w-auto">
                        <x-icon name="filter" class="h-4 w-4" />
                        Filtrer
                    </button>
                </div>
            </div>


            <p class="flex items-center gap-1.5 px-5 pt-1 text-xs text-slate-500 lg:hidden">
                <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0" />
                Faites glisser le tableau pour voir toutes les colonnes
            </p>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[58rem] text-left">
                    <thead>
                        <tr class="whitespace-nowrap border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th scope="col" class="w-12 px-5 py-3">
                                <input
                                    type="checkbox"
                                    aria-label="Sélectionner toutes les actualités"
                                    class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                >
                            </th>
                            <th scope="col" class="w-12 px-2 py-3">#</th>
                            <th scope="col" class="w-20 px-2 py-3">Image</th>
                            <th scope="col" class="min-w-[14rem] px-3 py-3">Titre de l'actualité</th>
                            <th scope="col" class="px-3 py-3">Catégorie</th>
                            <th scope="col" class="px-3 py-3">Auteur</th>
                            <th scope="col" class="px-3 py-3">Date</th>
                            <th scope="col" class="px-3 py-3">Statut</th>
                            <th scope="col" class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($articles as $article)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3">
                                    <input
                                        type="checkbox"
                                        aria-label="Sélectionner : {{ $article->title }}"
                                        class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                    >
                                </td>

                                <td class="px-2 py-3 text-sm text-slate-500">
                                    {{ $loop->iteration + ($articles->firstItem() - 1) }}
                                </td>

                                <td class="px-2 py-3">
                                    <img
                                        src="{{ $article->imageUrl() }}"
                                        alt=""
                                        width="96" height="72"
                                        loading="lazy"
                                        class="h-11 w-14 rounded-md object-cover"
                                    >
                                </td>

                                <td class="px-3 py-3">
                                    <span class="block max-w-[16rem] text-sm font-semibold leading-snug text-slate-900">
                                        {{ $article->title }}
                                    </span>
                                </td>

                                <td class="px-3 py-3">
                                    @if($article->category)
                                        <x-admin.badge :tone="$article->category->tone">{{ $article->category->name }}</x-admin.badge>
                                    @else
                                        <span class="text-sm text-slate-400">&mdash;</span>
                                    @endif
                                </td>

                                <td class="px-3 py-3 text-sm text-slate-500">
                                    {{ $article->author?->name ?? 'Admin' }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-3 text-sm leading-tight text-slate-500">
                                    @if($article->published_at)
                                        {{ $article->published_at->translatedFormat('j F Y') }}<br>
                                        <span class="text-xs">{{ $article->published_at->format('H:i') }}</span>
                                    @else
                                        <span class="text-slate-400">&mdash;</span>
                                    @endif
                                </td>

                                <td class="px-3 py-3">
                                    <x-admin.badge :tone="$article->statusTone()">{{ $article->statusLabel() }}</x-admin.badge>
                                </td>

                                <td class="px-5 py-3">
                                    <x-admin.row-actions
                                        :label="$article->title"
                                        :view-href="$article->isPublic() ? route('news.show', $article) : null"
                                        :delete-action="route('admin.news.destroy', $article)"
                                    />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center">
                                    <p class="text-sm text-slate-500">Aucune actualité ne correspond à cette recherche.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-admin.table-footer :paginator="$articles" unit="actualités" />
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
                    // Dot colours reuse the badge palette so a category reads
                    // the same here as it does in the table.
                    $dots = [
                        'blue' => 'bg-primary-500', 'green' => 'bg-emerald-500',
                        'amber' => 'bg-amber-500', 'red' => 'bg-rose-500',
                        'purple' => 'bg-violet-500', 'pink' => 'bg-pink-500',
                        'slate' => 'bg-slate-400',
                    ];
                @endphp

                <ul class="mt-4 space-y-1">
                    <li>
                        <a
                            href="{{ route('admin.news') }}"
                            @class(['group flex items-center gap-2.5 rounded-md px-2 py-2 hover:bg-slate-50', 'bg-primary-50' => ! ($filters['categorie'] ?? null)])
                        >
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-primary-600" aria-hidden="true"></span>
                            <span class="min-w-0 flex-1 truncate text-sm font-medium text-slate-700">Toutes les catégories</span>
                            <span class="shrink-0 text-sm font-semibold text-slate-500">{{ $articles->total() }}</span>
                        </a>
                    </li>

                    @foreach ($categories as $category)
                        <li>
                            <a
                                href="{{ route('admin.news', ['categorie' => $category->id]) }}"
                                @class(['group flex items-center gap-2.5 rounded-md px-2 py-2 hover:bg-slate-50', 'bg-primary-50' => (int) ($filters['categorie'] ?? 0) === $category->id])
                            >
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $dots[$category->tone] ?? $dots['slate'] }}" aria-hidden="true"></span>
                                <span class="min-w-0 flex-1 truncate text-sm text-slate-600 group-hover:text-primary-600">{{ $category->name }}</span>
                                <span class="shrink-0 text-sm font-semibold text-slate-500">{{ $category->articles_count ?? $category->articles()->count() }}</span>
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

                <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
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
                            name="title"
                            required
                            value="{{ old('title') }}"
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
                            name="category_id"
                            required
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option value="">Sélectionner une catégorie</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
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

                    {{-- Quick publish goes live straight away; scheduling
                         belongs to the full editor. --}}
                    <input type="hidden" name="status" value="published">

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
