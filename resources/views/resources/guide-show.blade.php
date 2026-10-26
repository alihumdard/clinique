@extends('layouts.app')

@section('title', $guide->title . ' - ' . config('app.name'))
@section('meta_description', $guide->description ?? 'Une fiche pratique de la Clinique Juridique FSJP-UAM.')

@section('content')

    <x-page-hero
        image="placeholders/news-guide"
        :badge="$guide->category?->name"
        :title="$guide->title"
        :subtitle="$guide->description"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Ressources', 'url' => route('resources')],
            ['label' => 'Fiches pratiques juridiques', 'url' => route('resources.guides')],
            ['label' => $guide->title],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">

            {{-- ============ ARTICLE ============ --}}
            <article class="lg:col-span-2">

                {{-- Meta bar: publication data on the left, sharing on the right --}}
                <div class="flex flex-col gap-4 border-b border-slate-100 pb-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-500">
                        @if($guide->published_at)
                            <span class="flex items-center gap-1.5">
                                <x-icon name="calendar" class="h-4 w-4 shrink-0" />
                                {{ $guide->published_at->translatedFormat('j F Y') }}
                            </span>
                        @endif
                        <span class="flex items-center gap-1.5">
                            <x-icon name="users" class="h-4 w-4 shrink-0" />
                            Équipe de la Clinique
                        </span>
                        <span class="flex items-center gap-1.5">
                            <x-icon name="download" class="h-4 w-4 shrink-0" />
                            {{ number_format($guide->downloads, 0, ',', ' ') }} téléchargements
                        </span>
                    </div>

                    <x-share-buttons :title="$guide->title" />
                </div>

                {{-- ============ INTRO ============ --}}
                @if($guide->description)
                    <div class="mt-8">
                        <x-info-note-card icon="book">
                            {{ $guide->description }}
                        </x-info-note-card>
                    </div>
                @endif

                {{-- ============ CONTENT ============ --}}
                {{-- whitespace-pre-line keeps the editor's paragraph breaks
                     without trusting their markup. --}}
                <div class="mt-8 whitespace-pre-line text-sm leading-relaxed text-slate-600">
                    {{ $guide->body }}
                </div>

                {{-- ============ DOWNLOAD ============ --}}
                <div class="mt-10 flex flex-col gap-4 rounded-lg bg-primary-50/60 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-white text-rose-500">
                            <x-icon name="document" class="h-6 w-6" />
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900">Télécharger la fiche complète</p>
                            <p class="mt-0.5 text-sm text-slate-600">
                                {{ $guide->title }} (PDF, {{ $guide->fileSizeForHumans() }})
                            </p>
                        </div>
                    </div>

                    <x-button :href="route('documents.guide', $guide)" variant="outline" :arrow="false" class="shrink-0">
                        <x-icon name="download" class="h-4 w-4" />
                        Télécharger
                    </x-button>
                </div>
            </article>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">

                <x-sidebar-card icon="folder" title="Domaines du droit">
                    @php
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
                            :count="$totalGuides"
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
                                :active="$guide->category_id === $domain->id"
                                :count="$domain->guides_count"
                                :icon="$icon"
                                :color="$color"
                            >
                                {{ $domain->name }}
                            </x-category-list-item>
                        @endforeach
                    </nav>
                </x-sidebar-card>

                @if($more->isNotEmpty())
                    <x-sidebar-card icon="document" title="Fiches similaires">
                        <ul class="space-y-4">
                            @foreach ($more as $item)
                                <li>
                                    <a href="{{ route('resources.guides.show', $item) }}" class="group flex items-start gap-3">
                                        <x-stored-image
                                            :path="$item->cover"
                                            alt=""
                                            fallback="placeholders/guide-practical"
                                            width="80" height="80"
                                            sizes="48px"
                                            class="h-12 w-12 shrink-0 rounded-md object-cover"
                                        />
                                        <span class="min-w-0">
                                            <span class="block text-sm font-medium leading-snug text-slate-800 group-hover:text-primary-600">{{ $item->title }}</span>
                                            @if($item->category)
                                                <span class="mt-1 flex items-center gap-1 text-xs text-slate-400">
                                                    <x-icon name="folder" class="h-3 w-3 shrink-0" />
                                                    {{ $item->category->name }}
                                                </span>
                                            @endif
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </x-sidebar-card>
                @endif

                <x-help-card title="Besoin d'un accompagnement ?">
                    Notre équipe est à votre écoute pour vous informer et vous orienter dans vos démarches juridiques.
                </x-help-card>

            </aside>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            title="Vous avez une question juridique ?"
            button-label="Faire une pré-saisine"
            :button-href="route('presaisine.index')"
        >
            Utilisez notre service de pré-saisine pour décrire votre situation et recevoir une orientation.
        </x-inline-cta>
    </section>

@endsection
