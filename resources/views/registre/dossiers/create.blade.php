@extends('layouts.registre')

@section('title', 'Nouveau dossier')

@section('content')

    <p class="text-sm text-slate-500">
        <a href="{{ route('registre.dossiers') }}" class="hover:text-slate-700">Dossiers</a>
        <span aria-hidden="true">&rsaquo;</span>
        <span class="text-slate-700">Nouveau dossier</span>
    </p>

    <h1 class="mt-3 text-2xl font-bold text-slate-900">Ouvrir un dossier</h1>
    <p class="mt-1.5 text-sm text-slate-600">
        Pour une personne reçue à la permanence. Une demande déposée en ligne s'ouvre
        depuis <a href="{{ route('registre.submissions') }}" class="font-medium text-primary-600 hover:underline">Demandes en ligne</a>.
    </p>

    <form method="POST" action="{{ route('registre.dossiers.store') }}" class="mt-6 max-w-3xl space-y-6">
        @csrf

        {{-- ============ DEMANDEUR ============ --}}
        <fieldset class="space-y-4 rounded-xl border border-slate-200 bg-white p-5">
            <legend class="px-1 text-sm font-bold text-slate-900">Demandeur</legend>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="last_name" class="block text-xs font-semibold text-slate-600">Nom <span class="text-rose-600">*</span></label>
                    <input type="text" id="last_name" name="last_name" required value="{{ old('last_name') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                </div>
                <div>
                    <label for="first_name" class="block text-xs font-semibold text-slate-600">Prénom</label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label for="phone" class="block text-xs font-semibold text-slate-600">Téléphone <span class="text-rose-600">*</span></label>
                    <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                </div>
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-600">Adresse e-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                </div>
                <div>
                    <label for="commune" class="block text-xs font-semibold text-slate-600">Commune</label>
                    <input type="text" id="commune" name="commune" value="{{ old('commune') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_uam_student" value="1" @checked(old('is_uam_student')) class="h-4 w-4 rounded border-slate-300 text-slate-700 focus:ring-slate-500">
                    Étudiant(e) de l'UAM
                </label>
                <div>
                    <label for="student_card_number" class="block text-xs font-semibold text-slate-600">Numéro de carte</label>
                    <input type="text" id="student_card_number" name="student_card_number" value="{{ old('student_card_number') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                </div>
            </div>
        </fieldset>

        {{-- ============ LA DEMANDE ============ --}}
        <fieldset class="space-y-4 rounded-xl border border-slate-200 bg-white p-5">
            <legend class="px-1 text-sm font-bold text-slate-900">La demande</legend>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label for="legal_domain" class="block text-xs font-semibold text-slate-600">Domaine <span class="text-rose-600">*</span></label>
                    <select id="legal_domain" name="legal_domain" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                        <option value="">Choisir</option>
                        @foreach ($domains as $domain)
                            <option value="{{ $domain->name }}" @selected(old('legal_domain') === $domain->name)>{{ $domain->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="urgency" class="block text-xs font-semibold text-slate-600">Urgence <span class="text-rose-600">*</span></label>
                    <select id="urgency" name="urgency" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                        @foreach (['normale' => 'Normale', 'elevee' => 'Élevée', 'urgente' => 'Urgente'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('urgency', 'normale') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="opened_at" class="block text-xs font-semibold text-slate-600">Date d'ouverture</label>
                    <input type="date" id="opened_at" name="opened_at" value="{{ old('opened_at', now()->toDateString()) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                </div>
            </div>

            <div>
                <label for="subject" class="block text-xs font-semibold text-slate-600">Objet <span class="text-rose-600">*</span></label>
                <input type="text" id="subject" name="subject" required value="{{ old('subject') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>

            <div>
                <label for="narrative" class="block text-xs font-semibold text-slate-600">Exposé de la situation</label>
                <textarea id="narrative" name="narrative" rows="6" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">{{ old('narrative') }}</textarea>
            </div>

            @foreach ([1, 2, 3] as $n)
                <div>
                    <label for="question_{{ $n }}" class="block text-xs font-semibold text-slate-600">Question {{ $n }}</label>
                    <input type="text" id="question_{{ $n }}" name="question_{{ $n }}" value="{{ old('question_' . $n) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                </div>
            @endforeach
        </fieldset>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                Ouvrir le dossier
            </button>
            <a href="{{ route('registre.dossiers') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Annuler</a>
        </div>
    </form>

@endsection
