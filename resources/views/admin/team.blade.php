@extends('layouts.admin')

@section('title', "Gestion de l'équipe")
@section('search_placeholder', "Rechercher un membre d'équipe, un poste, une compétence...")

@section('content')

    <x-admin.page-header
        title="Gestion de l'équipe"
        lead="Gérez les membres de l'équipe de la Clinique Juridique FSJP-UAM."
        action-label="Ajouter un membre"
        :action-href="route('admin.team')"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Équipe'],
        ]"
    />

    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-4">

        <div class="space-y-5 xl:col-span-3">

            {{-- ============ KPIs ============ --}}
            {{-- Counts are illustrative until Module 2 supplies real queries. --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card icon="users"   color="blue"  value="12" label="Membres de l'équipe" trend="+20%" note="par rapport à l'année dernière" />
                <x-admin.stat-card icon="user-plus" color="green" value="8" label="Membres actifs"     trend="+33%" note="actuellement visibles" />
                <x-admin.stat-card icon="clock"   color="amber" value="3"  label="En attente"          note="à valider" />
                <x-admin.stat-card icon="eye-off" color="red"   value="1"  label="Inactif"             note="non affiché" />
            </div>

            {{-- ============ TABLE ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white">

                <div class="flex flex-wrap items-center gap-3 p-5">
                    <form role="search" class="relative w-full min-w-[12rem] flex-1 sm:w-auto sm:max-w-xs">
                        <label for="team-search" class="sr-only">Rechercher un membre</label>
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input
                            type="search"
                            id="team-search"
                            name="q"
                            placeholder="Rechercher un membre..."
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </form>

                    @php
                        $selects = [
                            ['id' => 'team-department', 'label' => 'Filtrer par département', 'options' => ['Tous les départements', 'Direction', 'Coordination', 'Conseil juridique', 'Formation', 'Communication', 'Événements', 'Administration', 'Partenariats']],
                            ['id' => 'team-status',     'label' => 'Filtrer par statut',      'options' => ['Tous les statuts', 'Publié', 'En attente', 'Inactif']],
                            ['id' => 'team-sort',       'label' => 'Trier les résultats',     'options' => ['Trier par', 'Plus récents', 'Ordre alphabétique', 'Département']],
                        ];
                    @endphp

                    @foreach ($selects as $select)
                        <div class="shrink-0">
                            <label for="{{ $select['id'] }}" class="sr-only">{{ $select['label'] }}</label>
                            <select
                                id="{{ $select['id'] }}"
                                class="rounded-lg border border-slate-200 py-2.5 pl-3.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                                @foreach ($select['options'] as $option)
                                    <option>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach

                    <button type="button" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50">
                        <x-icon name="filter" class="h-4 w-4" />
                        Filtrer
                    </button>
                </div>

                @php
                    $members = [
                        ['photo' => 'team/director-1', 'name' => 'Dr. Amadou Issa',       'role' => 'Directeur',                 'department' => 'Direction',         'tone' => 'blue',   'status' => 'Publié',     'statusTone' => 'green'],
                        ['photo' => 'team/staff-1',    'name' => 'Mme Aïssatou Moussa',   'role' => 'Coordinatrice',             'department' => 'Coordination',      'tone' => 'purple', 'status' => 'Publié',     'statusTone' => 'green'],
                        ['photo' => 'team/director-2', 'name' => 'M. Ibrahim Hamidou',    'role' => 'Juriste',                   'department' => 'Conseil juridique', 'tone' => 'green',  'status' => 'Publié',     'statusTone' => 'green'],
                        ['photo' => 'team/staff-2',    'name' => 'Mme Fatima Ali',        'role' => 'Chargée de formation',      'department' => 'Formation',         'tone' => 'amber',  'status' => 'En attente', 'statusTone' => 'amber'],
                        ['photo' => 'team/student-1',  'name' => 'M. Abdoulaye Sani',     'role' => 'Responsable Web TV',        'department' => 'Communication',     'tone' => 'red',    'status' => 'Publié',     'statusTone' => 'green'],
                        ['photo' => 'team/staff-3',    'name' => 'Mme Zara Oumarou',      'role' => 'Juriste',                   'department' => 'Conseil juridique', 'tone' => 'green',  'status' => 'Publié',     'statusTone' => 'green'],
                        ['photo' => 'team/student-2',  'name' => 'M. Moussa Saley',       'role' => 'Chargé des activités',      'department' => 'Événements',        'tone' => 'blue',   'status' => 'Publié',     'statusTone' => 'green'],
                        ['photo' => 'team/student-3',  'name' => 'Mme Rahamatou Idrissa', 'role' => 'Assistante administrative', 'department' => 'Administration',    'tone' => 'purple', 'status' => 'Inactif',    'statusTone' => 'red'],
                        ['photo' => 'team/student-4',  'name' => 'M. Souleymane Tahirou', 'role' => 'Graphiste / Communication', 'department' => 'Communication',     'tone' => 'red',    'status' => 'Publié',     'statusTone' => 'green'],
                        ['photo' => 'team/student-5',  'name' => 'Mme Halima Danladi',    'role' => 'Chargée des partenariats',  'department' => 'Partenariats',      'tone' => 'green',  'status' => 'En attente', 'statusTone' => 'amber'],
                    ];
                @endphp

                <p class="flex items-center gap-1.5 px-5 pt-1 text-xs text-slate-500 lg:hidden">
                    <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0" />
                    Faites glisser le tableau pour voir toutes les colonnes
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[56rem] text-left">
                        <thead>
                            <tr class="border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
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
                                <th scope="col" class="px-3 py-3">Département</th>
                                <th scope="col" class="px-3 py-3">Statut</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($members as $i => $member)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <input
                                            type="checkbox"
                                            aria-label="Sélectionner : {{ $member['name'] }}"
                                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                    </td>

                                    <td class="px-2 py-3 text-sm text-slate-500">{{ $i + 1 }}</td>

                                    <td class="px-2 py-3">
                                        <x-picture
                                            :path="$member['photo']"
                                            alt=""
                                            width="80" height="80"
                                            class="h-11 w-11 rounded-lg object-cover"
                                        />
                                    </td>

                                    <td class="px-3 py-3 text-sm font-semibold text-slate-900">{{ $member['name'] }}</td>

                                    <td class="px-3 py-3 text-sm text-slate-600">{{ $member['role'] }}</td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$member['tone']">{{ $member['department'] }}</x-admin.badge>
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$member['statusTone']">{{ $member['status'] }}</x-admin.badge>
                                    </td>

                                    <td class="px-5 py-3">
                                        <x-admin.row-actions :label="$member['name']" :duplicate="false" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-admin.table-footer
                    :from="1" :to="10" :total="12" unit="membres"
                    :current="1" :pages="2"
                />
            </section>
        </div>

        {{-- ============ ADD MEMBER ============ --}}
        {{--
            Presentation only: Module 2 adds the POST route and server-side
            validation. Note this form holds personal data about identifiable
            people, so Module 2 must also apply the retention rules agreed
            with the Clinic before any of it is stored.
        --}}
        <aside>
            <section class="rounded-xl border border-slate-200 bg-white p-5">

                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="user-plus" class="h-5 w-5 text-primary-600" />
                    Ajouter un membre de l'équipe
                </h2>

                <x-admin.filter-tabs
                    class="mt-4"
                    :tabs="[
                        ['label' => 'Informations générales', 'active' => true],
                        ['label' => 'Réseaux sociaux'],
                    ]"
                />

                <form method="POST" action="#" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="member-name" class="block text-sm font-medium text-slate-700">
                            Nom complet
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <input
                            type="text"
                            id="member-name"
                            name="nom"
                            required
                            autocomplete="name"
                            placeholder="Ex. Aïcha Moussa"
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
                            name="poste"
                            required
                            placeholder="Ex. Juriste, Coordinatrice..."
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="member-department" class="block text-sm font-medium text-slate-700">
                            Département
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <select
                            id="member-department"
                            name="departement"
                            required
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option value="">Sélectionner un département</option>
                            <option>Direction</option>
                            <option>Coordination</option>
                            <option>Conseil juridique</option>
                            <option>Formation</option>
                            <option>Communication</option>
                            <option>Événements</option>
                            <option>Administration</option>
                            <option>Partenariats</option>
                        </select>
                    </div>

                    <div>
                        <label for="member-bio" class="block text-sm font-medium text-slate-700">
                            Bio / Description
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>

                        {{-- Formatting controls are inert until Module 2 wires an editor. --}}
                        <div class="mt-1.5 overflow-hidden rounded-lg border border-slate-200">
                            <div class="flex items-center gap-1 border-b border-slate-200 bg-slate-50 px-2 py-1.5">
                                @php
                                    $tools = [
                                        ['icon' => 'bold',        'label' => 'Gras'],
                                        ['icon' => 'italic',      'label' => 'Italique'],
                                        ['icon' => 'underline',   'label' => 'Souligné'],
                                        ['icon' => 'list-bullet', 'label' => 'Liste à puces'],
                                        ['icon' => 'list-number', 'label' => 'Liste numérotée'],
                                        ['icon' => 'link',        'label' => 'Insérer un lien'],
                                    ];
                                @endphp

                                @foreach ($tools as $tool)
                                    <button
                                        type="button"
                                        class="flex h-7 w-7 items-center justify-center rounded text-slate-500 transition-colors hover:bg-slate-200 hover:text-slate-700"
                                    >
                                        <x-icon :name="$tool['icon']" class="h-4 w-4" />
                                        <span class="sr-only">{{ $tool['label'] }}</span>
                                    </button>
                                @endforeach
                            </div>

                            <textarea
                                id="member-bio"
                                name="bio"
                                rows="4"
                                required
                                placeholder="Décrivez brièvement le membre de l'équipe..."
                                class="w-full resize-y border-0 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-0"
                            ></textarea>
                        </div>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-slate-700">
                            Photo
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </span>

                        <label
                            for="member-photo"
                            class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
                        >
                            <x-icon name="upload" class="h-7 w-7 text-primary-500" />
                            <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour télécharger</span>
                            <span class="mt-0.5 text-xs text-slate-500">ou glissez une image ici</span>
                            <span class="mt-1 text-xs text-slate-400">(JPG, PNG - Max 2MB)</span>

                            <input
                                type="file"
                                id="member-photo"
                                name="photo"
                                accept="image/jpeg,image/png"
                                class="sr-only"
                            >
                        </label>
                    </div>

                    <div>
                        <label for="member-status" class="block text-sm font-medium text-slate-700">
                            Statut
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <select
                            id="member-status"
                            name="statut"
                            required
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option>Publié</option>
                            <option>En attente</option>
                            <option>Inactif</option>
                        </select>
                    </div>

                    <div class="pt-1">
                        <x-admin.toggle name="afficher" label="Afficher sur la page publique" :checked="true" />
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button
                            type="button"
                            class="flex-1 rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-50"
                        >
                            Annuler
                        </button>

                        <button
                            type="submit"
                            class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary-600 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-primary-700"
                        >
                            <x-icon name="save" class="h-4 w-4" />
                            Enregistrer
                        </button>
                    </div>
                </form>
            </section>
        </aside>
    </div>

@endsection
