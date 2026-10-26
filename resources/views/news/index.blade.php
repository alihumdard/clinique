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
                <x-filter-pill
                    :href="route('news.index')"
                    :active="! ($filters['categorie'] ?? null)"
                >
                    Toutes les actualités
                </x-filter-pill>

                @foreach ($categories as $category)
                    @continue($category->articles_count === 0)
                    <x-filter-pill
                        :href="route('news.index', ['categorie' => $category->slug])"
                        :active="($filters['categorie'] ?? null) === $category->slug"
                    >
                        {{ $category->name }}
                    </x-filter-pill>
                @endforeach
            </div>

            <form role="search" method="GET" action="{{ route('news.index') }}" class="relative self-start">
                {{-- The active rubrique rides along, so searching inside a
                     category does not silently drop it. --}}
                @if($filters['categorie'] ?? null)
                    <input type="hidden" name="categorie" value="{{ $filters['categorie'] }}">
                @endif

                <label for="news-search" class="sr-only">Rechercher une actualité</label>
                <input
                    type="search"
                    id="news-search"
                    name="q"
                    value="{{ $filters['q'] ?? '' }}"
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
                            :stored-path="$article->image"
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
                    @php
                        // The icon pairs with the rubrique by slug; one the
                        // Clinic adds later falls back to the folder icon.
                        $icons = [
                            'evenements' => 'calendar',
                            'formations' => 'graduation-cap',
                            'caravanes-juridiques' => 'map-pin',
                            'communiques' => 'megaphone',
                            'publications' => 'document',
                        ];
                    @endphp

                    <nav class="space-y-1">
                        <x-category-list-item
                            :href="route('news.index')"
                            :active="! ($filters['categorie'] ?? null)"
                            icon="folder"
                            color="amber"
                            :count="$total"
                        >
                            Toutes les actualités
                        </x-category-list-item>

                        @foreach ($categories as $category)
                            @continue($category->articles_count === 0)
                            <x-category-list-item
                                :href="route('news.index', ['categorie' => $category->slug])"
                                :active="($filters['categorie'] ?? null) === $category->slug"
                                :icon="$icons[$category->slug] ?? 'folder'"
                                color="amber"
                                :count="$category->articles_count"
                            >
                                {{ $category->name }}
                            </x-category-list-item>
                        @endforeach
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
                        @forelse ($recent as $item)
                            <x-mini-article-row
                                :stored-path="$item->image"
                                :title="$item->title"
                                :date="$item->published_at?->translatedFormat('j F Y')"
                                :href="route('news.show', $item)"
                            />
                        @empty
                            <p class="text-sm text-slate-500">Aucune actualité pour le moment.</p>
                        @endforelse
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
                    @if(session('newsletter'))
                        <p role="status" class="mt-4 rounded-md bg-emerald-50 p-3 text-sm text-emerald-800">
                            {{ session('newsletter') }}
                        </p>
                    @else
                        <form method="POST" action="{{ route('newsletter.store') }}" class="mt-4 space-y-2.5">
                            @csrf

                            <label for="newsletter-email" class="sr-only">Votre adresse e-mail</label>
                            <input
                                type="email"
                                id="newsletter-email"
                                name="email"
                                required
                                value="{{ old('email') }}"
                                placeholder="Votre adresse e-mail"
                                @error('email') aria-invalid="true" aria-describedby="newsletter-error" @enderror
                                class="w-full rounded-md border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >

                            {{-- A field nobody sees and a script usually fills in;
                                 the server refuses the request if it arrives filled. --}}
                            <div class="hidden" aria-hidden="true">
                                <label for="newsletter-website">Site web</label>
                                <input type="text" id="newsletter-website" name="website" tabindex="-1" autocomplete="off">
                            </div>

                            @error('email')
                                <p id="newsletter-error" role="alert" class="text-sm text-rose-700">{{ $message }}</p>
                            @enderror

                            <x-button variant="primary" type="submit" class="w-full justify-center">S'abonner</x-button>
                        </form>
                    @endif
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
