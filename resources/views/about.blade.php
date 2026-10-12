@extends('layouts.app')

@section('title', 'À propos de la Clinique - ' . config('app.name'))
@section('meta_description', "La Clinique Juridique FSJP-UAM est une structure universitaire creee au sein de la Faculte des Sciences Juridiques et Politiques de l'Universite Abdou Moumouni de Niamey.")

@section('content')

    <x-page-hero
        image="placeholders/about-hero"
        title="À propos de la Clinique"
        subtitle="Une clinique juridique universitaire au service de la population, pour une justice plus accessible, plus humaine et plus proche des citoyens."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'À propos de la Clinique'],
        ]"
    />

    {{-- ============ PRESENTATION ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2">
            <div>
                <x-eyebrow>Notre présentation</x-eyebrow>
                <h2 class="mt-2 text-3xl font-bold leading-tight text-slate-900">
                    Une clinique juridique au cœur
                    <span class="block">de l'Université Abdou Moumouni</span>
                </h2>
                <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">
                    La Clinique Juridique FSJP-UAM est une structure universitaire créée au sein de la Faculté
                    des Sciences Juridiques et Politiques de l'Université Abdou Moumouni de Niamey. Elle a pour
                    mission d'offrir une assistance juridique gratuite à la population, de promouvoir la culture
                    des droits et de former les futurs professionnels du droit à travers une approche pratique,
                    solidaire et citoyenne.
                </p>
                <div class="mt-6">
                    <x-button href="#mission" variant="primary">Notre mission</x-button>
                </div>
            </div>

            <x-picture
                path="placeholders/about-presentation"
                alt="Bâtiment de la Faculté des Sciences Juridiques et Politiques"
                width="1384" height="1136"
                class="aspect-[3/2] w-full rounded-xl object-cover"
            />
        </div>

        {{-- Mission / Vision / Valeurs --}}
        <div id="mission" class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-3">
            <x-value-card icon="document" title="Notre mission">
                Offrir une assistance juridique gratuite et accessible, promouvoir la justice et former les
                étudiants par la pratique.
            </x-value-card>
            <x-value-card icon="search" title="Notre vision">
                Une société plus juste, informée et respectueuse des droits fondamentaux.
            </x-value-card>
            <x-value-card icon="scale" title="Nos valeurs">
                Solidarité, intégrité, impartialité, professionnalisme et engagement au service de la communauté.
            </x-value-card>
        </div>
    </section>

    {{-- ============ HISTOIRE ============ --}}
    <section class="bg-slate-50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2">
                <div>
                    <x-eyebrow color="muted">Notre histoire</x-eyebrow>
                    <h2 class="mt-2 text-3xl font-bold leading-tight text-slate-900">Une initiative au service de la justice</h2>
                    <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">
                        La Clinique Juridique FSJP-UAM est née de la volonté de la Faculté des Sciences Juridiques
                        et Politiques de contribuer activement à l'accès au droit et à la promotion de la justice au Niger.
                    </p>
                    <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">
                        Depuis sa création, elle n'a cessé de développer ses activités à travers des consultations
                        juridiques, des campagnes de sensibilisation, des caravanes juridiques et des formations
                        pratiques pour les étudiants.
                    </p>
                </div>

                <x-picture
                    path="placeholders/about-story"
                    alt="Étudiants cliniciens devant la Faculté des Sciences Juridiques et Politiques"
                    width="1495" height="1052"
                    class="aspect-[3/2] w-full rounded-xl object-cover"
                />
            </div>
        </div>
    </section>

    {{-- ============ CHIFFRES CLES (dark band) ============ --}}
    <section class="bg-navy-900 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-eyebrow class="!text-amber-400">Quelques chiffres clés</x-eyebrow>
            <h2 class="mt-2 text-3xl font-bold text-white">Notre impact en chiffres</h2>

            <div class="mt-8 grid grid-cols-2 gap-5 lg:grid-cols-4">
                <x-stat-dark icon="users" value="500+" label="Personnes conseillées depuis la création" />
                <x-stat-dark icon="book" value="200+" label="Étudiants formés par la pratique" />
                <x-stat-dark icon="gavel" value="50+" label="Activités de sensibilisation et caravanes juridiques" />
                <x-stat-dark icon="document" value="100+" label="Supports pédagogiques et fiches pratiques" />
            </div>
        </div>
    </section>

    {{-- ============ CADRE INSTITUTIONNEL ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2">
            <x-picture
                path="placeholders/about-institutional"
                alt="Balance de la justice devant des livres de droit"
                width="1536" height="1024"
                class="aspect-[3/2] w-full rounded-xl object-cover"
            />

            <div>
                <x-eyebrow>Cadre institutionnel</x-eyebrow>
                <h2 class="mt-2 text-3xl font-bold leading-tight text-slate-900">Création et cadre juridique</h2>
                <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">
                    La Clinique Juridique FSJP-UAM a été créée par arrêté de l'Université Abdou Moumouni de
                    Niamey, dans le cadre de la promotion de l'enseignement clinique du droit et de l'engagement
                    social de la faculté.
                </p>

                <div class="mt-6 flex flex-col items-start gap-4 rounded-lg border border-primary-100 bg-primary-50/60 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <x-icon-badge size="sm"><x-icon name="document" class="h-5 w-5" /></x-icon-badge>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Arrêté de création</p>
                            <p class="text-xs text-slate-500">Téléchargez l'arrêté de création de la Clinique Juridique FSJP-UAM au format PDF.</p>
                        </div>
                    </div>
                    <x-button :href="route('resources.reports')" variant="primary" :arrow="false" class="shrink-0">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Télécharger le document
                    </x-button>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ PARCOURS (timeline) ============ --}}
    <section class="bg-slate-50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-eyebrow color="muted">Les grandes étapes</x-eyebrow>
            <h2 class="mt-2 text-3xl font-bold text-slate-900">Notre parcours</h2>

            <div class="relative mt-10 flex flex-col gap-10 sm:flex-row sm:items-start sm:gap-0">
                <div class="hidden sm:absolute sm:left-0 sm:right-0 sm:top-[0.6rem] sm:z-0 sm:mx-[10%] sm:block sm:h-px sm:bg-slate-300"></div>
                <x-timeline-item year="2018" label="Réflexion et lancement du projet" />
                <x-timeline-item year="2019" label="Création officielle de la Clinique Juridique" :accent="true" />
                <x-timeline-item year="2021" label="Développement des activités et premières caravanes juridiques" />
                <x-timeline-item year="2023" label="Renforcement des partenariats et des ressources" :accent="true" />
                <x-timeline-item year="2024" label="Extension des services et digitalisation" />
            </div>
        </div>
    </section>

    {{-- ============ PARTENAIRES ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <x-eyebrow>Ils nous soutiennent</x-eyebrow>
        <h2 class="mt-2 text-3xl font-bold text-slate-900">Nos partenaires institutionnels</h2>

        <div class="mt-8">
            @include('partials.partners-grid')
        </div>
    </section>

    {{-- ============ INLINE CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            icon="users"
            title="Une clinique au service de tous"
            button-label="Faire une pré-saisine"
            :button-href="route('presaisine.index')"
        >
            Vous avez une question juridique ? Besoin d'une orientation ? Notre équipe est là pour vous accompagner.
        </x-inline-cta>
    </section>

@endsection
