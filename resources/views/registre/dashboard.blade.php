@extends('layouts.registre')

@section('title', 'Tableau de bord')

@section('content')

    <x-admin.page-header
        title="Tableau de bord"
        :lead="'Bienvenue, ' . $user->name . '.'"
        :breadcrumb="[['label' => 'Registre numérique']]"
    />

    {{-- 4 stat cards — ref style: horizontal, icon left, arrow right --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Total dossiers --}}
        <a href="{{ route('registre.dossiers') }}" class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-5 transition hover:border-primary-300 hover:shadow-sm">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <x-icon name="folder" class="h-6 w-6" />
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-slate-500">Dossiers ouverts</p>
                <p class="mt-0.5 text-2xl font-bold text-slate-900">{{ $openDossiers }}</p>
            </div>
            <x-icon name="arrow-right" class="h-4 w-4 shrink-0 text-slate-400" />
        </a>

        {{-- Demandes en attente --}}
        <a href="{{ route('registre.submissions', ['statut' => 'en_attente']) }}" class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-5 transition hover:border-amber-300 hover:shadow-sm">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                <x-icon name="mail" class="h-6 w-6" />
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-slate-500">Demandes en attente</p>
                <p class="mt-0.5 text-2xl font-bold text-slate-900">{{ $pendingSubmissions }}</p>
                <p class="text-xs text-slate-400">à examiner</p>
            </div>
            <x-icon name="arrow-right" class="h-4 w-4 shrink-0 text-slate-400" />
        </a>

        {{-- Dossiers en retard --}}
        <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-5">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $overdueDossiers ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }}">
                <x-icon name="clock" class="h-6 w-6" />
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-slate-500">Dossiers en retard</p>
                <p class="mt-0.5 text-2xl font-bold {{ $overdueDossiers ? 'text-red-600' : 'text-slate-900' }}">{{ $overdueDossiers }}</p>
                <p class="text-xs text-slate-400">{{ $overdueDossiers ? 'échéance dépassée' : 'rien en retard' }}</p>
            </div>
        </div>

        {{-- Demandes reçues --}}
        <a href="{{ route('registre.submissions') }}" class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-5 transition hover:border-purple-300 hover:shadow-sm">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                <x-icon name="chart-bar" class="h-6 w-6" />
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-slate-500">Demandes reçues</p>
                <p class="mt-0.5 text-2xl font-bold text-slate-900">{{ $totalSubmissions }}</p>
                <p class="text-xs text-slate-400">depuis le site public</p>
            </div>
            <x-icon name="arrow-right" class="h-4 w-4 shrink-0 text-slate-400" />
        </a>

    </div>

    {{-- Warning banner --}}
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

    {{-- Recent dossiers table --}}
    @if($recentDossiers->isNotEmpty())
    <div class="mt-5 overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h2 class="text-sm font-bold text-slate-900">Derniers dossiers</h2>
            <a href="{{ route('registre.dossiers') }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Voir tous les dossiers →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-3 text-left">N° Dossier</th>
                        <th class="px-5 py-3 text-left">Demandeur</th>
                        <th class="px-5 py-3 text-left">Domaine</th>
                        <th class="px-5 py-3 text-left">Statut</th>
                        <th class="px-5 py-3 text-left">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recentDossiers as $dossier)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-5 py-3">
                            <a href="{{ route('registre.dossiers.show', $dossier) }}" class="font-mono text-xs font-semibold text-primary-600 hover:text-primary-700">
                                {{ $dossier->reference }}
                            </a>
                        </td>
                        <td class="px-5 py-3 font-medium text-slate-900">{{ $dossier->applicant?->fullName() ?? '—' }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $dossier->legal_domain ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-medium text-primary-700">
                                {{ $dossier->status?->label ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-500">{{ $dossier->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Bottom section: shortcuts + profile --}}
    <div class="mt-5 grid grid-cols-1 items-stretch gap-5 lg:grid-cols-3">
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

        <aside class="flex flex-col gap-5">
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

            <section class="flex-1 rounded-xl border border-slate-200 bg-white p-5">
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
