@extends('layouts.registre')

@section('title', 'Tableau de bord')

@section('content')

    <h1 class="text-2xl font-bold text-slate-900">Tableau de bord</h1>
    <p class="mt-1.5 text-sm text-slate-600">Bienvenue, {{ $user->name }}.</p>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $tiles = [
                ['Demandes en attente', $pendingSubmissions, 'mail', route('registre.submissions', ['statut' => 'en_attente'])],
                ['Dossiers ouverts', $openDossiers, 'folder', null],
                ['Dossiers en retard', $overdueDossiers, 'clock', null],
                ['Demandes reçues', $totalSubmissions, 'chart-bar', route('registre.submissions')],
            ];
        @endphp

        @foreach ($tiles as [$label, $value, $icon, $href])
            <{{ $href ? 'a' : 'div' }} @if($href) href="{{ $href }}" @endif class="rounded-xl border border-slate-200 bg-white p-5 {{ $href ? 'transition hover:border-slate-300 hover:shadow-sm' : '' }}">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                    <x-icon :name="$icon" class="h-5 w-5" />
                </span>
                <p class="mt-3 text-2xl font-bold text-slate-900">{{ $value }}</p>
                <p class="text-sm text-slate-500">{{ $label }}</p>
            </{{ $href ? 'a' : 'div' }}>
        @endforeach
    </div>

    {{--
        Said plainly rather than shown as empty tables: the dossier
        screens are the rest of module 3, and two of their rules are not
        ours to write. Nothing here invents a status or a right.
    --}}
    <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-5">
        <h2 class="flex items-center gap-2 text-sm font-bold text-amber-900">
            <x-icon name="info" class="h-5 w-5" />
            Module 3 en cours de développement
        </h2>

        <p class="mt-2 text-sm leading-relaxed text-amber-900">
            La pré-saisine, le transfert vers le registre et la gestion des pièces jointes
            fonctionnent. Les écrans de consultation et de suivi des dossiers suivent.
        </p>

        <p class="mt-3 text-sm leading-relaxed text-amber-900">
            Deux éléments restent attendus de la Clinique avant que le cycle de vie des
            dossiers puisse être écrit : les <strong>libellés exacts des treize statuts</strong>
            et leurs transitions autorisées, et la <strong>matrice des droits</strong> des
            quatre profils. Le plan demande expressément de ne pas les inventer.
        </p>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-bold text-slate-900">Votre profil</h2>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Nom</dt>
                    <dd class="font-medium text-slate-900">{{ $user->name }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Rôle</dt>
                    <dd class="font-medium text-slate-900">{{ $user->roleLabel() }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Adresse</dt>
                    <dd class="truncate font-medium text-slate-900">{{ $user->email }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-bold text-slate-900">Séparation des données</h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-600">
                Ce registre utilise sa propre base de données et ses propres comptes. Un compte
                du site public n'ouvre pas cet espace, et aucune donnée de dossier — y compris
                les pré-saisines reçues — n'est enregistrée dans la base du site public.
            </p>
        </div>
    </div>

@endsection
