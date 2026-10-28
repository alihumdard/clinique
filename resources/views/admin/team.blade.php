@extends('layouts.admin')

@section('title', "Gestion de l'équipe")
@section('search_action', route('admin.team'))
@section('search_placeholder', "Rechercher un membre d'équipe, un poste, une compétence...")

@section('content')

    <x-admin.flash />

    <x-admin.page-header
        title="Gestion de l'équipe"
        lead="Gérez les membres de l'équipe de la Clinique Juridique FSJP-UAM."
        action-label="Ajouter un membre"
        action-dialog="create-member"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Équipe'],
        ]"
    />

    <div class="mt-6 space-y-5">

        <div class="space-y-5">

            {{-- ============ KPIs ============ --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card icon="users"   color="green" :value="$counts['published']" label="Membres visibles" note="affichés sur le site" />
                <x-admin.stat-card icon="eye-off" color="amber" :value="$counts['hidden']"    label="Masqués" note="enregistrés, non affichés" />
                <x-admin.stat-card icon="clock"   color="blue"  :value="$counts['drafts']"    label="Brouillons" />
                <x-admin.stat-card icon="trash"   color="red"   :value="$counts['trashed']"   :href="route('admin.team', ['statut' => 'corbeille'])"
                                   label="À la corbeille" />
            </div>

            {{-- ============ TABLE ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white">

                {{-- One GET form: search, groupe and statut submit together,
                     so a filtered list is a URL that can be shared. --}}
                <form method="GET" action="{{ route('admin.team') }}" class="space-y-3 p-5">
                    <div role="search" class="relative w-full lg:max-w-xs">
                        <label for="team-search" class="sr-only">Rechercher un membre</label>
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input
                            type="search"
                            id="team-search"
                            name="q"
                            value="{{ $filters['q'] ?? '' }}"
                            placeholder="Rechercher un membre..."
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    @php
                        $statuses = [
                            '' => 'Tous les statuts',
                            'published' => 'Publiés',
                            'draft' => 'Brouillons',
                            'archived' => 'Archivés',
                            'corbeille' => 'Corbeille',
                        ];
                    @endphp

                    <div class="grid grid-cols-2 gap-3 sm:flex sm:flex-wrap sm:items-center">
                        <div class="min-w-0 sm:shrink-0">
                            <label for="team-group" class="sr-only">Filtrer par groupe</label>
                            <select
                                id="team-group"
                                name="groupe"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-3.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-auto"
                            >
                                <option value="">Tous les groupes</option>
                                @foreach ($departments as $key => $label)
                                    <option value="{{ $key }}" @selected(($filters['groupe'] ?? '') === $key)>
                                        {{ $label }} ({{ $byDepartment[$key] ?? 0 }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="min-w-0 sm:shrink-0">
                            <label for="team-status" class="sr-only">Filtrer par statut</label>
                            <select
                                id="team-status"
                                name="statut"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-3.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-auto"
                            >
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['statut'] ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="inline-flex w-full shrink-0 items-center justify-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50 sm:w-auto">
                            <x-icon name="filter" class="h-4 w-4" />
                            Filtrer
                        </button>

                        @if (array_filter($filters))
                            <a href="{{ route('admin.team') }}" class="col-span-2 inline-flex w-full shrink-0 items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-500 transition-colors hover:bg-slate-50 sm:col-span-1 sm:w-auto">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </form>

                <p class="flex items-center gap-1.5 px-5 pt-1 text-xs text-slate-500 lg:hidden">
                    <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0" />
                    Faites glisser le tableau pour voir toutes les colonnes
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[56rem] text-left">
                        <thead>
                            <tr class="whitespace-nowrap border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <th scope="col" class="w-12 px-5 py-3">
                                    <input
                                        type="checkbox"
                                        aria-label="Sélectionner tous les membres"
                                        class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                    >
                                </th>
                                <th scope="col" class="w-12 px-2 py-3">#</th>
                                <th scope="col" class="w-20 px-2 py-3">Photo</th>
                                <th scope="col" class="min-w-[14rem] px-3 py-3">Nom complet</th>
                                <th scope="col" class="px-3 py-3">Poste / Fonction</th>
                                <th scope="col" class="px-3 py-3">Groupe</th>
                                <th scope="col" class="px-3 py-3">Statut</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($members as $member)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <input
                                            type="checkbox"
                                            aria-label="Sélectionner : {{ $member->name }}"
                                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                    </td>

                                    <td class="px-2 py-3 text-sm text-slate-500">{{ $loop->iteration + ($members->firstItem() - 1) }}</td>

                                    <td class="px-2 py-3">
                                        @if($member->hasPhoto())
                                            <img
                                                src="{{ $member->photoUrl() }}"
                                                alt=""
                                                width="80" height="80"
                                                loading="lazy"
                                                class="h-11 w-11 rounded-lg object-cover"
                                            >
                                        @else
                                            <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-primary-50 text-sm font-bold text-primary-600" aria-hidden="true">
                                                {{ $member->initials() }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="block text-sm font-semibold text-slate-900">{{ $member->name }}</span>
                                        @if($member->cohort)
                                            <span class="mt-0.5 block text-xs text-slate-400">Promotion {{ $member->cohort }}</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3 text-sm text-slate-600">{{ $member->role }}</td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge tone="blue">{{ $departments[$member->department] ?? $member->department }}</x-admin.badge>
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$member->statusTone()">{{ $member->statusLabel() }}</x-admin.badge>
                                    </td>

                                    <td class="px-5 py-3">
                                        <x-admin.row-actions
                                            :label="$member->name"
                                            edit-dialog="edit-member-{{ $member->id }}"
                                            :view-href="route('about.team')"
                                            :duplicate-action="route('admin.team.duplicate', $member)"
                                            :toggle-action="route('admin.team.toggle', $member)"
                                            :is-visible="$member->is_visible"
                                            :delete-action="route('admin.team.destroy', $member)"
                                            :restore-action="$member->trashed() ? route('admin.team.restore', $member->id) : null"
                                            :force-delete-action="$member->trashed() && auth()->user()->isAdmin() ? route('admin.team.force-destroy', $member->id) : null"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-12 text-center">
                                        <p class="text-sm text-slate-500">Aucun membre ne correspond à cette recherche.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-admin.table-footer :paginator="$members" unit="membres" />
            </section>
        </div>

    </div>

    {{--
        Every row's "Modifier" dialog, rendered here rather than inside the
        table above: a dialog element is not valid content for a table body
        between row elements, and a browser that encounters one there
        foster-parents it out of the table during parsing - which was
        silently corrupting the form markup of every dialog on the page
        (content spilling out as siblings of an empty form, breaking its
        height/scroll layout) without ever raising a console error, since
        this is valid-by-recovery HTML, not a parse error.
    --}}
    @foreach ($members as $member)
        <x-admin.edit-modal id="edit-member-{{ $member->id }}" title="Modifier le membre" :action="route('admin.team.update', $member)">
            <div>
                <label for="edit-member-{{ $member->id }}-name" class="block text-sm font-medium text-slate-700">
                    Nom complet
                    <span class="text-rose-600" aria-hidden="true">*</span>
                </label>
                <input type="text" id="edit-member-{{ $member->id }}-name" name="name" required value="{{ $member->name }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <div>
                <label for="edit-member-{{ $member->id }}-role" class="block text-sm font-medium text-slate-700">
                    Poste / Fonction
                    <span class="text-rose-600" aria-hidden="true">*</span>
                </label>
                <input type="text" id="edit-member-{{ $member->id }}-role" name="role" required value="{{ $member->role }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <div>
                <label for="edit-member-{{ $member->id }}-department" class="block text-sm font-medium text-slate-700">Groupe</label>
                <select id="edit-member-{{ $member->id }}-department" name="department" required class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    @foreach ($departments as $key => $label)
                        <option value="{{ $key }}" @selected($member->department === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="edit-member-{{ $member->id }}-cohort" class="block text-sm font-medium text-slate-700">Promotion</label>
                <input type="text" id="edit-member-{{ $member->id }}-cohort" name="cohort" value="{{ $member->cohort }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <div>
                <label for="edit-member-{{ $member->id }}-bio" class="block text-sm font-medium text-slate-700">Présentation</label>
                <textarea id="edit-member-{{ $member->id }}-bio" name="bio" rows="3" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">{{ $member->bio }}</textarea>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="edit-member-{{ $member->id }}-email" class="block text-sm font-medium text-slate-700">Adresse e-mail</label>
                    <input type="email" id="edit-member-{{ $member->id }}-email" name="email" value="{{ $member->email }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>

                <div>
                    <label for="edit-member-{{ $member->id }}-linkedin" class="block text-sm font-medium text-slate-700">Profil LinkedIn</label>
                    <input type="url" id="edit-member-{{ $member->id }}-linkedin" name="linkedin" value="{{ $member->linkedin }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="edit-member-{{ $member->id }}-status" class="block text-sm font-medium text-slate-700">Statut</label>
                    <select id="edit-member-{{ $member->id }}-status" name="status" required class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                        <option value="draft" @selected($member->status === 'draft')>Brouillon</option>
                        <option value="published" @selected($member->status === 'published')>Publié</option>
                        <option value="archived" @selected($member->status === 'archived')>Archivé</option>
                    </select>
                </div>

                <div>
                    <label for="edit-member-{{ $member->id }}-position" class="block text-sm font-medium text-slate-700">Ordre dans le groupe</label>
                    <input type="number" id="edit-member-{{ $member->id }}-position" name="position" min="0" value="{{ $member->position }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>
            </div>

            <div>
                <span class="block text-sm font-medium text-slate-700">Photo</span>
                <div class="mt-1.5 flex items-center gap-3">
                    @if($member->hasPhoto())
                        <img src="{{ $member->photoUrl() }}" alt="" class="h-14 w-14 shrink-0 rounded-lg object-cover">
                    @endif
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100">
                </div>
            </div>

            <div class="pt-1">
                <x-admin.toggle name="is_visible" label="Afficher sur la page publique" :checked="(bool) $member->is_visible" />
            </div>
        </x-admin.edit-modal>
    @endforeach

    {{--
        "Ajouter un membre" in the page header opens this dialog rather than
        scrolling to a sidebar form, matching the Actualités pattern.
    --}}
    <x-admin.edit-modal id="create-member" title="Ajouter un membre de l'équipe" :action="route('admin.team.store')" method="POST" submit-label="Ajouter le membre">
        <div>
            <label for="member-name" class="block text-sm font-medium text-slate-700">
                Nom complet
                <span class="text-rose-600" aria-hidden="true">*</span>
                <span class="sr-only">(obligatoire)</span>
            </label>
            <input
                type="text"
                id="member-name"
                name="name"
                required
                placeholder="Ex. Dr. Halima Issa"
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
        </div>

        <div>
            <label for="member-role" class="block text-sm font-medium text-slate-700">
                Poste / Fonction
                <span class="text-rose-600" aria-hidden="true">*</span>
                <span class="sr-only">(obligatoire)</span>
            </label>
            <input
                type="text"
                id="member-role"
                name="role"
                required
                placeholder="Ex. Droit de la famille"
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
        </div>

        <div>
            <label for="member-department" class="block text-sm font-medium text-slate-700">
                Groupe
                <span class="text-rose-600" aria-hidden="true">*</span>
                <span class="sr-only">(obligatoire)</span>
            </label>
            <select
                id="member-department"
                name="department"
                required
                aria-describedby="member-department-hint"
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
                @foreach ($departments as $key => $label)
                    <option value="{{ $key }}" @selected($key === 'enseignants')>{{ $label }}</option>
                @endforeach
            </select>
            <p id="member-department-hint" class="mt-1.5 text-xs text-slate-500">
                Détermine la section de la page Équipe où le membre apparaît.
            </p>
        </div>

        <div>
            <label for="member-cohort" class="block text-sm font-medium text-slate-700">Promotion</label>
            <input
                type="text"
                id="member-cohort"
                name="cohort"
                placeholder="Ex. 2025"
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
        </div>

        <div>
            <label for="member-bio" class="block text-sm font-medium text-slate-700">Présentation</label>
            <textarea
                id="member-bio"
                name="bio"
                rows="3"
                placeholder="Quelques lignes sur son rôle au sein de la Clinique..."
                class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            ></textarea>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="member-email" class="block text-sm font-medium text-slate-700">Adresse e-mail</label>
                <input
                    type="email"
                    id="member-email"
                    name="email"
                    aria-describedby="member-email-hint"
                    placeholder="prenom.nom@uam.ne"
                    class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                >
                <p id="member-email-hint" class="mt-1.5 text-xs text-slate-500">
                    Publiée sur la page Équipe. Laissez vide pour ne pas l'afficher.
                </p>
            </div>

            <div>
                <label for="member-linkedin" class="block text-sm font-medium text-slate-700">Profil LinkedIn</label>
                <input
                    type="url"
                    id="member-linkedin"
                    name="linkedin"
                    placeholder="https://www.linkedin.com/in/..."
                    class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                >
            </div>
        </div>

        <div>
            <span class="block text-sm font-medium text-slate-700">Photo</span>

            <label
                for="member-photo"
                class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
            >
                <x-icon name="upload" class="h-7 w-7 text-primary-500" />
                <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour importer une photo</span>
                <span class="mt-1 text-xs text-slate-400">(JPG, PNG - Max 2MB)</span>

                <input
                    type="file"
                    id="member-photo"
                    name="photo"
                    accept="image/jpeg,image/png,image/webp"
                    class="sr-only"
                >
            </label>
            <p class="mt-1.5 text-xs text-slate-500">
                Facultative : sans photo, les initiales sont affichées.
            </p>
        </div>

        <div>
            <label for="member-position" class="block text-sm font-medium text-slate-700">Ordre dans le groupe</label>
            <input
                type="number"
                id="member-position"
                name="position"
                min="0"
                aria-describedby="member-position-hint"
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
            <p id="member-position-hint" class="mt-1.5 text-xs text-slate-500">
                Laissez vide pour ajouter à la fin du groupe.
            </p>
        </div>

        <div>
            <label for="member-status" class="block text-sm font-medium text-slate-700">
                Statut
                <span class="text-rose-600" aria-hidden="true">*</span>
                <span class="sr-only">(obligatoire)</span>
            </label>
            <select
                id="member-status"
                name="status"
                required
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
                @php
                    $options = ['published' => 'Publié', 'draft' => 'Brouillon', 'archived' => 'Archivé'];
                @endphp
                @foreach ($options as $value => $label)
                    <option value="{{ $value }}" @selected($value === 'published')>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="pt-1">
            <x-admin.toggle name="is_visible" label="Afficher sur la page publique"
                :checked="session()->hasOldInput() ? (bool) old('is_visible') : true" />
        </div>
    </x-admin.edit-modal>

@endsection
