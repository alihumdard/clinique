@extends('layouts.app')

@section('title', 'Nos services juridiques - ' . config('app.name'))
@section('meta_description', "La Clinique Juridique FSJP-UAM offre un accompagnement, une information et une orientation juridique gratuite dans plusieurs domaines du droit.")

@section('content')

    <x-page-hero
        image="placeholders/about-hero"
        title="Nos services juridiques"
        subtitle="Une assistance juridique gratuite et accessible dans plusieurs domaines du droit, assurée par des étudiants encadrés par des enseignants et des professionnels."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Nos services juridiques'],
        ]"
    />

    {{-- ============ DOMAINES D'INTERVENTION ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:py-16 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-slate-900">Nos principaux domaines d'intervention</h2>
        <p class="mt-2 max-w-3xl text-base text-slate-600">
            La Clinique Juridique FSJP-UAM offre un accompagnement, une information et une orientation
            juridique gratuite dans plusieurs domaines du droit, en fonction des besoins de la population.
        </p>

        @php
            // x-service-card has its own palette names; the domain's tone
            // maps onto them.
            $cardColours = [
                'blue' => 'primary', 'green' => 'green', 'amber' => 'amber',
                'red' => 'red', 'purple' => 'purple', 'pink' => 'red', 'slate' => 'teal',
            ];
        @endphp

        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($domains as $domain)
                <x-service-card
                    :icon="$domain->icon ?? 'scale'"
                    :title="$domain->name"
                    :color="$cardColours[$domain->tone] ?? 'primary'"
                >
                    {{ $domain->plain_language }}
                </x-service-card>
            @endforeach

            {{-- Always last: the Clinic orients anyone whose situation is
                 not on the list, which is the point of saying so here. --}}
            <x-service-card icon="users" title="Autres domaines" color="teal">
                Orientation vers des services spécialisés selon la nature de votre situation.
            </x-service-card>
        </div>
    </section>

    {{-- ============ NOTRE ENGAGEMENT (photo banner) ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-12 sm:pb-16 sm:px-6 lg:px-8">
        <div class="relative min-h-[200px] overflow-hidden rounded-xl">
            <x-picture
                path="placeholders/services-engagement"
                alt="Avocat en consultation avec un client"
                width="2172" height="724"
                class="absolute inset-0 h-full w-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-navy-950/90 via-navy-900/50 to-navy-900/10 lg:bg-gradient-to-r"></div>

            <div class="relative flex h-full flex-col gap-6 px-6 py-8 sm:px-10 sm:py-10 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-lg">
                    <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-amber-400">
                        <span aria-hidden="true">&mdash;</span> Notre engagement
                    </p>
                    <h2 class="mt-2 text-2xl font-bold leading-tight text-white sm:text-3xl">Une justice plus proche des citoyens</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-200">
                        Nous mettons nos connaissances juridiques au service de la population pour offrir
                        une assistance gratuite, humaine et de qualité, dans le respect de la confidentialité
                        et de l'éthique professionnelle.
                    </p>
                </div>

                <x-button :href="route('presaisine.index')" variant="primary" class="shrink-0 self-start lg:self-end !bg-white !text-primary-700 hover:!bg-slate-100">
                    Faire une pré-saisine
                </x-button>
            </div>
        </div>
    </section>

    {{-- ============ PROCESSUS (4 steps) ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-12 sm:pb-16 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-slate-900">Comment bénéficier de nos services ?</h2>
        <p class="mt-2 text-base text-slate-600">Un processus simple, transparent et gratuit pour tous.</p>

        <div class="mt-8 grid grid-cols-1 gap-8 sm:grid-cols-2 sm:gap-4 lg:flex lg:items-start">
            <x-process-step number="1" icon="document" title="Orientation initiale">
                Nous analysons votre situation et vous orientons vers le service approprié.
            </x-process-step>
            <div class="hidden items-center justify-center text-slate-300 lg:flex">
                <x-icon name="arrow-right" class="h-5 w-5" />
            </div>
            <x-process-step number="2" icon="users" title="Entretien" accent>
                Rencontre avec nos étudiants encadrés pour comprendre votre situation.
            </x-process-step>
            <div class="hidden items-center justify-center text-slate-300 lg:flex">
                <x-icon name="arrow-right" class="h-5 w-5" />
            </div>
            <x-process-step number="3" icon="search" title="Analyse juridique">
                Étude de votre dossier et recherche des solutions adaptées.
            </x-process-step>
            <div class="hidden items-center justify-center text-slate-300 lg:flex">
                <x-icon name="arrow-right" class="h-5 w-5" />
            </div>
            <x-process-step number="4" icon="document" title="Orientation / Suivi">
                Recommandations, accompagnement et orientation vers les structures compétentes si nécessaire.
            </x-process-step>
        </div>

        {{-- Info notes --}}
        <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-info-note-card icon="clock" title="Nos services sont gratuits" color="blue">
                La Clinique Juridique FSJP-UAM offre une assistance juridique gratuite à la population, dans
                le cadre de sa mission de service public et de formation pratique des étudiants.
            </x-info-note-card>
            <x-info-note-card icon="users" title="Qui peut en bénéficier ?" color="amber">
                Toute personne résidant au Niger, sans discrimination, peut bénéficier de nos services, en
                particulier les personnes en situation de vulnérabilité.
            </x-info-note-card>
        </div>
    </section>

    {{-- ============ FAQ ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-12 sm:pb-16 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Questions fréquentes</h2>
                <p class="mt-1 text-sm text-slate-600">Trouvez rapidement les réponses aux questions les plus courantes sur nos services.</p>
            </div>
            <x-button :href="route('contact')" variant="text" class="self-start shrink-0 sm:self-auto">Voir toutes les questions</x-button>
        </div>

        <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
            <x-faq-item question="Les services de la Clinique sont-ils vraiment gratuits ?">
                Oui, l'ensemble de nos services de consultation, d'orientation et d'accompagnement juridique
                sont entièrement gratuits, sans aucune exception.
            </x-faq-item>
            <x-faq-item question="Faut-il être étudiant pour bénéficier des services ?">
                Non, nos services sont ouverts à toute personne résidant au Niger, qu'elle soit étudiante ou non.
            </x-faq-item>
            <x-faq-item question="Quels types de problèmes juridiques pouvez-vous traiter ?">
                Nous traitons les questions de droit de la famille, droit civil, droit du travail, droit
                administratif, droits humains et bien d'autres domaines selon votre situation.
            </x-faq-item>
            <x-faq-item question="Quels documents apporter lors de l'entretien ?">
                Munissez-vous de votre pièce d'identité et de tout document relatif à votre situation
                (contrats, courriers, décisions administratives, etc.).
            </x-faq-item>
            <x-faq-item question="Comment prendre rendez-vous ?">
                Vous pouvez effectuer une pré-saisine en ligne ou vous présenter directement à nos permanences
                aux horaires indiqués sur le site.
            </x-faq-item>
            <x-faq-item question="Que se passe-t-il après la consultation ?">
                Selon votre dossier, nos étudiants encadrés vous orientent vers un suivi juridique, un service
                partenaire, ou une structure compétente si nécessaire.
            </x-faq-item>
        </div>
    </section>

    {{-- ============ INLINE CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-12 sm:pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            icon="paper-plane"
            title="Vous avez besoin d'une assistance juridique ?"
            button-label="Faire une pré-saisine"
            :button-href="route('presaisine.index')"
        >
            Notre équipe est à votre écoute pour vous orienter et vous accompagner.
        </x-inline-cta>
    </section>

@endsection
