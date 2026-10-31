@extends('layouts.admin')

@section('title', 'Utilisateurs')

@section('content')

    <x-admin.flash />

    <x-admin.page-header
        title="Utilisateurs"
        lead="Gérez les comptes qui accèdent à cette administration."
        action-label="Ajouter un compte"
        action-dialog="create-user"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Utilisateurs'],
        ]"
    />

    {{--
        Said on the screen, because it is the question this page raises:
        an account that leaves keeps its name on what it published.
    --}}
    <div class="mt-6 flex items-start gap-3 rounded-xl border border-primary-100 bg-primary-50/60 p-4">
        <x-icon name="info" class="mt-0.5 h-5 w-5 shrink-0 text-primary-600" />
        <p class="text-sm leading-relaxed text-slate-700">
            Un compte se désactive, il ne se supprime pas : le supprimer effacerait l'auteur
            de tout ce qu'il a publié. Un compte désactivé ne peut plus se connecter, et son
            nom reste sur ses contenus. Le registre numérique a ses propres comptes, gérés
            séparément.
        </p>
    </div>

    @error('compte')
        <p role="alert" class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $message }}</p>
    @enderror

    <div class="mt-5 space-y-5">

        {{-- ============ KPIs ============ --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-admin.stat-card icon="users"       color="blue"  :value="$counts['total']" label="Comptes enregistrés" keep-at-zero />
            <x-admin.stat-card icon="lock-shield" color="green" :value="$counts['admins']" label="Administrateurs" note="accès complet" />
            <x-admin.stat-card icon="pencil"      color="amber" :value="$counts['editors']" label="Éditeurs" note="contenus uniquement" />
            <x-admin.stat-card icon="eye-off"     color="red"   :value="$counts['disabled']" label="Comptes désactivés" />
        </div>

        {{-- ============ TABLE ============ --}}
        <section class="rounded-xl border border-slate-200 bg-white">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="whitespace-nowrap border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th scope="col" class="px-5 py-3">Nom</th>
                            <th scope="col" class="px-5 py-3">Adresse e-mail</th>
                            <th scope="col" class="px-5 py-3">Rôle</th>
                            <th scope="col" class="px-5 py-3">Dernière connexion</th>
                            <th scope="col" class="px-5 py-3">Statut</th>
                            <th scope="col" class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($users as $account)
                            <tr class="{{ $account->is_active ? '' : 'bg-slate-50/60' }}">
                                <td class="px-5 py-3">
                                    <span class="font-semibold text-slate-900">{{ $account->name }}</span>
                                    @if($account->is(auth()->user()))
                                        <span class="ml-1.5 text-xs text-slate-400">(vous)</span>
                                    @endif
                                </td>

                                <td class="px-5 py-3 text-slate-600">{{ $account->email }}</td>

                                <td class="px-5 py-3">
                                    <x-admin.badge :tone="$account->isAdmin() ? 'blue' : 'slate'">
                                        {{ $account->roleLabel() }}
                                    </x-admin.badge>
                                </td>

                                <td class="whitespace-nowrap px-5 py-3 text-slate-600">
                                    {{ $account->last_login_at?->translatedFormat('j M Y, H:i') ?? 'Jamais' }}
                                </td>

                                <td class="px-5 py-3">
                                    <x-admin.badge :tone="$account->is_active ? 'green' : 'slate'">
                                        {{ $account->is_active ? 'Actif' : 'Désactivé' }}
                                    </x-admin.badge>
                                </td>

                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button
                                            type="button"
                                            data-open-dialog="edit-user-{{ $account->id }}"
                                            title="Modifier"
                                            class="flex h-8 w-8 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-primary-600"
                                        >
                                            <x-icon name="pencil" class="h-4 w-4" />
                                            <span class="sr-only">Modifier {{ $account->name }}</span>
                                        </button>

                                        <button
                                            type="button"
                                            data-open-dialog="password-user-{{ $account->id }}"
                                            title="Réinitialiser le mot de passe"
                                            class="flex h-8 w-8 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-primary-600"
                                        >
                                            <x-icon name="lock" class="h-4 w-4" />
                                            <span class="sr-only">Réinitialiser le mot de passe de {{ $account->name }}</span>
                                        </button>

                                        {{-- Outside the table body would be cleaner, but a
                                             one-button form is not a dialog: it posts and
                                             returns, like the other toggles in the admin. --}}
                                        <form method="POST" action="{{ route('admin.users.toggle', $account) }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                title="{{ $account->is_active ? 'Désactiver' : 'Réactiver' }}"
                                                class="flex h-8 w-8 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 {{ $account->is_active ? 'hover:text-rose-600' : 'hover:text-emerald-600' }}"
                                            >
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
        </section>
    </div>

    {{-- ============ DIALOGS ============ --}}

    <x-admin.edit-modal
        id="create-user"
        title="Ajouter un compte"
        :action="route('admin.users.store')"
        method="POST"
        submit-label="Créer le compte"
    >
        <div>
            <label for="create-user-name" class="block text-sm font-semibold text-slate-700">Nom <span class="text-rose-600">*</span></label>
            <input type="text" id="create-user-name" name="name" required value="{{ old('name') }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
        </div>

        <div>
            <label for="create-user-email" class="block text-sm font-semibold text-slate-700">Adresse e-mail <span class="text-rose-600">*</span></label>
            <input type="email" id="create-user-email" name="email" required value="{{ old('email') }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
        </div>

        <div>
            <label for="create-user-role" class="block text-sm font-semibold text-slate-700">Rôle <span class="text-rose-600">*</span></label>
            <select id="create-user-role" name="role" required class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                <option value="editor">Éditeur — rédige et publie les contenus</option>
                <option value="admin">Administrateur — contenus, paramètres, référentiels, données personnelles</option>
            </select>
        </div>

        <div>
            <label for="create-user-password" class="block text-sm font-semibold text-slate-700">Mot de passe <span class="text-rose-600">*</span></label>
            <input type="password" id="create-user-password" name="password" required minlength="12" autocomplete="new-password" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            <p class="mt-1.5 text-xs text-slate-500">Douze caractères au minimum, à renouveler à la première connexion.</p>
        </div>

        <div>
            <label for="create-user-password-confirm" class="block text-sm font-semibold text-slate-700">Confirmer le mot de passe <span class="text-rose-600">*</span></label>
            <input type="password" id="create-user-password-confirm" name="password_confirmation" required minlength="12" autocomplete="new-password" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
        </div>
    </x-admin.edit-modal>

    @foreach ($users as $account)
        <x-admin.edit-modal
            :id="'edit-user-' . $account->id"
            :title="'Modifier : ' . $account->name"
            :action="route('admin.users.update', $account)"
            method="PUT"
        >
            <div>
                <label for="edit-user-{{ $account->id }}-name" class="block text-sm font-semibold text-slate-700">Nom <span class="text-rose-600">*</span></label>
                <input type="text" id="edit-user-{{ $account->id }}-name" name="name" required value="{{ $account->name }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <div>
                <label for="edit-user-{{ $account->id }}-email" class="block text-sm font-semibold text-slate-700">Adresse e-mail <span class="text-rose-600">*</span></label>
                <input type="email" id="edit-user-{{ $account->id }}-email" name="email" required value="{{ $account->email }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <div>
                <label for="edit-user-{{ $account->id }}-role" class="block text-sm font-semibold text-slate-700">Rôle <span class="text-rose-600">*</span></label>
                <select id="edit-user-{{ $account->id }}-role" name="role" required class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    <option value="editor" @selected(! $account->isAdmin())>Éditeur — rédige et publie les contenus</option>
                    <option value="admin" @selected($account->isAdmin())>Administrateur — contenus, paramètres, référentiels, données personnelles</option>
                </select>
            </div>
        </x-admin.edit-modal>

        <x-admin.edit-modal
            :id="'password-user-' . $account->id"
            :title="'Mot de passe : ' . $account->name"
            :action="route('admin.users.password', $account)"
            method="PUT"
            submit-label="Réinitialiser"
        >
            <p class="text-sm leading-relaxed text-slate-600">
                Communiquez ce mot de passe à son titulaire et demandez-lui de le changer
                à sa première connexion.
            </p>

            <div>
                <label for="password-user-{{ $account->id }}" class="block text-sm font-semibold text-slate-700">Nouveau mot de passe <span class="text-rose-600">*</span></label>
                <input type="password" id="password-user-{{ $account->id }}" name="password" required minlength="12" autocomplete="new-password" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                <p class="mt-1.5 text-xs text-slate-500">Douze caractères au minimum.</p>
            </div>

            <div>
                <label for="password-user-{{ $account->id }}-confirm" class="block text-sm font-semibold text-slate-700">Confirmer <span class="text-rose-600">*</span></label>
                <input type="password" id="password-user-{{ $account->id }}-confirm" name="password_confirmation" required minlength="12" autocomplete="new-password" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>
        </x-admin.edit-modal>
    @endforeach

@endsection
