@extends('layouts.app')

@section('title', 'Présentation de la Clinique - ' . config('app.name'))
@section('meta_description', "La Clinique Juridique FSJP-UAM est une structure universitaire créée au sein de la Faculté des Sciences Juridiques et Politiques de l'Université Abdou Moumouni de Niamey.")

@section('content')

    <x-page-hero
        image="placeholders/about-hero"
        imagePosition="object-right"
        :title="__('Présentation de la Clinique')"
        subtitle="Une structure universitaire d'assistance juridique gratuite, créée au sein de la FSJP-UAM pour rapprocher le droit des citoyens."
        :breadcrumb="[
            ['label' => __('Accueil'), 'url' => route('home')],
            ['label' => __('Présentation de la Clinique')],
        ]"
    />

    {{-- ============ PRESENTATION ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2">
            <div>
                <x-eyebrow>La Clinique en bref</x-eyebrow>
                <h2 class="mt-2 text-3xl font-bold leading-tight text-slate-900">
                    Une clinique juridique universitaire
                    <span class="block">au service de tous</span>
                </h2>
                <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">
                    La Clinique Juridique de la FSJP-UAM est une structure d'enseignement clinique du droit
                    créée en 2025 au sein de la Faculté des Sciences Juridiques et Politiques de l'Université
                    Abdou Moumouni de Niamey, par arrêté rectoral n° 00072 du 22 avril 2025. Elle offre une
                    assistance juridique gratuite à toute personne confrontée à une difficulté juridique,
                    assurée par des étudiants encadrés par des enseignants et des professionnels du droit.
                </p>
                <div class="mt-6">
                    <x-button href="#mission" variant="primary">{{ __('Notre mission') }}</x-button>
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
            <x-value-card icon="document" :title="__('Notre mission')">
                Offrir une assistance juridique gratuite et accessible, former les étudiants en droit par la
                pratique et promouvoir la culture des droits au sein de la communauté.
            </x-value-card>
            <x-value-card icon="search" title="Notre vision">
                Une société nigérienne plus juste, où chaque personne peut connaître et défendre ses droits,
                quelle que soit sa situation.
            </x-value-card>
            <x-value-card icon="scale" title="Nos valeurs">
                Gratuité, confidentialité, impartialité, professionnalisme et engagement au service de toutes
                les personnes vulnérables.
            </x-value-card>
        </div>
    </section>

    {{-- ============ HISTOIRE ============ --}}
    <section class="bg-slate-50 py-12 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2">
                <div>
                    <x-eyebrow color="muted">Notre histoire</x-eyebrow>
                    <h2 class="mt-2 text-3xl font-bold leading-tight text-slate-900">Naissance d'une initiative au service du droit</h2>
                    <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">
                        La Clinique Juridique FSJP-UAM est née de la volonté de la Faculté des Sciences Juridiques
                        et Politiques de l'Université Abdou Moumouni de contribuer activement à l'accès au droit
                        et à la promotion de la justice au Niger.
                    </p>
                    <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">
                        Créée le 22 avril 2025 par arrêté rectoral n° 00072, elle a lancé ses premières activités
                        avec le soutien de partenaires nationaux et internationaux, et poursuit son développement
                        à travers des consultations, des caravanes juridiques et la formation pratique des étudiants.
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
    <section class="bg-navy-900 py-12 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-eyebrow class="!text-amber-400">Quelques chiffres clés</x-eyebrow>
            <h2 class="mt-2 text-3xl font-bold text-white">Notre impact en chiffres</h2>

            <div class="mt-8 grid grid-cols-2 gap-5 lg:grid-cols-4">
                <x-stat-dark icon="gift" value="0 F CFA" label="Coût des consultations — entièrement gratuit" />
                <x-stat-dark icon="clock" value="5 jours" label="Délai de réponse pour les dossiers courants" />
                <x-stat-dark icon="document" value="72h" label="Délai de traitement pour les situations urgentes" />
                <x-stat-dark icon="users" value="2025" label="Année de création de la Clinique Juridique" />
            </div>
        </div>
    </section>

    {{-- ============ CADRE INSTITUTIONNEL ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:py-16 sm:px-6 lg:px-8">
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
                    La Clinique Juridique FSJP-UAM a été créée le 22 avril 2025 par arrêté rectoral n° 00072
                    de l'Université Abdou Moumouni de Niamey, dans le cadre de la promotion de l'enseignement
                    clinique du droit et de l'engagement social de la faculté.
                </p>

                <div class="mt-6 flex flex-col items-start gap-4 rounded-lg border border-primary-100 bg-primary-50/60 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <x-icon-badge size="sm"><x-icon name="document" class="h-5 w-5" /></x-icon-badge>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Arrêté rectoral n° 00072 du 22 avril 2025</p>
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
    <section class="bg-slate-50 py-12 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-eyebrow color="muted">Les grandes étapes</x-eyebrow>
            <h2 class="mt-2 text-3xl font-bold text-slate-900">Notre parcours</h2>

            <div class="relative mt-10 flex flex-col gap-10 sm:flex-row sm:items-start sm:gap-0">
                <div class="hidden sm:absolute sm:left-0 sm:right-0 sm:top-[0.6rem] sm:z-0 sm:mx-[10%] sm:block sm:h-px sm:bg-slate-300"></div>
                <x-timeline-item year="2025" label="Création officielle de la Clinique par arrêté rectoral n° 00072" :accent="true" />
                <x-timeline-item year="2025" label="Signature des conventions de partenariat avec HCR, IDLO et Affaires mondiales Canada" />
                <x-timeline-item year="2025" label="Premières caravanes juridiques avec IDLO dans les communes de Niamey" :accent="true" />
                <x-timeline-item year="2026" label="Lancement du site web et ouverture des permanences (mardi et jeudi)" />
            </div>
        </div>
    </section>

    {{-- ============ PARTENAIRES ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:py-16 sm:px-6 lg:px-8">
        <x-eyebrow>{{ __('Ils nous soutiennent') }}</x-eyebrow>
        <h2 class="mt-2 text-3xl font-bold text-slate-900">{{ __('Nos partenaires institutionnels') }}</h2>

        <div class="mt-8">
            @include('partials.partners-grid')
        </div>
    </section>

    {{-- ============ INLINE CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            icon="users"
            :title="__('Vous avez une question juridique ?')"
            :button-label="__('Faire une pré-saisine')"
            :button-href="route('presaisine.index')"
        >
            Venez nous rencontrer lors de nos permanences, le mardi et le jeudi de 8 h 30 à 18 h 00 à la FSJP.
            Les consultations sont entièrement gratuites.
        </x-inline-cta>
    </section>

@endsection
