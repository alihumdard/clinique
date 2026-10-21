@extends('layouts.app')

@section('title', 'Activités et caravanes juridiques - ' . config('app.name'))
@section('meta_description', "Caravanes juridiques, sensibilisations, formations et rencontres communautaires organisees par la Clinique Juridique FSJP-UAM pour rapprocher la justice des populations.")

@section('content')

    <x-page-hero
        image="placeholders/webtv-show-hero"
        title="Activités et caravanes juridiques"
        subtitle="Aller vers les populations pour une justice plus proche de tous"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Activités et caravanes juridiques'],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        {{-- ============ CATEGORY FILTERS ============ --}}
        {{-- Static pills until Module 2 wires them to real query filters. --}}
        <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-wrap lg:overflow-visible lg:pb-0">
            <x-filter-pill :active="true">Toutes les activités</x-filter-pill>
            <x-filter-pill>Caravanes juridiques</x-filter-pill>
            <x-filter-pill>Sensibilisations</x-filter-pill>
            <x-filter-pill>Formations</x-filter-pill>
            <x-filter-pill>Ateliers</x-filter-pill>
            <x-filter-pill>Rencontres communautaires</x-filter-pill>
        </div>

        {{-- ============ INTRO + HIGHLIGHT ============ --}}
        <div class="mt-10 grid grid-cols-1 items-start gap-8 lg:grid-cols-5">
            <div class="lg:col-span-3">
                <x-eyebrow>Notre engagement</x-eyebrow>
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
            <x-eyebrow>Nos domaines d'intervention</x-eyebrow>
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
        <x-section-heading :link="route('activities.index')" link-label="Voir toutes les activités">
            Nos dernières activités
        </x-section-heading>

        @php
            $activities = [
                [
                    'image' => 'placeholders/news-caravan',
                    'category' => 'Caravane juridique',
                    'color' => 'amber',
                    'date' => '28 septembre 2024',
                    'location' => 'Tillabéri',
                    'title' => 'Caravane juridique à Tillabéri',
                    'excerpt' => "Une journée d'information et de conseil juridique pour les populations locales sur leurs droits et démarches administratives.",
                ],
                [
                    'image' => 'placeholders/news-3',
                    'category' => 'Sensibilisation',
                    'color' => 'purple',
                    'date' => '12 octobre 2024',
                    'location' => 'Niamey',
                    'title' => 'Session de sensibilisation des jeunes',
                    'excerpt' => "Sensibilisation des étudiants et jeunes sur l'importance de l'accès au droit et la prévention des conflits.",
                ],
                [
                    'image' => 'placeholders/news-formation',
                    'category' => 'Formation',
                    'color' => 'green',
                    'date' => '5 septembre 2024',
                    'location' => 'Niamey',
                    'title' => 'Formation sur la médiation',
                    'excerpt' => "Renforcement des capacités des étudiants de la Clinique sur les techniques de médiation et de résolution des conflits.",
                ],
            ];
        @endphp

        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($activities as $activity)
                <x-activity-card
                    :image="$activity['image']"
                    :category="$activity['category']"
                    :category-color="$activity['color']"
                    :date="$activity['date']"
                    :location="$activity['location']"
                    :title="$activity['title']"
                    :excerpt="$activity['excerpt']"
                    :href="route('activities.index')"
                />
            @endforeach
        </div>
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
                <div class="flex items-center gap-4 bg-primary-50/60 p-6">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-100 text-primary-600">
                        <x-icon :name="$item['icon']" class="h-6 w-6" />
                    </span>
                    <div>
                        <dt class="sr-only">{{ $item['label'] }}</dt>
                        <dd>
                            @php
                                $numeric = (int) str_replace(' ', '', $item['value']);
                                $suffix = preg_replace('/[\d\s]+/', '', $item['value']);
                            @endphp
                            <span
                                class="block text-2xl font-bold text-slate-900"
                                data-counter
                                data-counter-target="{{ $numeric }}"
                                data-counter-suffix="{{ $suffix }}"
                            >0{{ $suffix }}</span>
                            <span class="mt-0.5 block text-sm text-slate-600">{{ $item['label'] }}</span>
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

        @php
            $gallery = [
                ['path' => 'placeholders/news-caravan', 'alt' => 'Caravane juridique en zone rurale'],
                ['path' => 'placeholders/news-1', 'alt' => "Séance d'information collective"],
                ['path' => 'placeholders/news-3', 'alt' => 'Rencontre communautaire'],
                ['path' => 'placeholders/news-formation', 'alt' => 'Formation des étudiants cliniciens'],
                ['path' => 'placeholders/news-2', 'alt' => 'Atelier de sensibilisation'],
            ];
        @endphp

        <div class="mt-5">
            <x-gallery-strip :items="$gallery" :href="route('gallery')" />
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            title="Vous souhaitez organiser une activité dans votre localité ?"
            button-label="Nous contacter"
            :button-href="route('contact')"
        >
            Contactez-nous pour planifier une caravane juridique ou une session de sensibilisation.
        </x-inline-cta>
    </section>

@endsection
