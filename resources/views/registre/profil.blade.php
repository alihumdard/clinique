@extends('layouts.registre')

@section('title', 'Mon compte')

@section('content')

    <h1 class="text-2xl font-bold text-slate-900">Mon compte</h1>
    <p class="mt-1.5 text-sm text-slate-600">
        Vos informations et votre mot de passe.
    </p>

    <div class="mt-6 grid max-w-4xl grid-cols-1 gap-5 lg:grid-cols-2">

        <form method="POST" action="{{ route('registre.profile.update') }}" class="space-y-4 rounded-xl border border-slate-200 bg-white p-5">
            @csrf
            @method('PUT')

            <h2 class="text-sm font-bold text-slate-900">Vos informations</h2>

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700">Nom <span class="text-rose-600">*</span></label>
                <input type="text" id="name" name="name" required value="{{ old('name', $account->name) }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <dl class="space-y-2 border-t border-slate-100 pt-4 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Adresse e-mail</dt>
                    <dd class="truncate font-medium text-slate-900">{{ $account->email }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Profil</dt>
                    <dd class="font-medium text-slate-900">{{ $account->roleLabel() }}</dd>
                </div>
            </dl>

            {{-- Changed from the Comptes screen, not here: an address is
                 how the Clinic reaches an account, and a profile is what
                 it may do. Neither is for its holder to set alone. --}}
            <p class="text-xs leading-relaxed text-slate-500">
                L'adresse e-mail et le profil sont modifiés depuis l'écran Comptes.
            </p>

            <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                Enregistrer
            </button>
        </form>

        <form method="POST" action="{{ route('registre.profile.password') }}" class="space-y-4 rounded-xl border border-slate-200 bg-white p-5">
            @csrf
            @method('PUT')

            <h2 class="text-sm font-bold text-slate-900">Changer votre mot de passe</h2>

            <div>
                <label for="current_password" class="block text-sm font-semibold text-slate-700">Mot de passe actuel <span class="text-rose-600">*</span></label>
                <input type="password" id="current_password" name="current_password" required autocomplete="current-password" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <x-registre.password-fields prefix="profil" label="Nouveau mot de passe" />

            <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                Changer le mot de passe
            </button>
        </form>
    </div>

@endsection
