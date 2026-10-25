@extends('layouts.app')

@section('title', ($current ? $current->name . ' - Web TV - ' : 'Web TV - ') . config('app.name'))
@section('meta_description', "Des videos pour informer, sensibiliser et promouvoir l'acces au droit pour tous : capsules, emissions, temoignages et formations de la Clinique Juridique FSJP-UAM.")

@section('content')

    <x-page-hero
        image="placeholders/webtv-hero"
        image-position="object-right"
        title="Web TV"
        subtitle="Des vidéos pour informer, sensibiliser et promouvoir l'accès au droit pour tous."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Web TV'],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        {{-- ============ RUBRIQUE FILTERS ============ --}}
        <div class="scrollbar-hide flex gap-1.5 overflow-x-auto">
            <x-filter-pill :href="route('webtv.index')" :active="! $current">
                Toutes les vidéos
            </x-filter-pill>

            @foreach ($categories as $category)
                @continue($category->videos_count === 0)
                <x-filter-pill
                    :href="route('webtv.index', ['categorie' => $category->slug])"
                    :active="$current?->id === $category->id"
                >
                    {{ $category->name }}
                </x-filter-pill>
            @endforeach
        </div>

        <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-3">

            {{-- ============ MAIN ============ --}}
            <div class="lg:col-span-2">

                {{-- Featured capsule: the newest one, and only on the
                     unfiltered list, so it is never also in the grid. --}}
                @if($featured)
                    <x-section-label>Vidéo à la une</x-section-label>

                    <div class="mt-5 grid grid-cols-1 gap-6 lg:grid-cols-[3fr_2fr] lg:items-stretch">
                        <x-video-player
                            :stored-path="$featured->cover"
                            :video-id="$featured->provider_id"
                            :provider="$featured->provider"
                            :duration="$featured->durationLabel()"
                            :title="$featured->title"
                            class="!aspect-auto lg:h-full lg:max-h-80"
                        />

                        <div class="flex flex-col">
                            @if($featured->category)
                                <x-category-badge color="primary" position="inline" class="self-start">
                                    {{ $featured->category->name }}
                                </x-category-badge>
                            @endif

                            <h3 class="mt-3 text-xl font-bold leading-snug text-slate-900">
                                {{ $featured->title }}
                            </h3>

                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-500">
                                @if($featured->published_at)
                                    <span class="inline-flex items-center gap-1.5">
                                        <x-icon name="calendar" class="h-3.5 w-3.5 shrink-0" />
                                        {{ $featured->published_at->translatedFormat('j F Y') }}
                                    </span>
                                @endif
                                <span class="inline-flex items-center gap-1.5">
                                    <x-icon name="eye" class="h-3.5 w-3.5 shrink-0" />
                                    {{ number_format($featured->views, 0, ',', ' ') }} vues
                                </span>
                            </div>

                            @if($featured->summary)
                                <p class="mt-3 text-sm leading-relaxed text-slate-600">
                                    {{ $featured->summary }}
                                </p>
                            @endif

                            <div class="mt-5">
                                <x-button :href="route('webtv.show', $featured)" variant="primary">
                                    Voir la vidéo
                                </x-button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Capsule grid --}}
                <div class="{{ $featured ? 'mt-12' : '' }}">
                    <x-section-heading>
                        {{ $current ? $current->name : 'Dernières vidéos' }}
                    </x-section-heading>

                    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                        @forelse ($videos as $video)
                            <x-video-card
                                :stored-path="$video->cover"
                                :category="$video->category?->name"
                                category-color="primary"
                                :duration="$video->durationLabel()"
                                :title="$video->title"
                                :date="$video->published_at?->translatedFormat('j F Y')"
                                :views="number_format($video->views, 0, ',', ' ')"
                                :href="route('webtv.show', $video)"
                            />
                        @empty
                            <p class="text-sm text-slate-500 sm:col-span-2 xl:col-span-3">
                                Aucune vidéo n'est disponible dans cette rubrique pour le moment.
                            </p>
                        @endforelse
                    </div>

                    <x-pagination :paginator="$videos" :edges="true" />
                </div>
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">

                <div class="rounded-xl bg-primary-50/60 p-5">
                    <div class="flex items-center gap-2.5">
                        <x-icon-badge size="sm">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M23.5 6.2a3.02 3.02 0 00-2.12-2.14C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.38.56A3.02 3.02 0 00.5 6.2 31.6 31.6 0 000 12a31.6 31.6 0 00.5 5.8 3.02 3.02 0 002.12 2.14c1.88.56 9.38.56 9.38.56s7.5 0 9.38-.56a3.02 3.02 0 002.12-2.14A31.6 31.6 0 0024 12a31.6 31.6 0 00-.5-5.8zM9.75 15.5v-7l6.5 3.5-6.5 3.5z"/></svg>
                        </x-icon-badge>
                        <h2 class="text-base font-bold text-slate-900">Web TV en chiffres</h2>
                    </div>

                    <x-figure-list
                        class="mt-4"
                        :items="[
                            ['icon' => 'youtube', 'value' => number_format($totals['videos'], 0, ',', ' '), 'label' => 'Vidéos publiées', 'color' => 'red'],
                            ['icon' => 'users', 'value' => number_format($totals['views'], 0, ',', ' '), 'label' => 'Vues cumulées', 'color' => 'blue'],
                            ['icon' => 'folder', 'value' => $totals['rubriques'], 'label' => 'Rubriques thématiques', 'color' => 'purple'],
                        ]"
                    />
                </div>

                <x-sidebar-card icon="folder" title="Playlists thématiques" :tint="true">
                    <ul class="space-y-3">
                        @foreach ($categories as $category)
                            @continue($category->videos_count === 0)
                            <li>
                                <a href="{{ route('webtv.index', ['categorie' => $category->slug]) }}" class="group flex items-center gap-3">
                                    <img
                                        src="{{ $category->coverUrl() }}"
                                        alt=""
                                        width="80" height="80"
                                        loading="lazy"
                                        class="h-12 w-12 shrink-0 rounded-md object-cover"
                                    >
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-semibold text-slate-900 group-hover:text-primary-600">{{ $category->name }}</span>
                                        <span class="block text-xs text-slate-500">
                                            {{ $category->videos_count }} {{ $category->videos_count === 1 ? 'vidéo' : 'vidéos' }}
                                        </span>
                                    </span>
                                    <x-icon name="arrow-right" class="h-4 w-4 shrink-0 text-slate-400 group-hover:text-primary-600" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-sidebar-card>

                <x-sidebar-card icon="youtube" title="Nos chaînes" :tint="true">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M23.5 6.2a3.02 3.02 0 00-2.12-2.14C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.38.56A3.02 3.02 0 00.5 6.2 31.6 31.6 0 000 12a31.6 31.6 0 00.5 5.8 3.02 3.02 0 002.12 2.14c1.88.56 9.38.56 9.38.56s7.5 0 9.38-.56a3.02 3.02 0 002.12-2.14A31.6 31.6 0 0024 12a31.6 31.6 0 00-.5-5.8zM9.75 15.5v-7l6.5 3.5-6.5 3.5z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900">YouTube</p>
                            <p class="mt-0.5 text-sm leading-relaxed text-slate-600">
                                Suivez toutes nos vidéos sur notre chaîne officielle.
                            </p>
                        </div>
                    </div>

                    <x-button :href="config('clinique.social.youtube') ?: '#'" variant="outline" class="mt-4 w-full justify-center">
                        Voir la chaîne
                    </x-button>
                </x-sidebar-card>

                <x-help-card icon="bell" title="Vous avez une vidéo à proposer ?">
                    Partagez vos initiatives, témoignages ou événements pour une diffusion sur notre Web TV.
                </x-help-card>

            </aside>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            title="Abonnez-vous à notre Web TV"
            button-label="Voir la chaîne YouTube"
            :button-href="config('clinique.social.youtube') ?: '#'"
        >
            Restez informé de nos dernières vidéos, émissions et événements.
        </x-inline-cta>
    </section>

@endsection
