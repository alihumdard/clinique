@extends('layouts.app')

@section('title', 'Processus de consultation - ' . config('app.name'))
@section('meta_description', "La Clinique Juridique FSJP-UAM vous accompagne tout au long de votre demarche, depuis votre premiere prise de contact jusqu'au suivi de votre dossier.")

@section('content')

    <x-page-hero
        image="placeholders/process-hero"
        imagePosition="object-right-top"
        :title="__('Processus de consultation')"
        subtitle="De la première prise de contact à la réponse écrite — un processus simple, gratuit et confidentiel."
        :breadcrumb="[
            ['label' => __('Accueil'), 'url' => route('home')],
            ['label' => __('Processus de consultation')],
        ]"
    />

    {{-- ============ ETAPES + A RETENIR ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <x-eyebrow>Comment ça marche ?</x-eyebrow>
        <h2 class="mt-2 text-2xl font-bold text-slate-900">Les quatre étapes de votre consultation</h2>
        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600">
            La Clinique vous accompagne de votre première prise de contact jusqu'à la réponse écrite validée
            par un encadrant. Chaque étape est gratuite et confidentielle.
        </p>

        <div class="mt-6 grid grid-cols-1 items-start gap-6 lg:grid-cols-[1fr_260px]">
            @php
                $timelineSteps = [
                    ['number' => '1', 'icon' => 'document', 'title' => 'Pré-saisine', 'duration' => 'En ligne ou sur place', 'text' => 'Vous décrivez votre situation via le formulaire en ligne ou directement à l\'accueil pendant les permanences (mardi et jeudi, 8 h 30 – 18 h 00).'],
                    ['number' => '2', 'icon' => 'users', 'title' => 'Entretien de consultation', 'duration' => '30 à 60 minutes', 'text' => 'Un rendez-vous est fixé dans nos locaux à la FSJP. Un étudiant clinicien encadré recueille toutes les informations nécessaires à l\'étude de votre dossier.'],
                    ['number' => '3', 'icon' => 'search', 'title' => 'Analyse juridique', 'duration' => 'Sous 5 jours ouvrables', 'text' => 'Votre dossier est étudié par l\'équipe et validé par un encadrant (enseignant-chercheur ou professionnel du droit). Aucune réponse n\'est transmise sans cette validation.'],
                    ['number' => '4', 'icon' => 'check-circle', 'title' => 'Réponse écrite et orientation', 'duration' => 'Sous 5 jours ouvrables', 'text' => 'Vous recevez une réponse écrite : avis juridique, démarches à suivre, ou orientation vers un partenaire ou une structure compétente si nécessaire.'],
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
                    <x-checklist-item icon="gift" title="Entièrement gratuit">
                        Aucun frais n'est demandé, à aucune étape du processus.
                    </x-checklist-item>
                    <x-checklist-item icon="users" title="Ouvert à tous">
                        Toute personne confrontée à une difficulté juridique peut nous solliciter.
                    </x-checklist-item>
                    <x-checklist-item icon="lock" title="Confidentiel">
                        Vos informations personnelles et juridiques sont protégées.
                    </x-checklist-item>
                    <x-checklist-item icon="check-circle" title="Validé par un encadrant">
                        Chaque réponse est contrôlée par un enseignant-chercheur ou un professionnel du droit.
                    </x-checklist-item>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ URGENCES ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 rounded-xl border border-rose-100 bg-rose-50/60 p-2 lg:grid-cols-2 lg:items-center lg:gap-0 lg:p-0">
            <x-picture
                path="placeholders/scales-gavel-engagement"
                alt="Balance de la justice et marteau de juge"
                width="700" height="400"
                class="aspect-[16/10] w-full rounded-lg object-cover lg:aspect-auto lg:h-full lg:rounded-none lg:rounded-l-xl"
            />

            <div class="p-4 lg:p-8">
                <x-eyebrow>{{ __('Urgences') }}</x-eyebrow>
                <h2 class="mt-2 text-xl font-bold leading-tight text-slate-900 sm:text-2xl">Situation urgente ? Nous intervenons sous 72 heures</h2>
                <p class="mt-2.5 max-w-md text-sm leading-relaxed text-slate-600">
                    Pour les situations nécessitant une réponse immédiate — garde à vue, audience imminente,
                    expulsion, violence conjugale — signalez l'urgence lors de votre prise de contact.
                    La Clinique mobilise son équipe en priorité et vous répond sous 72 heures.
                </p>

                <div class="mt-4 flex flex-wrap items-center gap-5">
                    <x-inline-icon-label icon="gavel" label="Garde à vue" />
                    <x-inline-icon-label icon="document" label="Audience imminente" />
                    <x-inline-icon-label icon="heart" label="Violence conjugale" />
                    <x-inline-icon-label icon="users" label="Expulsion" />
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
            :title="__('Prêt à nous soumettre votre situation ?')"
            :button-label="__('Faire une pré-saisine')"
            :button-href="route('presaisine.index')"
        >
            La pré-saisine est gratuite et prend quelques minutes. Notre équipe vous répondra sous cinq jours ouvrables.
        </x-inline-cta>
    </section>

@endsection
