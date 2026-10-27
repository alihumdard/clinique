<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Confidential area: never indexed, and no referrer leaks out. --}}
    <meta name="robots" content="noindex, nofollow">
    <meta name="referrer" content="no-referrer">
    <title>Registre numérique - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{--
    Slate rather than the Clinic's blue, on purpose: the Registre holds
    confidential dossiers and the CMS holds the website. Someone with both
    open should never have to read the title to know which one they are
    typing into.
--}}
<body class="flex h-full items-center justify-center bg-slate-900 px-4 py-10">

<main class="w-full max-w-md">

    <div class="flex items-center gap-3">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-slate-800 text-amber-400">
            <x-icon name="lock-shield" class="h-6 w-6" />
        </span>
        <span class="leading-tight">
            <span class="block text-sm font-bold uppercase tracking-wider text-amber-400">Registre numérique</span>
            <span class="block text-xs text-slate-400">Clinique Juridique FSJP - UAM</span>
        </span>
    </div>

    <div class="mt-6 rounded-xl bg-white p-6 shadow-xl sm:p-8">
        <h1 class="text-xl font-bold text-slate-900">Connexion</h1>
        <p class="mt-1.5 text-sm leading-relaxed text-slate-500">
            Espace réservé au personnel habilité de la Clinique. Les comptes du site
            public ne donnent pas accès à cet espace.
        </p>

        <form method="POST" action="{{ route('registre.login.attempt') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700">Adresse e-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                    class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700">Mot de passe</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                >
            </div>

            @error('email')
                <p id="email-error" role="alert" class="rounded-md bg-rose-50 px-3 py-2 text-sm text-rose-700">
                    {{ $message }}
                </p>
            @enderror

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-slate-700 focus:ring-slate-500">
                Rester connecté sur ce poste
            </label>

            <button type="submit" class="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                Se connecter
            </button>
        </form>
    </div>

    <p class="mt-5 text-center text-xs leading-relaxed text-slate-400">
        Toute consultation est enregistrée. La session se termine automatiquement
        après trente minutes d'inactivité.
    </p>

    <p class="mt-3 text-center text-xs text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-slate-300">Retour au site public</a>
    </p>

</main>

</body>
</html>
