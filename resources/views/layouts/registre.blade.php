<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Confidential area: never indexed, and no referrer leaks out. --}}
    <meta name="robots" content="noindex, nofollow">
    <meta name="referrer" content="no-referrer">
    <title>@yield('title', 'Registre numérique') - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-100">

@php $agent = auth('registre')->user(); @endphp

<header class="bg-slate-900">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <a href="{{ route('registre.dashboard') }}" class="flex items-center gap-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-800 text-amber-400">
                <x-icon name="lock-shield" class="h-5 w-5" />
            </span>
            <span class="leading-tight">
                <span class="block text-sm font-bold uppercase tracking-wider text-amber-400">Registre numérique</span>
                <span class="block text-xs text-slate-400">Clinique Juridique FSJP - UAM</span>
            </span>
        </a>

        <div class="flex items-center gap-3">
            <span class="hidden text-right leading-tight sm:block">
                <span class="block text-sm font-semibold text-white">{{ $agent?->name }}</span>
                <span class="block text-xs text-slate-400">{{ $agent?->roleLabel() }}</span>
            </span>

            <form method="POST" action="{{ route('registre.logout') }}">
                @csrf
                <button type="submit" class="rounded-lg border border-slate-700 px-3 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800">
                    Se déconnecter
                </button>
            </form>
        </div>
    </div>

    <nav class="border-t border-slate-800">
        <div class="mx-auto flex max-w-6xl gap-1 overflow-x-auto px-2 sm:px-4">
            @php
                $tabs = [
                    ['Tableau de bord', 'registre.dashboard'],
                    ['Demandes en ligne', 'registre.submissions'],
                ];
            @endphp

            @foreach ($tabs as [$label, $name])
                <a
                    href="{{ route($name) }}"
                    @if(Route::is($name . '*')) aria-current="page" @endif
                    class="whitespace-nowrap border-b-2 px-3 py-2.5 text-sm font-medium transition {{ Route::is($name . '*') ? 'border-amber-400 text-amber-400' : 'border-transparent text-slate-400 hover:text-slate-200' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </nav>
</header>

<main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
    {{-- registre_message, not registre: the idle-session middleware
         keeps registre.last_activity, which makes session('registre') an
         array rather than this message. --}}
    @if(session('registre_message'))
        <p role="status" class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('registre_message') }}
        </p>
    @endif

    @if($errors->any())
        <div role="alert" class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

</body>
</html>
