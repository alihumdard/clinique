<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- The administration area must never be indexed. --}}
    <meta name="robots" content="noindex, nofollow">
    <title>Connexion Administrateur - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-white lg:h-full lg:overflow-hidden">

{{--
    The two panels only share the viewport height from lg up, where they
    sit side by side. Below that they stack, so the document has to be
    allowed to grow and scroll: pinning the body to h-full with
    overflow-hidden put the password field and the submit button below
    the fold with no way to reach them on any viewport under 1024px.
--}}
<div class="flex min-h-full flex-col lg:h-full lg:flex-row">

    {{-- ============ BRAND PANEL ============ --}}
    <div class="relative overflow-hidden bg-primary-700 px-6 py-8 lg:w-1/2 lg:px-12 lg:py-10">

        <div class="absolute inset-0">
            <x-picture
                path="placeholders/about-hero"
                alt=""
                width="900" height="1000"
                :eager="true"
                class="h-full w-full object-cover opacity-20"
            />
            <div class="absolute inset-0 bg-gradient-to-br from-primary-700/95 via-primary-700/90 to-primary-900/95"></div>
        </div>

        <div class="relative flex h-full flex-col">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/brand/logo.png') }}" alt="Clinique Juridique FSJP - UAM" class="h-16 w-16 shrink-0 rounded-full object-cover" width="64" height="64">
                <span class="leading-tight text-white">
                    <span class="block text-lg font-bold tracking-wide">CLINIQUE JURIDIQUE</span>
                    <span class="block text-lg font-bold tracking-wide">FSJP - UAM</span>
                    <span class="mt-1 block text-xs text-primary-100">
                        {{ config('clinique.faculty') }}<br>
                        {{ config('clinique.university') }}
                    </span>
                </span>
            </div>

            <div class="mt-6 lg:mt-8">
                <h1 class="text-3xl font-bold leading-tight text-white lg:text-4xl">
                    Espace<br>Administration
                </h1>

                <p class="mt-3 max-w-md text-sm leading-relaxed text-primary-100">
                    Gérez les contenus, les candidatures, les actualités et toutes les activités
                    de la Clinique Juridique FSJP-UAM.
                </p>

                <span class="mt-4 block h-0.5 w-12 bg-white/40" aria-hidden="true"></span>

                @php
                    $capabilities = [
                        ['icon' => 'document', 'title' => 'Gestion des contenus', 'text' => 'Actualités, ressources, Web TV, etc.'],
                        ['icon' => 'users', 'title' => 'Suivi des candidatures', 'text' => 'Appels à candidatures et stages'],
                        ['icon' => 'calendar', 'title' => 'Gestion des activités', 'text' => 'Événements et caravanes juridiques'],
                        ['icon' => 'chart-bar', 'title' => 'Tableau de bord', 'text' => 'Statistiques et rapports'],
                    ];
                @endphp

                <ul class="mt-5 space-y-3">
                    @foreach ($capabilities as $capability)
                        <li class="flex items-start gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/15 text-white">
                                <x-icon :name="$capability['icon']" class="h-4 w-4" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-white">{{ $capability['title'] }}</p>
                                <p class="mt-0.5 text-xs text-primary-100">{{ $capability['text'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-6 lg:mt-auto lg:pt-6">
                <span class="block h-0.5 w-10 bg-amber-400" aria-hidden="true"></span>
                <p class="mt-3 text-xs uppercase tracking-[0.2em] text-primary-100">
                    Droit &nbsp;&bull;&nbsp; Justice &nbsp;&bull;&nbsp; Accès pour tous
                </p>
            </div>
        </div>
    </div>

    {{-- ============ FORM PANEL ============ --}}
    {{-- flex-1 with its own scrollbar is right beside the brand panel and
     wrong beneath it: stacked, it gets squeezed to nothing. --}}
    <div class="relative flex flex-col bg-slate-50 px-6 py-5 lg:flex-1 lg:overflow-y-auto lg:px-12">

        <div class="flex justify-end">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Retour au site
            </a>
        </div>

        <div class="flex flex-1 items-center justify-center py-5">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-sm sm:p-8">

                <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-primary-50">
                    <x-icon name="lock-shield" class="h-8 w-8 text-primary-600" />
                </span>

                <h2 class="mt-4 text-center text-xl font-bold text-slate-900">Connexion Administrateur</h2>
                <p class="mt-1.5 text-center text-sm leading-relaxed text-slate-600">
                    Accédez à votre espace d'administration de la Clinique Juridique FSJP-UAM.
                </p>

                @if ($errors->any())
                    <div role="alert" class="mt-4 flex items-start gap-3 rounded-lg border border-rose-200 bg-rose-50 p-4">
                        <x-icon name="help-circle" class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />
                        <div class="min-w-0 text-sm text-rose-700">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.attempt') }}" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700">Adresse e-mail</label>
                        <div class="relative mt-1.5">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <x-icon name="mail" class="h-5 w-5" />
                            </span>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                autocomplete="username"
                                required
                                autofocus
                                placeholder="votre@email.com"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                        </div>
                    </div>

                    <div>
                        <div class="flex items-baseline justify-between gap-3">
                            <label for="password" class="block text-sm font-semibold text-slate-700">Mot de passe</label>
                            <a href="{{ route('contact') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                Mot de passe oublié ?
                            </a>
                        </div>

                        <div class="relative mt-1.5">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <x-icon name="lock" class="h-5 w-5" />
                            </span>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                autocomplete="current-password"
                                required
                                placeholder="Votre mot de passe"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-11 pr-11 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >

                            <button
                                type="button"
                                data-password-toggle="password"
                                aria-label="Afficher le mot de passe"
                                aria-pressed="false"
                                hidden
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                            >
                                <x-icon name="eye" class="h-5 w-5" />
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            class="h-4 w-4 shrink-0 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                        >
                        <label for="remember" class="text-sm text-slate-600">Se souvenir de moi</label>
                    </div>

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 px-5 py-3.5 text-sm font-semibold text-white transition-colors hover:bg-primary-700"
                    >
                        <x-icon name="login" class="h-5 w-5" />
                        Se connecter
                    </button>
                </form>

                <div class="mt-5 flex items-center gap-4">
                    <span class="h-px flex-1 bg-slate-200"></span>
                    <span class="text-xs text-slate-400">ou</span>
                    <span class="h-px flex-1 bg-slate-200"></span>
                </div>

                <div class="mt-4 flex items-start gap-3 rounded-lg bg-primary-50/70 p-3.5">
                    <x-icon name="shield" class="mt-0.5 h-5 w-5 shrink-0 text-primary-600" />
                    <div class="min-w-0 text-sm">
                        <p class="font-semibold text-primary-700">Accès réservé aux administrateurs et membres autorisés</p>
                        <p class="mt-0.5 leading-relaxed text-slate-600">
                            Toute tentative d'accès non autorisée est interdite et peut faire l'objet de poursuites.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
