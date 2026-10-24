@extends('layouts.app')

@section('title', 'Résultats de recherche - ' . config('app.name'))
@section('meta_description', "Trouvez rapidement des actualites, guides pratiques, videos et informations sur les services de la Clinique Juridique FSJP-UAM.")

@section('content')

    <x-page-hero
        image="placeholders/search-hero"
        image-position="object-right"
        title="Résultats de recherche"
        subtitle="Trouvez rapidement des actualités, guides pratiques, vidéos et informations sur nos services."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Recherche'],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

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

                            <div class="mt-3 space-y-2.5">
                                @foreach ($types as $value => $label)
                                    <div class="flex items-center gap-2.5">
                                        <input
                                            type="checkbox"
                                            id="type-{{ $value }}"
                                            name="type[]"
                                            value="{{ $value }}"
                                            @checked(in_array($value, $activeTypes, true))
                                            class="h-4 w-4 shrink-0 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                        <label for="type-{{ $value }}" class="min-w-0 flex-1 truncate text-sm text-slate-600">
                                            {{ $label }}
                                        </label>
                                        <span class="shrink-0 text-xs text-slate-400">{{ $counts[$value] }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <p class="mt-3 text-xs text-slate-500">
                                Aucun type coché : tous les types sont inclus.
                            </p>
                        </fieldset>

                        <div class="space-y-2.5">
                            <x-button variant="primary" type="submit" :arrow="false" class="w-full justify-center">
                                <x-icon name="folder" class="h-4 w-4" />
                                Appliquer les filtres
                            </x-button>

                            <a
                                href="{{ route('search') }}"
                                class="flex w-full items-center justify-center gap-2 rounded-md bg-slate-50 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-100"
                            >
                                <x-icon name="refresh" class="h-4 w-4" />
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
                        @if($query === '')
                            Saisissez un mot-clé pour lancer une recherche.
                        @elseif($tooShort)
                            Saisissez un mot-clé de deux caractères au moins.
                        @else
                            <strong class="font-bold text-primary-600">{{ $total }} {{ $total === 1 ? 'résultat' : 'résultats' }}</strong>
                            pour « {{ $query }} »
                        @endif
                    </p>

                    {{-- Results come back newest first across every type;
                         relevance ranking would need a search index, which the
                         performance plan rules out for a corpus this size. --}}
                </div>

                <div>
                    @forelse ($results as $result)
                        <x-search-result
                            :image-url="$result['imageUrl'] ?? null"
                            :type="$result['type']"
                            :title="$result['title']"
                            :excerpt="$result['excerpt'] ?? null"
                            :date="$result['date']?->translatedFormat('j F Y')"
                            :views="isset($result['views']) ? number_format($result['views'], 0, ',', ' ') : null"
                            :href="$result['href']"
                            :video="$result['video'] ?? false"
                        />
                    @empty
                        <div class="py-12 text-center">
                            @if($query === '' || $tooShort)
                                <p class="text-sm text-slate-500">
                                    Recherchez une actualité, une fiche pratique, une capsule vidéo,
                                    une activité ou un rapport.
                                </p>
                            @else
                                <p class="text-sm text-slate-600">
                                    Aucun résultat pour « {{ $query }} ».
                                </p>
                                <p class="mt-2 text-sm text-slate-500">
                                    Essayez un mot-clé plus court, ou décochez les filtres de type.
                                </p>
                            @endif
                        </div>
                    @endforelse
                </div>

                <x-pagination :paginator="$results" :edges="true" />
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
