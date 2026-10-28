@extends('layouts.app')

@section('title', ($current ? $current->name . ' - ' : '') . 'Activités et caravanes juridiques - ' . config('app.name'))
@section('meta_description', "Caravanes juridiques, sensibilisations, formations et rencontres communautaires organisees par la Clinique Juridique FSJP-UAM pour rapprocher la justice des populations.")

@section('content')

    <x-page-hero
        image="placeholders/webtv-show-hero"
        :title="__('Activités et caravanes juridiques')"
        subtitle="Aller vers les populations pour une justice plus proche de tous"
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
                <h2 class="mt-3 text-2xl font-bold text-slate-900 sm:text-3xl">Une présence sur le terrain</h2>
                <p class="mt-4 text-base leading-relaxed text-slate-600">
                    La Clinique Juridique FSJP-UAM organise régulièrement des activités de proximité
                    à travers les caravanes juridiques, des sessions de sensibilisation, des formations
                    et des rencontres communautaires pour promouvoir l'accès au droit et renforcer la
                    protection des personnes vulnérables.
                </p>
            </div>

            <div class="lg:col-span-2">
                <x-info-note-card icon="users" title="Une justice plus proche des populations">
                    Nos activités permettent d'informer, d'écouter et d'accompagner les citoyens dans leurs
                    démarches juridiques, notamment dans les zones rurales et périurbaines.
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
                    'green' => 'bg-emerald-50 text-emerald-600',
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

    {{-- ============ IMPACT ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
        <x-section-label>Impact de nos activités</x-section-label>

        @php
            $impact = [
                ['icon' => 'users', 'value' => '15+', 'label' => 'Caravanes juridiques organisées'],
                ['icon' => 'users', 'value' => '2 000+', 'label' => 'Personnes sensibilisées'],
                ['icon' => 'document', 'value' => '50+', 'label' => 'Consultations juridiques sur le terrain'],
                ['icon' => 'graduation-cap', 'value' => '10+', 'label' => 'Partenaires institutionnels'],
            ];
        @endphp

        <dl class="mt-5 grid grid-cols-2 gap-px overflow-hidden rounded-xl bg-slate-200 lg:grid-cols-4">
            @foreach ($impact as $item)
                <div class="flex items-center gap-3 bg-primary-50/60 p-4 sm:gap-4 sm:p-6">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-100 text-primary-600 sm:h-12 sm:w-12">
                        <x-icon :name="$item['icon']" class="h-5 w-5 sm:h-6 sm:w-6" />
                    </span>
                    <div class="min-w-0">
                        <dt class="sr-only">{{ $item['label'] }}</dt>
                        <dd>
                            @php
                                $numeric = (int) str_replace(' ', '', $item['value']);
                                $suffix = preg_replace('/[\d\s]+/', '', $item['value']);
                            @endphp
                            <span
                                class="block text-xl font-bold text-slate-900 sm:text-2xl"
                                data-counter
                                data-counter-target="{{ $numeric }}"
                                data-counter-suffix="{{ $suffix }}"
                            >{{ $item['value'] }}</span>
                            <span class="mt-0.5 block text-xs text-slate-600 sm:text-sm">{{ $item['label'] }}</span>
                        </dd>
                    </div>
                </div>
            @endforeach
        </dl>
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
            :title="__('Vous souhaitez organiser une activité dans votre localité ?')"
            :button-label="__('Nous contacter')"
            :button-href="route('contact')"
        >
            Contactez-nous pour planifier une caravane juridique ou une session de sensibilisation.
        </x-inline-cta>
    </section>

@endsection
