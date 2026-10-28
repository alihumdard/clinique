@extends('layouts.app')

@section('title', 'Processus de consultation - ' . config('app.name'))
@section('meta_description', "La Clinique Juridique FSJP-UAM vous accompagne tout au long de votre demarche, depuis votre premiere prise de contact jusqu'au suivi de votre dossier.")

@section('content')

    <x-page-hero
        image="placeholders/process-hero"
        :title="__('Processus de consultation')"
        subtitle="Un accompagnement simple, transparent et gratuit pour tous."
        :breadcrumb="[
            ['label' => __('Accueil'), 'url' => route('home')],
            ['label' => __('Processus de consultation')],
        ]"
    />

    {{-- ============ ETAPES + A RETENIR ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <x-eyebrow>Comment ça marche ?</x-eyebrow>
        <h2 class="mt-2 text-2xl font-bold text-slate-900">Les étapes de votre consultation</h2>
        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600">
            La Clinique Juridique FSJP-UAM vous accompagne tout au long de votre démarche, depuis votre
            première prise de contact jusqu'au suivi de votre dossier.
        </p>

        <div class="mt-6 grid grid-cols-1 items-start gap-6 lg:grid-cols-[1fr_260px]">
            @php
                $timelineSteps = [
                    ['number' => '1', 'icon' => 'document', 'title' => 'Orientation initiale', 'duration' => '15 à 30 minutes', 'text' => 'Nous analysons votre situation et vous orientons vers le service approprié.'],
                    ['number' => '2', 'icon' => 'users', 'title' => 'Entretien', 'duration' => '30 à 60 minutes', 'text' => 'Rencontre avec nos étudiants encadrés pour comprendre votre situation.'],
                    ['number' => '3', 'icon' => 'search', 'title' => 'Analyse juridique', 'duration' => '2 à 5 jours', 'text' => 'Étude de votre dossier et recherche des solutions adaptées.'],
                    ['number' => '4', 'icon' => 'document', 'title' => 'Orientation / Suivi', 'duration' => 'selon le cas', 'text' => 'Recommandations et accompagnement vers les structures compétentes si nécessaire.'],
                ];
            @endphp

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ($timelineSteps as $step)
                    <div class="flex flex-col rounded-lg border border-primary-100 bg-primary-50/60 p-5">
                        <div class="flex items-center gap-2">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white">
                                {{ $step['number'] }}
                            </span>
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-primary-600">
                                <x-icon :name="$step['icon']" class="h-4 w-4" />
                            </span>
                        </div>
                        <p class="mt-3.5 text-sm font-semibold text-slate-900">{{ $step['title'] }}</p>
                        <p class="mt-1.5 text-xs leading-relaxed text-slate-600">{{ $step['text'] }}</p>
                        <p class="mt-3.5 flex items-center gap-1.5 text-xs text-slate-500">
                            <x-icon name="clock" class="h-3.5 w-3.5 shrink-0" />
                            Durée : {{ $step['duration'] }}
                        </p>
                    </div>
                @endforeach
            </div>

            <div class="rounded-xl bg-primary-50/60 p-5 lg:-mt-6">
                <div class="flex items-center gap-2">
                    <x-icon-badge size="sm"><x-icon name="shield" class="h-5 w-5" /></x-icon-badge>
                    <h3 class="text-base font-bold text-slate-900">À retenir</h3>
                </div>

                <div class="mt-4 space-y-3.5">
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
        <div class="grid grid-cols-1 gap-6 rounded-xl border border-primary-100 bg-primary-50/60 p-2 lg:grid-cols-2 lg:items-center lg:gap-0 lg:p-0">
            <x-picture
                path="placeholders/scales-gavel-engagement"
                alt="Balance de la justice et marteau de juge"
                width="700" height="400"
                class="aspect-[16/10] w-full rounded-lg object-cover lg:aspect-auto lg:h-full lg:rounded-none lg:rounded-l-xl"
            />

            <div class="p-4 lg:p-8">
                <x-eyebrow>{{ __('Notre engagement') }}</x-eyebrow>
                <h2 class="mt-2 text-xl font-bold leading-tight text-slate-900 sm:text-2xl">Une justice plus proche</h2>
                <p class="mt-2.5 max-w-md text-sm leading-relaxed text-slate-600">
                    La Clinique Juridique FSJP-UAM s'engage à rendre le droit plus accessible et à renforcer
                    la culture juridique au Niger, à travers un accompagnement de proximité, humain et de qualité.
                </p>

                <div class="mt-4 flex flex-wrap items-center gap-5">
                    <x-inline-icon-label icon="headset" label="Écoute" />
                    <x-inline-icon-label icon="chat" label="Conseil" />
                    <x-inline-icon-label icon="heart" label="Accompagnement" />
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FAQ + DOCUMENTS ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-action-note-card icon="help-circle" :title="__('Questions fréquentes')">
                <p>Vous avez d'autres questions sur notre processus de consultation ? Consultez notre FAQ pour plus d'informations.</p>
                <x-button href="{{ route('services') }}#faq" variant="primary" class="mt-4">{{ __('Voir toutes les questions') }}</x-button>
            </x-action-note-card>

            <x-action-note-card icon="download" title="Documents utiles">
                <p>Téléchargez nos guides et fiches pratiques pour mieux comprendre vos droits et nos services.</p>
                <ul class="mt-4 space-y-2.5">
                    @forelse ($documents as $doc)
                        <li>
                            <a href="{{ route('documents.guide', $doc) }}" class="flex items-center gap-2 text-sm font-medium text-primary-600 hover:text-primary-700">
                                <x-icon name="document" class="h-4 w-4 shrink-0" />
                                {{ $doc->title }}
                                <span class="text-xs font-normal text-slate-400">(PDF)</span>
                            </a>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">Aucun document disponible pour le moment.</li>
                    @endforelse
                </ul>
            </x-action-note-card>
        </div>
    </section>

    {{-- ============ CTA (dark) ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            :title="__('Besoin d\'une assistance juridique ?')"
            :button-label="__('Faire une pré-saisine')"
            :button-href="route('presaisine.index')"
        >
            Remplissez notre formulaire de pré-saisine pour être rapidement orienté vers le bon service.
        </x-inline-cta>
    </section>

@endsection
