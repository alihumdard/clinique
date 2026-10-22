@extends('layouts.admin')

@section('title', "Gestion des rapports annuels d'activité")
@section('search_placeholder', 'Rechercher un rapport, une année...')

@section('content')

    <x-admin.page-header
        title="Gestion des rapports annuels d'activité"
        lead="Ajoutez, modifiez et organisez les rapports annuels d'activité de la Clinique Juridique FSJP-UAM."
        action-label="Ajouter un rapport annuel"
        :action-href="route('admin.reports')"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Rapports annuels'],
        ]"
    />

    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-4">

        <div class="space-y-5 xl:col-span-3">

            {{-- ============ KPIs ============ --}}
            {{-- Counts are illustrative until Module 2 supplies real queries. --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card icon="document" color="blue"  value="8"     label="Rapports publiés" trend="+33%" note="par rapport à l'année dernière" />
                <x-admin.stat-card icon="download" color="green" value="3 845" label="Téléchargements"  trend="+28%" note="cette année" />
                <x-admin.stat-card icon="clock"    color="amber" value="1"     label="En brouillon"     note="non publié" />
                <x-admin.stat-card icon="folder"   color="red"   value="12"    label="Pages totales"    note="dans tous les rapports" />
            </div>

            {{-- ============ TABLE ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white">

                <div class="space-y-3 p-5">
                    <form role="search" class="relative w-full lg:max-w-xs">
                        <label for="reports-search" class="sr-only">Rechercher un rapport</label>
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input
                            type="search"
                            id="reports-search"
                            name="q"
                            placeholder="Rechercher un rapport..."
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </form>

                    @php
                        $selects = [
                            ['id' => 'reports-year',   'label' => 'Filtrer par année',   'options' => ['Toutes les années', '2024', '2023', '2022', '2021', '2020', '2019', '2018', '2017', '2016', '2015']],
                            ['id' => 'reports-status', 'label' => 'Filtrer par statut',  'options' => ['Tous les statuts', 'Publié', 'Brouillon', 'Archivé']],
                            ['id' => 'reports-sort',   'label' => 'Trier les résultats', 'options' => ['Trier par date', 'Plus téléchargés', 'Année décroissante', 'Année croissante']],
                        ];
                    @endphp

                    <div class="grid grid-cols-2 gap-3 sm:flex sm:flex-wrap sm:items-center">
                        @foreach ($selects as $select)
                            <div class="min-w-0 sm:shrink-0">
                                <label for="{{ $select['id'] }}" class="sr-only">{{ $select['label'] }}</label>
                                <select
                                    id="{{ $select['id'] }}"
                                    class="w-full rounded-lg border border-slate-200 py-2.5 pl-3.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-auto"
                                >
                                    @foreach ($select['options'] as $option)
                                        <option>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach

                        <button type="button" class="col-span-2 inline-flex w-full shrink-0 items-center justify-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50 sm:col-span-1 sm:w-auto">
                            <x-icon name="filter" class="h-4 w-4" />
                            Filtrer
                        </button>
                    </div>
                </div>

                @php
                    $reports = [
                        ['cover' => 'placeholders/about-legal',          'year' => '2024', 'size' => '2.4 MB', 'downloads' => '842', 'date' => '15 juin 2025',  'time' => '14:32', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/article-highlight-1',  'year' => '2023', 'size' => '3.1 MB', 'downloads' => '756', 'date' => '12 juin 2025',  'time' => '11:20', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/article-highlight-2',  'year' => '2022', 'size' => '2.8 MB', 'downloads' => '689', 'date' => '28 mai 2025',   'time' => '16:45', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/article-highlight-3',  'year' => '2021', 'size' => '4.2 MB', 'downloads' => '512', 'date' => '20 mai 2025',   'time' => '09:15', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/news-guide',           'year' => '2020', 'size' => '2.1 MB', 'downloads' => '498', 'date' => '12 avril 2025', 'time' => '13:18', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/news-1',               'year' => '2019', 'size' => '1.9 MB', 'downloads' => '421', 'date' => '5 avril 2025',  'time' => '10:22', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/news-2',               'year' => '2018', 'size' => '2.6 MB', 'downloads' => '356', 'date' => '25 mars 2025',  'time' => '15:40', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/news-3',               'year' => '2017', 'size' => '2.3 MB', 'downloads' => '298', 'date' => '18 mars 2025',  'time' => '11:05', 'status' => 'Brouillon', 'statusTone' => 'amber'],
                        ['cover' => 'placeholders/news-partenariat',     'year' => '2016', 'size' => '1.8 MB', 'downloads' => '276', 'date' => '10 mars 2025',  'time' => '09:40', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/news-caravan',         'year' => '2015', 'size' => '2.0 MB', 'downloads' => '319', 'date' => '2 mars 2025',   'time' => '14:25', 'status' => 'Publié',    'statusTone' => 'green'],
                    ];
                @endphp

                <p class="flex items-center gap-1.5 px-5 pt-1 text-xs text-slate-500 lg:hidden">
                    <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0" />
                    Faites glisser le tableau pour voir toutes les colonnes
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[62rem] text-left">
                        <thead>
                            <tr class="border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <th scope="col" class="w-12 px-5 py-3">
                                    <input
                                        type="checkbox"
                                        aria-label="Sélectionner tous les rapports"
                                        class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                    >
                                </th>
                                <th scope="col" class="w-12 px-2 py-3">#</th>
                                <th scope="col" class="w-20 px-2 py-3">Couverture</th>
                                <th scope="col" class="min-w-[14rem] px-3 py-3">Titre du rapport</th>
                                <th scope="col" class="px-3 py-3">Année</th>
                                <th scope="col" class="px-3 py-3">Format</th>
                                <th scope="col" class="px-3 py-3">Téléchargements</th>
                                <th scope="col" class="px-3 py-3">Date d'ajout</th>
                                <th scope="col" class="px-3 py-3">Statut</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($reports as $i => $report)
                                @php $title = "Rapport annuel d'activité " . $report['year']; @endphp

                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <input
                                            type="checkbox"
                                            aria-label="Sélectionner : {{ $title }}"
                                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                    </td>

                                    <td class="px-2 py-3 text-sm text-slate-500">{{ $i + 1 }}</td>

                                    <td class="px-2 py-3">
                                        <x-picture
                                            :path="$report['cover']"
                                            alt=""
                                            width="80" height="100"
                                            class="h-12 w-10 rounded border border-slate-200 object-cover"
                                        />
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="block max-w-[12rem] text-sm font-semibold leading-snug text-slate-900">
                                            {{ $title }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3 text-sm font-medium text-slate-700">{{ $report['year'] }}</td>

                                    <td class="px-3 py-3">
                                        <span class="flex items-start gap-1.5">
                                            <x-icon name="document" class="mt-0.5 h-4 w-4 shrink-0 text-rose-500" />
                                            <span class="leading-tight">
                                                <span class="block text-sm text-slate-700">PDF</span>
                                                <span class="block text-xs text-slate-400">{{ $report['size'] }}</span>
                                            </span>
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="inline-flex items-center gap-1.5 text-sm text-slate-600">
                                            <x-icon name="download" class="h-4 w-4 shrink-0 text-slate-400" />
                                            {{ $report['downloads'] }}
                                            <span class="sr-only">téléchargements</span>
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-3 text-sm leading-tight text-slate-500">
                                        {{ $report['date'] }}<br>
                                        <span class="text-xs">{{ $report['time'] }}</span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$report['statusTone']">{{ $report['status'] }}</x-admin.badge>
                                    </td>

                                    <td class="px-5 py-3">
                                        <x-admin.row-actions :label="$title" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-admin.table-footer
                    :from="1" :to="10" :total="8" unit="rapports"
                    :current="1" :pages="1"
                />
            </section>
        </div>

        {{-- ============ ADD REPORT ============ --}}
        {{--
            Presentation only: Module 2 adds the POST route and server-side
            validation. PDFs must be validated by content rather than
            extension, stored outside the public web root, and served
            through a controller that records the download.
        --}}
        <aside>
            <section class="rounded-xl border border-slate-200 bg-white p-5">

                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="document" class="h-5 w-5 text-primary-600" />
                    Ajouter un rapport annuel
                </h2>

                <form method="POST" action="#" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="report-title" class="block text-sm font-medium text-slate-700">
                            Titre du rapport
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <input
                            type="text"
                            id="report-title"
                            name="titre"
                            required
                            placeholder="Ex. Rapport annuel d'activité 2024"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="report-year" class="block text-sm font-medium text-slate-700">
                            Année
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <select
                            id="report-year"
                            name="annee"
                            required
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option value="">Sélectionner l'année</option>
                            @for ($year = (int) date('Y'); $year >= 2015; $year--)
                                <option>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="report-description" class="block text-sm font-medium text-slate-700">
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
                                id="report-description"
                                name="description"
                                rows="4"
                                required
                                placeholder="Décrivez brièvement le contenu du rapport..."
                                class="w-full resize-y border-0 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-0"
                            ></textarea>
                        </div>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-slate-700">
                            Fichier PDF
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </span>

                        <label
                            for="report-pdf"
                            class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
                        >
                            <x-icon name="upload" class="h-7 w-7 text-primary-500" />
                            <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour télécharger</span>
                            <span class="mt-0.5 text-xs text-slate-500">un fichier PDF</span>
                            <span class="mt-1 text-xs text-slate-400">(Taille max : 20MB)</span>

                            <input
                                type="file"
                                id="report-pdf"
                                name="fichier"
                                accept="application/pdf"
                                required
                                class="sr-only"
                            >
                        </label>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-slate-700">Image de couverture</span>

                        <label
                            for="report-cover"
                            class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
                        >
                            <x-icon name="image" class="h-7 w-7 text-primary-500" />
                            <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour importer une image</span>
                            <span class="mt-1 text-xs text-slate-400">(JPG, PNG - Max 2MB)</span>

                            <input
                                type="file"
                                id="report-cover"
                                name="couverture"
                                accept="image/jpeg,image/png"
                                class="sr-only"
                            >
                        </label>
                    </div>

                    <div>
                        <label for="report-status" class="block text-sm font-medium text-slate-700">
                            Statut
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <select
                            id="report-status"
                            name="statut"
                            required
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option>Publié</option>
                            <option>Brouillon</option>
                            <option>Archivé</option>
                        </select>
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
