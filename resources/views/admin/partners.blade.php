@extends('layouts.admin')

@section('title', 'Gestion des partenaires')
@section('search_placeholder', 'Rechercher un partenaire, une organisation...')

@section('content')

    <x-admin.page-header
        title="Gestion des partenaires"
        lead="Gérez les organisations et institutions partenaires de la Clinique Juridique FSJP-UAM."
        action-label="Ajouter un partenaire"
        :action-href="route('admin.partners')"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Partenaires'],
        ]"
    />

    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-4">

        <div class="space-y-5 xl:col-span-3">

            {{-- ============ KPIs ============ --}}
            {{-- Counts are illustrative until Module 2 supplies real queries. --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card icon="users"       color="blue"  value="18" label="Partenaires"        trend="+20%" note="par rapport à l'année dernière" />
                <x-admin.stat-card icon="check-circle" color="green" value="15" label="Partenaires actifs" trend="+25%" note="actuellement visibles" />
                <x-admin.stat-card icon="eye"         color="amber" value="2"  label="En attente"         note="à valider" />
                <x-admin.stat-card icon="x-circle"    color="red"   value="1"  label="Inactif"            note="non affiché" />
            </div>

            {{-- ============ TABLE ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white">

                <div class="flex flex-wrap items-center gap-3 p-5">
                    <form role="search" class="relative min-w-0 flex-1 sm:max-w-xs">
                        <label for="partners-search" class="sr-only">Rechercher un partenaire</label>
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input
                            type="search"
                            id="partners-search"
                            name="q"
                            placeholder="Rechercher un partenaire..."
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </form>

                    @php
                        $selects = [
                            ['id' => 'partners-category', 'label' => 'Filtrer par catégorie', 'options' => ['Toutes les catégories', 'Institution internationale', 'Organisation internationale', 'Bailleur de fonds', 'ONG', 'Institution académique']],
                            ['id' => 'partners-status',   'label' => 'Filtrer par statut',    'options' => ['Tous les statuts', 'Publié', 'En attente', 'Inactif']],
                            ['id' => 'partners-sort',     'label' => 'Trier les résultats',   'options' => ['Trier par', 'Plus récents', 'Ordre alphabétique', 'Catégorie']],
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
                    /*
                     * Partner organisations. Logos are supplied by the Clinic;
                     * until then each row shows the organisation's initials on
                     * a plain tile rather than a stretched placeholder photo.
                     */
                    $partners = [
                        ['short' => 'UE',     'name' => 'Union Européenne',                               'category' => 'Institution internationale',  'tone' => 'blue',   'status' => 'Publié',     'statusTone' => 'green', 'date' => '10 juin 2025', 'time' => '14:32'],
                        ['short' => 'ONU',    'name' => 'Organisation des Nations Unies',                 'category' => 'Organisation internationale', 'tone' => 'purple', 'status' => 'Publié',     'statusTone' => 'green', 'date' => '8 juin 2025',  'time' => '11:20'],
                        ['short' => 'AFD',    'name' => 'Agence Française de Développement (AFD)',        'category' => 'Bailleur de fonds',           'tone' => 'amber',  'status' => 'Publié',     'statusTone' => 'green', 'date' => '5 juin 2025',  'time' => '09:15'],
                        ['short' => 'UNICEF', 'name' => 'UNICEF',                                         'category' => 'Organisation internationale', 'tone' => 'purple', 'status' => 'Publié',     'statusTone' => 'green', 'date' => '1 juin 2025',  'time' => '12:10'],
                        ['short' => 'USAID',  'name' => 'USAID',                                          'category' => 'Bailleur de fonds',           'tone' => 'amber',  'status' => 'Publié',     'statusTone' => 'green', 'date' => '28 mai 2025',  'time' => '16:45'],
                        ['short' => 'BM',     'name' => 'Banque Mondiale',                                'category' => 'Bailleur de fonds',           'tone' => 'amber',  'status' => 'En attente', 'statusTone' => 'amber', 'date' => '25 mai 2025',  'time' => '10:22'],
                        ['short' => 'HCR',    'name' => 'HCR (Haut-Commissariat aux Réfugiés)',           'category' => 'Organisation internationale', 'tone' => 'purple', 'status' => 'Publié',     'statusTone' => 'green', 'date' => '20 mai 2025',  'time' => '15:30'],
                        ['short' => 'OIM',    'name' => 'OIM (Organisation Internationale pour les Migrations)', 'category' => 'Organisation internationale', 'tone' => 'purple', 'status' => 'Publié', 'statusTone' => 'green', 'date' => '16 mai 2025', 'time' => '13:18'],
                        ['short' => 'CRN',    'name' => 'Croix-Rouge Niger',                              'category' => 'ONG',                         'tone' => 'red',    'status' => 'Publié',     'statusTone' => 'green', 'date' => '12 mai 2025',  'time' => '09:40'],
                        ['short' => 'UAM',    'name' => 'Université Abdou Moumouni',                      'category' => 'Institution académique',      'tone' => 'blue',   'status' => 'Inactif',    'statusTone' => 'red',   'date' => '8 mai 2025',   'time' => '11:05'],
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
                                        aria-label="Sélectionner tous les partenaires"
                                        class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                    >
                                </th>
                                <th scope="col" class="w-12 px-2 py-3">#</th>
                                <th scope="col" class="w-20 px-2 py-3">Logo</th>
                                <th scope="col" class="px-3 py-3">Nom du partenaire</th>
                                <th scope="col" class="px-3 py-3">Catégorie</th>
                                <th scope="col" class="px-3 py-3">Statut</th>
                                <th scope="col" class="px-3 py-3">Date d'ajout</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($partners as $i => $partner)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <input
                                            type="checkbox"
                                            aria-label="Sélectionner : {{ $partner['name'] }}"
                                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                    </td>

                                    <td class="px-2 py-3 text-sm text-slate-500">{{ $i + 1 }}</td>

                                    <td class="px-2 py-3">
                                        {{-- Logo tile: white background, since partner marks
                                             are supplied as transparent artwork. --}}
                                        <span class="flex h-11 w-14 items-center justify-center rounded-md border border-slate-200 bg-white px-1 text-[10px] font-bold text-slate-500">
                                            {{ $partner['short'] }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="block max-w-[15rem] text-sm font-semibold leading-snug text-slate-900">
                                            {{ $partner['name'] }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$partner['tone']">{{ $partner['category'] }}</x-admin.badge>
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$partner['statusTone']">{{ $partner['status'] }}</x-admin.badge>
                                    </td>

                                    <td class="px-3 py-3 text-sm leading-tight text-slate-500">
                                        {{ $partner['date'] }}<br>
                                        <span class="text-xs">{{ $partner['time'] }}</span>
                                    </td>

                                    <td class="px-5 py-3">
                                        <x-admin.row-actions :label="$partner['name']" :duplicate="false" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-admin.table-footer
                    :from="1" :to="10" :total="18" unit="partenaires"
                    :current="1" :pages="2"
                />
            </section>
        </div>

        {{-- ============ ADD PARTNER ============ --}}
        {{--
            Presentation only: Module 2 adds the POST route and server-side
            validation. The accept and size hints are conveniences, never
            the security boundary — SVG uploads in particular must be
            sanitised server-side, since SVG can carry script.
        --}}
        <aside>
            <section class="rounded-xl border border-slate-200 bg-white p-5">

                <div class="flex items-start justify-between gap-3">
                    <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                        <x-icon name="users" class="h-5 w-5 text-primary-600" />
                        Ajouter un partenaire
                    </h2>

                    <button type="button" class="shrink-0 rounded-md p-1 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="sr-only">Fermer le formulaire</span>
                    </button>
                </div>

                <x-admin.filter-tabs
                    class="mt-4"
                    :tabs="[
                        ['label' => 'Informations générales', 'active' => true],
                        ['label' => 'Détails supplémentaires'],
                    ]"
                />

                <form method="POST" action="#" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="partner-name" class="block text-sm font-medium text-slate-700">
                            Nom du partenaire
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <input
                            type="text"
                            id="partner-name"
                            name="nom"
                            required
                            placeholder="Ex. Union Européenne"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="partner-category" class="block text-sm font-medium text-slate-700">
                            Catégorie
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <select
                            id="partner-category"
                            name="categorie"
                            required
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option value="">Sélectionner une catégorie</option>
                            <option>Institution internationale</option>
                            <option>Organisation internationale</option>
                            <option>Bailleur de fonds</option>
                            <option>ONG</option>
                            <option>Institution académique</option>
                        </select>
                    </div>

                    <div>
                        <label for="partner-description" class="block text-sm font-medium text-slate-700">
                            Description
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
                                id="partner-description"
                                name="description"
                                rows="4"
                                required
                                placeholder="Décrivez brièvement le partenaire..."
                                class="w-full resize-y border-0 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-0"
                            ></textarea>
                        </div>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-slate-700">
                            Logo
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </span>

                        <label
                            for="partner-logo"
                            class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
                        >
                            <x-icon name="upload" class="h-7 w-7 text-primary-500" />
                            <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour télécharger</span>
                            <span class="mt-0.5 text-xs text-slate-500">ou glissez une image ici</span>
                            <span class="mt-1 text-xs text-slate-400">(JPG, PNG, SVG - Max 2MB)</span>

                            <input
                                type="file"
                                id="partner-logo"
                                name="logo"
                                accept="image/jpeg,image/png,image/svg+xml"
                                class="sr-only"
                            >
                        </label>
                    </div>

                    <div>
                        <label for="partner-website" class="block text-sm font-medium text-slate-700">Site web</label>
                        <div class="relative mt-1.5">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <x-icon name="link" class="h-4 w-4" />
                            </span>
                            <input
                                type="url"
                                id="partner-website"
                                name="site_web"
                                placeholder="https://www.exemple.com"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="partner-status" class="block text-sm font-medium text-slate-700">
                            Statut
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <select
                            id="partner-status"
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
