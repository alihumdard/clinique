@extends('layouts.app')

@section('title', 'Processus de consultation - ' . config('app.name'))
@section('meta_description', "La Clinique Juridique FSJP-UAM vous accompagne tout au long de votre demarche, depuis votre premiere prise de contact jusqu'au suivi de votre dossier.")

@section('content')

    <x-page-hero
        image="placeholders/process-hero"
        title="Processus de consultation"
        subtitle="Un accompagnement simple, transparent et gratuit pour tous."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Processus de consultation'],
        ]"
    />

    {{-- ============ ETAPES + A RETENIR ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-start gap-10 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-eyebrow>Comment ça marche ?</x-eyebrow>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Les étapes de votre consultation</h2>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600">
                    La Clinique Juridique FSJP-UAM vous accompagne tout au long de votre démarche, depuis votre
                    première prise de contact jusqu'au suivi de votre dossier.
                </p>

                <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <x-process-step boxed number="1" icon="document" title="Orientation initiale" duration="15 à 30 minutes">
                        Nous analysons votre situation et vous orientons vers le service approprié.
                    </x-process-step>
                    <x-process-step boxed number="2" icon="users" title="Entretien" duration="30 à 60 minutes">
                        Rencontre avec nos étudiants encadrés pour comprendre votre situation.
                    </x-process-step>
                    <x-process-step boxed number="3" icon="search" title="Analyse juridique" duration="2 à 5 jours">
                        Étude de votre dossier et recherche des solutions adaptées.
                    </x-process-step>
                    <x-process-step boxed number="4" icon="document" title="Orientation / Suivi" duration="selon le cas">
                        Recommandations et accompagnement vers les structures compétentes si nécessaire.
                    </x-process-step>
                </div>
            </div>

            <div class="rounded-xl bg-primary-50/60 p-6">
                <div class="flex items-center gap-2">
                    <x-icon-badge size="sm"><x-icon name="shield" class="h-5 w-5" /></x-icon-badge>
                    <h3 class="text-base font-bold text-slate-900">À retenir</h3>
                </div>

                <div class="mt-6 space-y-5">
                    <x-checklist-item icon="gift" title="Service gratuit">
                        Aucun frais n'est demandé pour les consultations.
                    </x-checklist-item>
                    <x-checklist-item icon="users" title="Accessibilité">
                        Ouvert à tous, sans condition de revenu.
                    </x-checklist-item>
                    <x-checklist-item icon="lock" title="Confidentialité">
                        Vos informations sont protégées.
                    </x-checklist-item>
                    <x-checklist-item icon="heart" title="Accompagnement">
                        Par des étudiants encadrés par des enseignants et professionnels du droit.
                    </x-checklist-item>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ NOTRE ENGAGEMENT ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 rounded-xl border border-slate-200 bg-white p-2 lg:grid-cols-2 lg:items-center lg:gap-0 lg:p-0">
            <x-picture
                path="placeholders/about-legal"
                alt="Balance de la justice et marteau de juge"
                width="700" height="500"
                class="aspect-[4/3] w-full rounded-lg object-cover lg:aspect-auto lg:h-full lg:rounded-none lg:rounded-l-xl"
            />

            <div class="p-4 lg:p-10">
                <x-eyebrow>Notre engagement</x-eyebrow>
                <h2 class="mt-2 text-2xl font-bold leading-tight text-slate-900">Une justice plus proche</h2>
                <p class="mt-3 max-w-md text-sm leading-relaxed text-slate-600">
                    La Clinique Juridique FSJP-UAM s'engage à rendre le droit plus accessible et à renforcer
                    la culture juridique au Niger, à travers un accompagnement de proximité, humain et de qualité.
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-5">
                    <x-inline-icon-label icon="ear" label="Écoute" />
                    <x-inline-icon-label icon="chat" label="Conseil" />
                    <x-inline-icon-label icon="heart" label="Accompagnement" />
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FAQ + DOCUMENTS ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-action-note-card icon="help-circle" title="Questions fréquentes">
                <p>Vous avez d'autres questions sur notre processus de consultation ? Consultez notre FAQ pour plus d'informations.</p>
                <x-button href="{{ route('services') }}#faq" variant="primary" class="mt-4">Voir toutes les questions</x-button>
            </x-action-note-card>

            <x-action-note-card icon="download" title="Documents utiles">
                <p>Téléchargez nos guides et fiches pratiques pour mieux comprendre vos droits et nos services.</p>
                <ul class="mt-4 space-y-2.5">
                    @foreach ([
                        'Guide de la consultation juridique',
                        'Vos droits en matière de justice',
                        'Fiche pratique : droits de la famille',
                    ] as $doc)
                        <li>
                            <a href="{{ route('services.eligibility') }}" class="flex items-center gap-2 text-sm font-medium text-primary-600 hover:text-primary-700">
                                <x-icon name="document" class="h-4 w-4 shrink-0" />
                                {{ $doc }}
                                <span class="text-xs font-normal text-slate-400">(PDF)</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </x-action-note-card>
        </div>
    </section>

    {{-- ============ CTA (dark) ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            title="Besoin d'une assistance juridique ?"
            button-label="Faire une pré-saisine"
            :button-href="route('presaisine.index')"
        >
            Remplissez notre formulaire de pré-saisine pour être rapidement orienté vers le bon service.
        </x-inline-cta>
    </section>

@endsection
