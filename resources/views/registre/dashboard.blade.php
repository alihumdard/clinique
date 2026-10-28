@extends('layouts.registre')

@section('title', 'Tableau de bord')

@section('content')

    <x-admin.page-header
        title="Tableau de bord"
        :lead="'Bienvenue, ' . $user->name . '.'"
        :breadcrumb="[['label' => 'Registre numérique']]"
    />

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('registre.submissions', ['statut' => 'en_attente']) }}" class="block transition hover:-translate-y-0.5">
            <x-admin.stat-card
                icon="mail"
                color="amber"
                :value="$pendingSubmissions"
                label="Demandes en attente"
                note="à examiner"
            />
        </a>

        <x-admin.stat-card
            icon="folder"
            color="blue"
            :value="$openDossiers"
            label="Dossiers ouverts"
        />

        <x-admin.stat-card
            icon="clock"
            :color="$overdueDossiers ? 'red' : 'green'"
            :value="$overdueDossiers"
            label="Dossiers en retard"
            :note="$overdueDossiers ? 'échéance dépassée' : 'rien en retard'"
        />

        <a href="{{ route('registre.submissions') }}" class="block transition hover:-translate-y-0.5">
            <x-admin.stat-card
                icon="chart-bar"
                color="purple"
                :value="$totalSubmissions"
                label="Demandes reçues"
                note="depuis le site public"
            />
        </a>
    </div>

    {{--
        Said plainly rather than shown as empty tables: two of the rules
        the dossiers follow are not ours to write.
    --}}
    <div class="mt-5 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-5">
        <x-icon name="info" class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" />
        <div class="min-w-0 text-sm leading-relaxed text-amber-900">
            <p class="font-bold">Deux éléments attendus de la Clinique</p>
            <p class="mt-1">
                Les <strong>libellés des treize statuts</strong> et leurs transitions autorisées —
                ceux affichés aujourd'hui sont provisoires — et la <strong>matrice des droits</strong>
                des quatre profils, d'ici là tout compte actif accède à l'ensemble du registre.
                Le plan demande expressément de ne pas les inventer.
            </p>
        </div>
    </div>

    {{-- items-start: without it the shorter card stretches to match the
         taller column and ends in a band of empty white. --}}
    <div class="mt-5 grid grid-cols-1 items-start gap-5 lg:grid-cols-3">
        <section class="rounded-xl border border-slate-200 bg-white p-5 lg:col-span-2">
            <h2 class="text-sm font-bold text-slate-900">Par où commencer</h2>

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                @php
                    $shortcuts = [
                        ['Demandes en ligne', 'Examiner une pré-saisine et ouvrir un dossier', 'mail', route('registre.submissions')],
                        ['Dossiers', 'Rechercher, filtrer, exporter', 'folder', route('registre.dossiers')],
                        ['Nouveau dossier', 'Pour une personne reçue à la permanence', 'plus', route('registre.dossiers.create')],
                        ["Journal d'accès", 'Qui a consulté quoi, et quand', 'lock-shield', route('registre.journal')],
                    ];
                @endphp

                @foreach ($shortcuts as [$label, $text, $icon, $href])
                    <a href="{{ $href }}" class="flex items-start gap-3 rounded-lg border border-slate-200 p-3.5 transition hover:border-primary-300 hover:bg-primary-50/40">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600">
                            <x-icon :name="$icon" class="h-5 w-5" />
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-slate-900">{{ $label }}</span>
                            <span class="block text-xs leading-relaxed text-slate-500">{{ $text }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>

        <aside class="space-y-5">
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-bold text-slate-900">Votre profil</h2>
                <dl class="mt-3 space-y-2.5 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Nom</dt>
                        <dd class="font-medium text-slate-900">{{ $user->name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Profil</dt>
                        <dd class="font-medium text-slate-900">{{ $user->roleLabel() }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="shrink-0 text-slate-500">Adresse</dt>
                        <dd class="truncate font-medium text-slate-900">{{ $user->email }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="flex items-center gap-2 text-sm font-bold text-slate-900">
                    <x-icon name="lock-shield" class="h-4 w-4 text-primary-600" />
                    Séparation des données
                </h2>
                <p class="mt-3 text-sm leading-relaxed text-slate-600">
                    Ce registre utilise sa propre base de données et ses propres comptes. Un compte
                    du site public n'ouvre pas cet espace, et aucune donnée de dossier — y compris
                    les pré-saisines reçues — n'est enregistrée dans la base du site public.
                </p>
            </section>
        </aside>
    </div>

@endsection
