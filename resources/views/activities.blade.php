@extends('layouts.app')

@section('title', ($current ? $current->name . ' - ' : '') . 'Activités et caravanes juridiques - ' . config('app.name'))
@section('meta_description', "Caravanes juridiques, sensibilisations, formations et rencontres communautaires organisees par la Clinique Juridique FSJP-UAM pour rapprocher la justice des populations.")

@section('content')

    <x-page-hero
        image="placeholders/webtv-show-hero"
        imagePosition="object-right-top"
        :title="__('Activités et caravanes juridiques')"
        subtitle="Caravanes juridiques, sensibilisations et rencontres communautaires pour rapprocher le droit des citoyens."
        :breadcrumb="[
            ['label' => __('Accueil'), 'url' => route('home')],
            ['label' => __('Activités et caravanes juridiques')],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        {{-- ============ CATEGORY FILTERS ============ --}}
        <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-wrap lg:overflow-visible lg:pb-0">
            <x-filter-pill :href="route('activities.index')" :active="! $current">
                Toutes les activités
            </x-filter-pill>

            @foreach ($categories as $category)
                @continue($category->activities_count === 0)
                <x-filter-pill
                    :href="route('activities.index', ['categorie' => $category->slug])"
                    :active="$current?->id === $category->id"
                >
                    {{ $category->name }}
                </x-filter-pill>
            @endforeach
        </div>

        {{-- ============ INTRO + HIGHLIGHT ============ --}}
        <div class="mt-10 grid grid-cols-1 items-start gap-8 lg:grid-cols-5">
            <div class="lg:col-span-3">
                <x-eyebrow>{{ __('Notre engagement') }}</x-eyebrow>
                <h2 class="mt-3 text-2xl font-bold text-slate-900 sm:text-3xl">Aller vers les populations</h2>
                <p class="mt-4 text-base leading-relaxed text-slate-600">
                    La Clinique Juridique FSJP-UAM organise régulièrement des caravanes juridiques,
                    des sessions de sensibilisation et des rencontres communautaires pour informer
                    les populations sur leurs droits et les accompagner dans leurs démarches, notamment
                    dans les communes de Niamey avec le soutien de l'IDLO.
                </p>
            </div>

            <div class="lg:col-span-2">
                <x-info-note-card icon="users" title="Avec le soutien de l'IDLO">
                    Les premières caravanes juridiques ont été menées dans les communes de Niamey
                    en partenariat avec l'Organisation internationale de droit du développement (IDLO).
                </x-info-note-card>
            </div>
        </div>
    </section>

    {{-- ============ DOMAINES D'INTERVENTION ============ --}}
    <section class="bg-slate-50 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-eyebrow>{{ __("Nos domaines d'intervention") }}</x-eyebrow>
            <h2 class="mt-3 text-2xl font-bold text-slate-900 sm:text-3xl">Des actions adaptées aux besoins des populations</h2>

            @php
                $domains = [
                    ['icon' => 'users', 'title' => 'Accès au droit', 'text' => 'Information et conseil juridique gratuit pour tous.', 'color' => 'blue'],
                    ['icon' => 'book', 'title' => 'Sensibilisation', 'text' => 'Campagnes sur les droits et les obligations des citoyens.', 'color' => 'green'],
                    ['icon' => 'users', 'title' => 'Caravanes juridiques', 'text' => 'Présence dans les zones rurales et éloignées.', 'color' => 'blue'],
                    ['icon' => 'graduation-cap', 'title' => 'Formations', 'text' => 'Renforcement des capacités des étudiants et des communautés.', 'color' => 'amber'],
                    ['icon' => 'heart', 'title' => 'Partenariats', 'text' => "Collaboration avec les autorités locales, ONG et organisations de la société civile.", 'color' => 'red'],
                ];

                $badgeColors = [
                    'blue' => 'bg-primary-50 text-primary-600',
                    'green' => 'bg-primary-50 text-primary-600',
                    'amber' => 'bg-amber-50 text-amber-600',
                    'red' => 'bg-rose-50 text-rose-600',
                ];
            @endphp

            <ul class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ($domains as $domain)
                    <li class="rounded-lg border border-slate-200 bg-white p-5 transition-all duration-300 hover:-translate-y-1 hover:border-primary-200 hover:bg-primary-50/40 hover:shadow-md">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full {{ $badgeColors[$domain['color']] }}">
                            <x-icon :name="$domain['icon']" class="h-5 w-5" />
                        </span>
                        <h3 class="mt-4 text-sm font-semibold text-slate-900">{{ $domain['title'] }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $domain['text'] }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ============ DERNIERES ACTIVITES ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <x-section-heading>
            {{ $current ? $current->name : 'Nos dernières activités' }}
        </x-section-heading>

        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($activities as $activity)
                <x-activity-card
                    :stored-path="$activity->image"
                    :category="$activity->category?->name"
                    category-color="primary"
                    :date="$activity->held_on?->translatedFormat('j F Y')"
                    :location="$activity->location"
                    :title="$activity->title"
                    :excerpt="$activity->excerpt"
                    :href="route('activities.show', $activity)"
                />
            @empty
                <p class="text-sm text-slate-500 sm:col-span-2 lg:col-span-3">
                    Aucune activité n'est publiée dans cette rubrique pour le moment.
                </p>
            @endforelse
        </div>

        <x-pagination :paginator="$activities" />
    </section>


    {{-- ============ GALERIE ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
        <x-section-heading :link="route('gallery')" link-label="Voir toute la galerie">
            Galerie photos
        </x-section-heading>

        <div class="mt-5">
            <x-gallery-strip :photos="$strip" :href="route('gallery')" />
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            :title="__('Vous souhaitez organiser une activité avec la Clinique ?')"
            :button-label="__('Nous contacter')"
            :button-href="route('contact')"
        >
            Contactez-nous pour planifier une caravane juridique, une session de sensibilisation ou
            toute autre action de terrain dans votre localité.
        </x-inline-cta>
    </section>

@endsection
