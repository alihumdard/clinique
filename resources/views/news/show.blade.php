@extends('layouts.app')

@section('title', $article->title . ' - ' . config('app.name'))
@section('meta_description', str(strip_tags($article->excerpt ?: $article->body ?: ''))->limit(150))

@section('content')

    {{-- ============ ARTICLE HERO ============ --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0">
            <x-stored-image
                :path="$article->image"
                alt=""
                width="1600" height="500"
                sizes="100vw"
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
                <span class="text-white">{{ $article->title }}</span>
            </nav>

            @if($article->category)
                <x-category-badge position="inline" :color="$article->category->tone ?? 'primary'" class="mb-4">
                    {{ $article->category->name }}
                </x-category-badge>
            @endif

            <h1 class="max-w-2xl text-3xl font-bold leading-tight text-white sm:text-4xl">
                {{ $article->title }}
            </h1>

            @if(filled($article->excerpt))
                <p class="mt-3 max-w-xl text-base text-slate-200">
                    {{ $article->excerpt }}
                </p>
            @endif
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-start gap-10 lg:grid-cols-3">

            {{-- ============ ARTICLE BODY ============ --}}
            <div class="lg:col-span-2">
                <x-article-meta-bar
                    :date="$article->published_at?->translatedFormat('j F Y')"
                    :author="$article->author?->name ?? 'Équipe de la Clinique'"
                    :category="$article->category?->name ?? 'Actualités'"
                    :title="$article->title"
                />

                @if(filled($article->body))
                    {{--
                        Same treatment as the CMS pages: whitespace-pre-line
                        keeps the editor's paragraph breaks, and the text is
                        escaped, so an article cannot put markup on the site.
                    --}}
                    <div class="mt-6 whitespace-pre-line text-base leading-relaxed text-slate-700">{{ $article->body }}</div>
                @endif

                {{--
                    The design had "Objectifs", "Les temps forts", "Conclusion"
                    and a "Documents liés" box here. Nothing in the schema
                    holds them: an article has one body and no attachments, so
                    rendering them would mean inventing content per article.
                --}}

                @if($related->isNotEmpty())
                    <div class="mt-12 space-y-5">
                        <x-section-label>Sur le même thème</x-section-label>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            @foreach ($related as $item)
                                <x-news-card
                                    :stored-path="$item->image"
                                    :category="$item->category?->name"
                                    :category-color="$item->category?->tone ?? 'primary'"
                                    :date="$item->published_at?->translatedFormat('j F Y')"
                                    :title="$item->title"
                                    :excerpt="$item->excerpt"
                                    :href="route('news.show', $item)"
                                />
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6">
                <x-sidebar-card icon="clock" title="Actualités récentes" color="amber" :tint="true">
                    <div class="space-y-4">
                        @forelse ($recent as $item)
                            <x-mini-article-row
                                :stored-path="$item->image"
                                :title="$item->title"
                                :date="$item->published_at?->translatedFormat('j F Y')"
                                :href="route('news.show', $item)"
                            />
                        @empty
                            <p class="text-sm text-slate-500">Aucune autre actualité pour le moment.</p>
                        @endforelse
                    </div>
                </x-sidebar-card>

                <x-sidebar-card icon="folder" title="Catégories" color="amber" :tint="true">
                    @php
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
                                :active="$article->category_id === $category->id"
                                :icon="$icons[$category->slug] ?? 'folder'"
                                color="amber"
                                :count="$category->articles_count"
                            >
                                {{ $category->name }}
                            </x-category-list-item>
                        @endforeach
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
