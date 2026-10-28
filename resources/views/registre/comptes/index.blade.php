@extends('layouts.registre')

@section('title', 'Comptes')

@section('content')

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Comptes du registre</h1>
            <p class="mt-1.5 text-sm text-slate-600">
                Les comptes qui accèdent au registre. Ceux du site public sont gérés
                séparément, dans l'administration du site.
            </p>
        </div>

        <button
            type="button"
            data-open-dialog="create-account"
            class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700"
        >
            Ajouter un compte
        </button>
    </div>

    <p class="mt-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-relaxed text-amber-900">
        Les droits exacts de chaque profil sont en attente de validation par la Clinique :
        pour l'instant, tout compte actif accède à l'ensemble du registre. Le profil
        enregistré ci-dessous sera appliqué dès que la matrice sera arrêtée.
    </p>

    <div class="mt-5 overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="whitespace-nowrap border-b border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th scope="col" class="px-5 py-3">Nom</th>
                        <th scope="col" class="px-5 py-3">Adresse e-mail</th>
                        <th scope="col" class="px-5 py-3">Profil</th>
                        <th scope="col" class="px-5 py-3">Dernière connexion</th>
                        <th scope="col" class="px-5 py-3">Statut</th>
                        <th scope="col" class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @foreach ($accounts as $account)
                        <tr class="{{ $account->is_active ? '' : 'bg-slate-50/60' }}">
                            <td class="px-5 py-3">
                                <span class="font-semibold text-slate-900">{{ $account->name }}</span>
                                @if($account->is(auth('registre')->user()))
                                    <span class="ml-1.5 text-xs text-slate-400">(vous)</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $account->email }}</td>
                            <td class="px-5 py-3 text-slate-700">{{ $account->roleLabel() }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-slate-600">
                                {{ $account->last_login_at?->translatedFormat('j M Y, H:i') ?? 'Jamais' }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-block whitespace-nowrap rounded px-2.5 py-1 text-xs font-medium {{ $account->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $account->is_active ? 'Actif' : 'Désactivé' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" data-open-dialog="edit-account-{{ $account->id }}" title="Modifier" class="flex h-8 w-8 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-primary-600">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                        <span class="sr-only">Modifier {{ $account->name }}</span>
                                    </button>

                                    <button type="button" data-open-dialog="password-account-{{ $account->id }}" title="Réinitialiser le mot de passe" class="flex h-8 w-8 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-primary-600">
                                        <x-icon name="lock" class="h-4 w-4" />
                                        <span class="sr-only">Réinitialiser le mot de passe de {{ $account->name }}</span>
                                    </button>

                                    <form method="POST" action="{{ route('registre.accounts.toggle', $account) }}">
                                        @csrf
                                        <button type="submit" title="{{ $account->is_active ? 'Désactiver' : 'Réactiver' }}" class="flex h-8 w-8 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 {{ $account->is_active ? 'hover:text-rose-600' : 'hover:text-emerald-600' }}">
                                            <x-icon name="{{ $account->is_active ? 'eye-off' : 'eye' }}" class="h-4 w-4" />
                                            <span class="sr-only">{{ $account->is_active ? 'Désactiver' : 'Réactiver' }} {{ $account->name }}</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============ DIALOGS ============ --}}

    <x-admin.edit-modal
        id="create-account"
        title="Ajouter un compte"
        :action="route('registre.accounts.store')"
        method="POST"
        submit-label="Créer le compte"
    >
        <x-registre.account-fields :roles="$roles" prefix="create-account" :with-password="true" />
    </x-admin.edit-modal>

    @foreach ($accounts as $account)
        <x-admin.edit-modal
            :id="'edit-account-' . $account->id"
            :title="'Modifier : ' . $account->name"
            :action="route('registre.accounts.update', $account)"
            method="PUT"
        >
            <x-registre.account-fields :roles="$roles" :prefix="'edit-account-' . $account->id" :account="$account" />
        </x-admin.edit-modal>

        <x-admin.edit-modal
            :id="'password-account-' . $account->id"
            :title="'Mot de passe : ' . $account->name"
            :action="route('registre.accounts.password', $account)"
            method="PUT"
            submit-label="Réinitialiser"
        >
            <p class="text-sm leading-relaxed text-slate-600">
                Communiquez ce mot de passe à son titulaire et demandez-lui de le changer
                depuis « Mon compte » à sa première connexion.
            </p>

            <x-registre.password-fields :prefix="'password-account-' . $account->id" />
        </x-admin.edit-modal>
    @endforeach

@endsection
