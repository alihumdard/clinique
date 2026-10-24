@extends('layouts.app')

@section('title', ($current ? $current->name . ' - ' : '') . 'Fiches pratiques juridiques - ' . config('app.name'))
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
        <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-wrap lg:overflow-visible lg:pb-0">
            <x-filter-pill :href="route('resources.guides')" :active="! $current">
                Toutes les fiches
            </x-filter-pill>

            @foreach ($domains as $domain)
                @continue($domain->guides_count === 0)
                <x-filter-pill
                    :href="route('resources.guides', ['domaine' => $domain->slug])"
                    :active="$current?->id === $domain->id"
                >
                    {{ $domain->name }}
                </x-filter-pill>
            @endforeach
        </div>

        <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-3">

            {{-- ============ GUIDE LIST ============ --}}
            <div class="lg:col-span-2">

                <div class="flex flex-wrap items-center justify-between gap-4">
                    <x-section-label>
                        {{ $current ? $current->name : 'Toutes les fiches pratiques' }} ({{ $guides->total() }})
                    </x-section-label>

                    {{-- No JavaScript: the select submits its own form, and
                         the active domain and search ride along as hidden
                         fields so sorting never clears them. --}}
                    <form method="GET" action="{{ route('resources.guides') }}" class="flex items-center gap-2">
                        @if($current)
                            <input type="hidden" name="domaine" value="{{ $current->slug }}">
                        @endif
                        @if($filters['q'] ?? null)
                            <input type="hidden" name="q" value="{{ $filters['q'] }}">
                        @endif

                        <label for="guides-sort" class="text-sm text-slate-500">Trier par :</label>
                        <select
                            id="guides-sort"
                            name="tri"
                            class="rounded-md border border-slate-200 py-2 pl-3 pr-8 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            @php
                                $orders = [
                                    'recentes' => 'Plus récentes',
                                    'consultees' => 'Plus consultées',
                                    'alphabetique' => 'Ordre alphabétique',
                                ];
                            @endphp
                            @foreach ($orders as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['tri'] ?? 'recentes') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50">
                            Trier
                        </button>
                    </form>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($guides as $guide)
                        <x-guide-card
                            :image-url="$guide->coverUrl()"
                            :category="$guide->category?->name"
                            category-color="primary"
                            :title="$guide->title"
                            :excerpt="$guide->description"
                            :filesize="$guide->fileSizeForHumans()"
                            :href="route('resources.guides.show', $guide)"
                            :download-href="route('documents.guide', $guide)"
                        />
                    @empty
                        <p class="text-sm text-slate-500 sm:col-span-2 xl:col-span-3">
                            Aucune fiche pratique ne correspond à cette recherche.
                        </p>
                    @endforelse
                </div>

                <x-pagination :paginator="$guides" />
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">

                <form role="search" method="GET" action="{{ route('resources.guides') }}" class="relative">
                    @if($current)
                        <input type="hidden" name="domaine" value="{{ $current->slug }}">
                    @endif
                    <label for="guides-search" class="sr-only">Rechercher une fiche pratique</label>
                    <input
                        type="search"
                        id="guides-search"
                        name="q"
                        value="{{ $filters['q'] ?? '' }}"
                        placeholder="Rechercher une fiche..."
                        class="w-full rounded-md border border-slate-200 py-2.5 pl-4 pr-10 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    >
                    <button type="submit" aria-label="Rechercher" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary-600">
                        <x-icon name="search" class="h-4 w-4" />
                    </button>
                </form>

                <x-sidebar-card icon="folder" title="Domaines du droit" :tint="true">
                    @php
                        // The icon pairs with the domain by name; a domain the
                        // Clinic adds later falls back to the folder icon.
                        $icons = [
                            'droit-de-la-famille' => ['users', 'blue'],
                            'droit-civil' => ['scale', 'green'],
                            'droit-penal' => ['gavel', 'pink'],
                            'droits-humains' => ['heart', 'purple'],
                            'droit-administratif' => ['building', 'green'],
                            'droit-du-travail' => ['handshake', 'purple'],
                            'droit-foncier' => ['map-pin', 'amber'],
                            'droit-des-successions' => ['document', 'red'],
                            'vie-pratique' => ['info', 'blue'],
                        ];
                    @endphp

                    <nav class="space-y-1">
                        <x-category-list-item
                            :href="route('resources.guides')"
                            :active="! $current"
                            :count="$totals['guides']"
                            icon="folder"
                            color="amber"
                        >
                            Toutes les fiches
                        </x-category-list-item>

                        @foreach ($domains as $domain)
                            @continue($domain->guides_count === 0)
                            @php [$icon, $color] = $icons[$domain->slug] ?? ['folder', 'slate']; @endphp
                            <x-category-list-item
                                :href="route('resources.guides', ['domaine' => $domain->slug])"
                                :active="$current?->id === $domain->id"
                                :count="$domain->guides_count"
                                :icon="$icon"
                                :color="$color"
                            >
                                {{ $domain->name }}
                            </x-category-list-item>
                        @endforeach
                    </nav>
                </x-sidebar-card>

                @if($popular->isNotEmpty())
                    <x-sidebar-card icon="heart" title="Fiches les plus consultées" :tint="true">
                        <ul class="space-y-4">
                            @foreach ($popular as $item)
                                <li>
                                    <a href="{{ route('resources.guides.show', $item) }}" class="group flex items-start gap-3">
                                        <img
                                            src="{{ $item->coverUrl() }}"
                                            alt=""
                                            width="80" height="80"
                                            loading="lazy"
                                            class="h-12 w-12 shrink-0 rounded-md object-cover"
                                        >
                                        <span class="min-w-0">
                                            <span class="block text-sm font-medium leading-snug text-slate-800 group-hover:text-primary-600">{{ $item->title }}</span>
                                            <span class="mt-1 flex items-center gap-1 text-xs text-slate-400">
                                                <x-icon name="download" class="h-3 w-3 shrink-0" />
                                                {{ number_format($item->downloads, 0, ',', ' ') }} téléchargements
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </x-sidebar-card>
                @endif

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
