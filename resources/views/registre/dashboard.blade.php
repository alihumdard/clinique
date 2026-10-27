<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="referrer" content="no-referrer">
    <title>Registre numérique - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-100">

<header class="bg-slate-900">
    <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <div class="flex items-center gap-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-800 text-amber-400">
                <x-icon name="lock-shield" class="h-5 w-5" />
            </span>
            <span class="leading-tight">
                <span class="block text-sm font-bold uppercase tracking-wider text-amber-400">Registre numérique</span>
                <span class="block text-xs text-slate-400">Clinique Juridique FSJP - UAM</span>
            </span>
        </div>

        <div class="flex items-center gap-3">
            <span class="hidden text-right leading-tight sm:block">
                <span class="block text-sm font-semibold text-white">{{ $user->name }}</span>
                <span class="block text-xs text-slate-400">{{ $user->roleLabel() }}</span>
            </span>

            <form method="POST" action="{{ route('registre.logout') }}">
                @csrf
                <button type="submit" class="rounded-lg border border-slate-700 px-3 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800">
                    Se déconnecter
                </button>
            </form>
        </div>
    </div>
</header>

<main class="mx-auto max-w-5xl px-4 py-8 sm:px-6">

    <h1 class="text-2xl font-bold text-slate-900">Tableau de bord</h1>
    <p class="mt-1.5 text-sm text-slate-600">
        Bienvenue, {{ $user->name }}.
    </p>

    {{--
        Said plainly rather than shown as empty tables: the dossier screens
        are the rest of module 3, and two of their rules are not ours to
        write. Nothing here invents a status or a right.
    --}}
    <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-5">
        <h2 class="flex items-center gap-2 text-sm font-bold text-amber-900">
            <x-icon name="info" class="h-5 w-5" />
            Module 3 en cours de développement
        </h2>

        <p class="mt-2 text-sm leading-relaxed text-amber-900">
            La fondation du registre est en place : base de données distincte, comptes
            distincts, session limitée à trente minutes. Les écrans de gestion des
            dossiers suivent.
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
                Ce registre utilise sa propre base de données et ses propres comptes.
                Un compte du site public n'ouvre pas cet espace, et aucune donnée de
                dossier n'est enregistrée dans la base du site public.
            </p>
        </div>
    </div>

</main>

</body>
</html>
