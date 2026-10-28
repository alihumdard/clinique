@extends('layouts.admin')

@section('title', 'Mon compte')

@section('content')

    <x-admin.flash />

    <x-admin.page-header
        title="Mon compte"
        lead="Vos informations et votre mot de passe."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Mon compte'],
        ]"
    />

    <div class="mt-6 grid max-w-4xl grid-cols-1 gap-5 lg:grid-cols-2">

        <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4 rounded-xl border border-slate-200 bg-white p-5">
            @csrf
            @method('PUT')

            <h2 class="text-sm font-bold text-slate-900">Vos informations</h2>

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700">Nom <span class="text-rose-600">*</span></label>
                <input type="text" id="name" name="name" required value="{{ old('name', $account->name) }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700">Adresse e-mail <span class="text-rose-600">*</span></label>
                <input type="email" id="email" name="email" required value="{{ old('email', $account->email) }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <p class="border-t border-slate-100 pt-4 text-sm text-slate-600">
                Rôle : <strong class="font-semibold text-slate-900">{{ $account->roleLabel() }}</strong>
                {{-- Your own role is not yours to raise. --}}
                <span class="block text-xs text-slate-500">Seul un administrateur peut modifier un rôle, depuis l'écran Utilisateurs.</span>
            </p>

            <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                Enregistrer
            </button>
        </form>

        <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4 rounded-xl border border-slate-200 bg-white p-5">
            @csrf
            @method('PUT')

            <h2 class="text-sm font-bold text-slate-900">Changer votre mot de passe</h2>

            <div>
                <label for="current_password" class="block text-sm font-semibold text-slate-700">Mot de passe actuel <span class="text-rose-600">*</span></label>
                <input type="password" id="current_password" name="current_password" required autocomplete="current-password" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <div>
                <label for="profil-password" class="block text-sm font-semibold text-slate-700">Nouveau mot de passe <span class="text-rose-600">*</span></label>
                <input type="password" id="profil-password" name="password" required minlength="12" autocomplete="new-password" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                <p class="mt-1.5 text-xs text-slate-500">Douze caractères au minimum.</p>
            </div>

            <div>
                <label for="profil-password-confirm" class="block text-sm font-semibold text-slate-700">Confirmer <span class="text-rose-600">*</span></label>
                <input type="password" id="profil-password-confirm" name="password_confirmation" required minlength="12" autocomplete="new-password" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                Changer le mot de passe
            </button>
        </form>
    </div>

@endsection
