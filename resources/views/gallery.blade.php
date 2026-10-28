@extends('layouts.app')

@section('title', 'Galerie photos - ' . config('app.name'))
@section('meta_description', "Decouvrez en images les activites, caravanes juridiques, formations et rencontres communautaires de la Clinique Juridique FSJP-UAM.")

@section('content')

    <x-page-hero
        image="placeholders/gallery-hero"
        image-position="object-left-top"
        :title="__('Galerie photos')"
        subtitle="Découvrez en images nos activités, caravanes juridiques, formations et rencontres avec les communautés."
        :breadcrumb="[
            ['label' => __('Accueil'), 'url' => route('home')],
            ['label' => __('Galerie photos')],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        {{-- ============ ALBUM FILTERS ============ --}}
        <div class="scrollbar-hide flex gap-1.5 overflow-x-auto">
            <x-filter-pill :href="route('gallery')" :active="! $current">
                Toutes les photos
            </x-filter-pill>

            @foreach ($index as $row)
                <x-filter-pill
                    :href="route('gallery', ['album' => $row['category']->slug])"
                    :active="$current?->id === $row['category']->id"
                >
                    {{ $row['category']->name }}
                </x-filter-pill>
            @endforeach
        </div>

        <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-3">

            {{-- ============ ALBUM GROUPS ============ --}}
            <div class="space-y-10 lg:col-span-2">
                @forelse ($albums as $album)
                    <section>
                        <x-section-heading
                            :link="$album['total'] > $album['photos']->count() ? route('gallery', ['album' => $album['category']->slug]) : null"
                            link-label="Voir les {{ $album['total'] }} photos"
                        >
                            {{ $album['category']->name }}
                        </x-section-heading>

                        <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3">
                            @foreach ($album['photos'] as $photo)
                                <x-photo-tile :stored-path="$photo->path" :alt="$photo->alt()" />
                            @endforeach
                        </div>
                    </section>
                @empty
                    <p class="text-sm text-slate-500">
                        Aucune photo n'est publiee dans cette rubrique pour le moment.
                    </p>
                @endforelse
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">

                <x-sidebar-card icon="camera" title="Albums photos" :tint="true">
                    <ul class="space-y-3">
                        @foreach ($index as $row)
                            <li>
                                <a href="{{ route('gallery', ['album' => $row['category']->slug]) }}" class="group flex items-center gap-3">
                                    <x-stored-image
                                        :path="$row['category']->cover ?? null"
                                        alt=""
                                        fallback="placeholders/news-caravan"
                                        width="80" height="80"
                                        sizes="48px"
                                        class="h-12 w-12 shrink-0 rounded-md object-cover"
                                    />
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-semibold text-slate-900 group-hover:text-primary-600">{{ $row['category']->name }}</span>
                                        <span class="block text-xs text-slate-500">{{ $row['total'] }} {{ $row['total'] === 1 ? 'photo' : 'photos' }}</span>
                                    </span>
                                    <x-icon name="arrow-right" class="h-4 w-4 shrink-0 text-slate-400 group-hover:text-primary-600" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-sidebar-card>

                <div class="rounded-xl bg-primary-50/60 p-5">
                    <div class="flex items-center gap-2.5">
                        <x-icon-badge size="sm"><x-icon name="image" class="h-5 w-5" /></x-icon-badge>
                        <h3 class="text-base font-bold text-slate-900">Notre galerie en chiffres</h3>
                    </div>

                    <x-figure-list
                        class="mt-4"
                        :items="[
                            ['icon' => 'image', 'value' => number_format($totals['photos'], 0, ',', ' '), 'label' => 'Photos publiées', 'color' => 'blue'],
                            ['icon' => 'folder', 'value' => $totals['albums'], 'label' => 'Albums thématiques', 'color' => 'amber'],
                            ['icon' => 'calendar', 'value' => $totals['activities'], 'label' => 'Activités couvertes', 'color' => 'purple'],
                        ]"
                    />
                </div>

                <x-help-card icon="paper-plane" title="Vous avez des photos à partager ?">
                    Participez à notre galerie en nous envoyant vos photos d'activités.
                </x-help-card>

            </aside>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            :title="__('Une activité à partager ?')"
            :button-label="__('Nous contacter')"
            :button-href="route('contact')"
        >
            Contactez-nous pour que vos photos soient publiées dans notre galerie.
        </x-inline-cta>
    </section>

@endsection
