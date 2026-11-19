@extends('layouts.app')

@section('title', 'Nos domaines d\'intervention - ' . config('app.name'))
@section('meta_description', "La Clinique Juridique FSJP-UAM offre une information, une orientation et un accompagnement juridique gratuits dans plusieurs domaines du droit au Niger.")

@section('content')

    <x-page-hero
        image="placeholders/about-hero"
        imagePosition="object-right-top"
        :title="__('Nos domaines d\'intervention')"
        subtitle="Information, orientation et accompagnement juridiques gratuits, assurés par des étudiants cliniciens encadrés par des enseignants-chercheurs et des professionnels du droit."
        :breadcrumb="[
            ['label' => __('Accueil'), 'url' => route('home')],
            ['label' => __('Nos services')],
        ]"
    />

    {{-- ============ DOMAINES D'INTERVENTION ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:py-16 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-slate-900">Pour quelles questions venir à la Clinique ?</h2>
        <p class="mt-2 max-w-3xl text-base text-slate-600">
            La Clinique Juridique FSJP-UAM offre une information, une orientation et un accompagnement
            juridiques entièrement gratuits dans plusieurs domaines du droit. Si votre situation ne
            figure pas dans la liste, venez tout de même : nous vous orienterons vers le bon interlocuteur.
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
                {{-- No per-domain detail page yet (needs real content from
                     the Clinic) — :link="false" so the card doesn't offer
                     a button that goes nowhere useful. --}}
                <x-service-card
                    :icon="$domain->icon ?? 'scale'"
                    :title="$domain->name"
                    :color="$cardColours[$domain->tone] ?? 'primary'"
                    :link="false"
                >
                    {{ $domain->plain_language }}
                </x-service-card>
            @endforeach

            {{-- Always last: the Clinic orients anyone whose situation is
                 not on the list, which is the point of saying so here. --}}
            <x-service-card icon="users" title="Autres domaines" color="teal" :link="false">
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
                    <h2 class="mt-2 text-2xl font-bold leading-tight text-white sm:text-3xl">Gratuit, confidentiel, encadré par des professionnels</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-200">
                        Toutes nos consultations sont gratuites et confidentielles. Chaque dossier est
                        traité par un étudiant clinicien sous la supervision d'un enseignant-chercheur
                        ou d'un praticien du droit. Aucune réponse n'est transmise sans validation d'un encadrant.
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
        <h2 class="text-2xl font-bold text-slate-900">Comment être reçu à la Clinique ?</h2>
        <p class="mt-2 text-base text-slate-600">Un processus simple, gratuit et confidentiel, en quatre étapes.</p>

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
            <x-info-note-card icon="gift" title="Consultation entièrement gratuite" color="blue">
                Aucun frais n'est demandé, à aucune étape. La gratuité est un principe fondamental de la
                Clinique Juridique FSJP-UAM.
            </x-info-note-card>
            <x-info-note-card icon="users" title="Ouvert à toute personne" color="amber">
                Toute personne confrontée à une difficulté juridique peut nous solliciter, avec une
                attention particulière aux personnes en situation de vulnérabilité.
            </x-info-note-card>
        </div>
    </section>

    {{-- ============ FAQ ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-12 sm:pb-16 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">{{ __('Questions fréquentes') }}</h2>
                <p class="mt-1 text-sm text-slate-600">Trouvez rapidement les réponses aux questions les plus courantes sur nos services.</p>
            </div>
            <x-button :href="route('contact')" variant="text" class="self-start shrink-0 sm:self-auto">{{ __('Voir toutes les questions') }}</x-button>
        </div>

        <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
            <x-faq-item question="Les consultations sont-elles gratuites ?">
                Oui. L'ensemble de nos services — consultation, orientation, accompagnement — est
                entièrement gratuit. Aucun frais n'est demandé, à aucune étape.
            </x-faq-item>
            <x-faq-item question="Qui peut venir à la Clinique ?">
                Toute personne confrontée à une difficulté juridique peut nous solliciter, avec une
                attention particulière aux personnes en situation de vulnérabilité. L'accueil se tient
                uniquement à Niamey.
            </x-faq-item>
            <x-faq-item question="Quels domaines du droit couvrez-vous ?">
                Droit de la famille, droit civil, droit du travail, droit administratif, droit pénal,
                droits humains, état civil, nationalité et questions liées aux personnes réfugiées.
                Si votre situation ne figure pas dans la liste, contactez-nous.
            </x-faq-item>
            <x-faq-item question="Quels documents apporter lors de la consultation ?">
                Une pièce d'identité et tout document utile à votre situation : contrat, acte, courrier
                reçu, décision administrative. Si vous n'avez aucun document, venez tout de même.
            </x-faq-item>
            <x-faq-item question="Comment prendre contact ou déposer une demande ?">
                Vous pouvez effectuer une pré-saisine en ligne ou vous présenter directement lors de nos
                permanences, le mardi et le jeudi de 8 h 30 à 18 h 00 à la FSJP.
            </x-faq-item>
            <x-faq-item question="Combien de temps avant d'obtenir une réponse ?">
                Après une pré-saisine, vous recevez une réponse dans un délai de cinq jours ouvrables.
                Pour les situations urgentes (garde à vue, audience imminente), nous intervenons sous 72 heures.
            </x-faq-item>
        </div>
    </section>

    {{-- ============ INLINE CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-12 sm:pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            icon="paper-plane"
            :title="__('Vous avez une question juridique ?')"
            :button-label="__('Faire une pré-saisine')"
            :button-href="route('presaisine.index')"
        >
            Décrivez votre situation en ligne via la pré-saisine. Notre équipe vous répondra dans
            les cinq jours ouvrables. C'est gratuit.
        </x-inline-cta>
    </section>

@endsection
