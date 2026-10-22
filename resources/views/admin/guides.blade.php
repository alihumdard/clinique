@extends('layouts.admin')

@section('title', 'Gestion des fiches pratiques')
@section('search_placeholder', 'Rechercher une fiche pratique, un sujet, un mot-clé...')

@section('content')

    <x-admin.page-header
        title="Gestion des fiches pratiques"
        lead="Ajoutez, modifiez et organisez les guides pratiques juridiques (PDF)."
        action-label="Ajouter un guide pratique"
        :action-href="route('admin.guides')"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Fiches pratiques'],
        ]"
    />

    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-4">

        <div class="space-y-5 xl:col-span-3">

            {{-- ============ KPIs ============ --}}
            {{-- Counts are illustrative until Module 2 supplies real queries. --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card icon="document" color="blue"  value="28"    label="Fiches publiées"  trend="+27%" note="cette année" />
                <x-admin.stat-card icon="download" color="green" value="1 845" label="Téléchargements"  trend="+35%" note="par rapport au mois dernier" />
                <x-admin.stat-card icon="folder"   color="amber" value="6"     label="Catégories"       note="organisées" />
                <x-admin.stat-card icon="clock"    color="red"   value="3"     label="En brouillon"     note="non publiées" />
            </div>

            {{-- ============ TABLE ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white">

                <div class="flex flex-wrap items-center gap-3 p-5">
                    <form role="search" class="relative min-w-0 flex-1 sm:max-w-xs">
                        <label for="guides-search" class="sr-only">Rechercher une fiche pratique</label>
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input
                            type="search"
                            id="guides-search"
                            name="q"
                            placeholder="Rechercher une fiche..."
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </form>

                    @php
                        $selects = [
                            ['id' => 'guides-category', 'label' => 'Filtrer par catégorie', 'options' => ['Toutes les catégories', 'Droits des femmes', 'Protection & sécurité', 'Accès à la justice', 'Étudiants', 'Droit foncier', 'Enfance & jeunesse', 'Droit administratif', 'Résolution des conflits', 'Inclusion', 'Égalité']],
                            ['id' => 'guides-status',   'label' => 'Filtrer par statut',    'options' => ['Tous les statuts', 'Publié', 'Brouillon', 'Archivé']],
                            ['id' => 'guides-sort',     'label' => 'Trier les résultats',   'options' => ['Trier par date', 'Plus téléchargées', 'Ordre alphabétique', 'Catégorie']],
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
                    $guides = [
                        ['cover' => 'placeholders/news-guide',          'title' => 'Droits des femmes en milieu universitaire', 'category' => 'Droits des femmes',       'tone' => 'pink',   'size' => '1.2 MB', 'downloads' => '425', 'date' => '15 juin 2025',  'time' => '14:32', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/news-3',              'title' => 'Procédure de plainte en cas de harcèlement','category' => 'Protection & sécurité',   'tone' => 'purple', 'size' => '0.8 MB', 'downloads' => '312', 'date' => '8 juin 2025',   'time' => '11:20', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/about-legal',         'title' => 'Accès à la justice pour tous',              'category' => 'Accès à la justice',      'tone' => 'blue',   'size' => '1.5 MB', 'downloads' => '689', 'date' => '28 mai 2025',   'time' => '16:45', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/about-team',          'title' => 'Guide sur les droits des étudiants',        'category' => 'Étudiants',               'tone' => 'amber',  'size' => '1.1 MB', 'downloads' => '502', 'date' => '20 mai 2025',   'time' => '09:15', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/news-1',              'title' => 'Aide juridique en cas de litige foncier',   'category' => 'Droit foncier',           'tone' => 'green',  'size' => '2.3 MB', 'downloads' => '298', 'date' => '12 avril 2025', 'time' => '13:18', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/news-candidatures',   'title' => 'Protection des mineurs',                    'category' => 'Enfance & jeunesse',      'tone' => 'green',  'size' => '1.0 MB', 'downloads' => '361', 'date' => '5 avril 2025',  'time' => '10:22', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/article-highlight-1', 'title' => 'Guide sur les procédures administratives',  'category' => 'Droit administratif',     'tone' => 'blue',   'size' => '1.8 MB', 'downloads' => '428', 'date' => '25 mars 2025',  'time' => '15:40', 'status' => 'Brouillon', 'statusTone' => 'amber'],
                        ['cover' => 'placeholders/news-partenariat',    'title' => 'Médiation communautaire',                   'category' => 'Résolution des conflits', 'tone' => 'purple', 'size' => '1.4 MB', 'downloads' => '276', 'date' => '18 mars 2025',  'time' => '11:05', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/news-2',              'title' => 'Droits des personnes en situation de handicap', 'category' => 'Inclusion',           'tone' => 'blue',   'size' => '1.6 MB', 'downloads' => '319', 'date' => '10 mars 2025',  'time' => '09:40', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['cover' => 'placeholders/article-highlight-2', 'title' => 'Lutte contre les discriminations',          'category' => 'Égalité',                 'tone' => 'red',    'size' => '0.9 MB', 'downloads' => '404', 'date' => '2 mars 2025',   'time' => '14:25', 'status' => 'Publié',    'statusTone' => 'green'],
                    ];
                @endphp

                <p class="flex items-center gap-1.5 px-5 pt-1 text-xs text-slate-500 lg:hidden">
                    <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0" />
                    Faites glisser le tableau pour voir toutes les colonnes
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[64rem] text-left">
                        <thead>
                            <tr class="border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <th scope="col" class="w-12 px-5 py-3">
                                    <input
                                        type="checkbox"
                                        aria-label="Sélectionner toutes les fiches"
                                        class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                    >
                                </th>
                                <th scope="col" class="w-12 px-2 py-3">#</th>
                                <th scope="col" class="w-20 px-2 py-3">Aperçu</th>
                                <th scope="col" class="px-3 py-3">Titre de la fiche pratique</th>
                                <th scope="col" class="px-3 py-3">Catégorie</th>
                                <th scope="col" class="px-3 py-3">Format</th>
                                <th scope="col" class="px-3 py-3">Téléchargements</th>
                                <th scope="col" class="px-3 py-3">Date d'ajout</th>
                                <th scope="col" class="px-3 py-3">Statut</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($guides as $i => $guide)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <input
                                            type="checkbox"
                                            aria-label="Sélectionner : {{ $guide['title'] }}"
                                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                    </td>

                                    <td class="px-2 py-3 text-sm text-slate-500">{{ $i + 1 }}</td>

                                    <td class="px-2 py-3">
                                        <x-picture
                                            :path="$guide['cover']"
                                            alt=""
                                            width="80" height="100"
                                            class="h-12 w-10 rounded border border-slate-200 object-cover"
                                        />
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="block max-w-[13rem] text-sm font-semibold leading-snug text-slate-900">
                                            {{ $guide['title'] }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$guide['tone']">{{ $guide['category'] }}</x-admin.badge>
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="flex items-start gap-1.5">
                                            <x-icon name="document" class="mt-0.5 h-4 w-4 shrink-0 text-rose-500" />
                                            <span class="leading-tight">
                                                <span class="block text-sm text-slate-700">PDF</span>
                                                <span class="block text-xs text-slate-400">{{ $guide['size'] }}</span>
                                            </span>
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="inline-flex items-center gap-1.5 text-sm text-slate-600">
                                            <x-icon name="download" class="h-4 w-4 shrink-0 text-slate-400" />
                                            {{ $guide['downloads'] }}
                                            <span class="sr-only">téléchargements</span>
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-3 text-sm leading-tight text-slate-500">
                                        {{ $guide['date'] }}<br>
                                        <span class="text-xs">{{ $guide['time'] }}</span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$guide['statusTone']">{{ $guide['status'] }}</x-admin.badge>
                                    </td>

                                    <td class="px-5 py-3">
                                        <x-admin.row-actions :label="$guide['title']" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-admin.table-footer
                    :from="1" :to="10" :total="28" unit="fiches pratiques"
                    :current="1" :pages="3"
                />
            </section>
        </div>

        {{-- ============ ADD GUIDE ============ --}}
        {{--
            Presentation only: Module 2 adds the POST route and server-side
            validation. PDFs in particular must be validated by content, not
            by extension, and stored outside the public web root with
            downloads served through an authorised controller.
        --}}
        <aside>
            <section class="rounded-xl border border-slate-200 bg-white p-5">

                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="document" class="h-5 w-5 text-primary-600" />
                    Ajouter un guide pratique
                </h2>

                <form method="POST" action="#" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="guide-title" class="block text-sm font-medium text-slate-700">
                            Titre de la fiche
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <input
                            type="text"
                            id="guide-title"
                            name="titre"
                            required
                            placeholder="Ex. Accès à la justice pour tous"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="guide-category" class="block text-sm font-medium text-slate-700">
                            Catégorie
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <select
                            id="guide-category"
                            name="categorie"
                            required
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option value="">Sélectionner une catégorie</option>
                            <option>Droits des femmes</option>
                            <option>Protection &amp; sécurité</option>
                            <option>Accès à la justice</option>
                            <option>Étudiants</option>
                            <option>Droit foncier</option>
                            <option>Enfance &amp; jeunesse</option>
                            <option>Droit administratif</option>
                            <option>Résolution des conflits</option>
                            <option>Inclusion</option>
                            <option>Égalité</option>
                        </select>
                    </div>

                    <div>
                        <label for="guide-description" class="block text-sm font-medium text-slate-700">
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
                                id="guide-description"
                                name="description"
                                rows="4"
                                required
                                placeholder="Décrivez brièvement le contenu de la fiche..."
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
                            for="guide-pdf"
                            class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
                        >
                            <x-icon name="upload" class="h-7 w-7 text-primary-500" />
                            <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour télécharger</span>
                            <span class="mt-0.5 text-xs text-slate-500">un fichier PDF ici</span>
                            <span class="mt-1 text-xs text-slate-400">(Taille max : 10MB)</span>

                            <input
                                type="file"
                                id="guide-pdf"
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
                            for="guide-cover"
                            class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
                        >
                            <x-icon name="image" class="h-7 w-7 text-primary-500" />
                            <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour importer une image</span>
                            <span class="mt-1 text-xs text-slate-400">(JPG, PNG - Max 2MB)</span>

                            <input
                                type="file"
                                id="guide-cover"
                                name="couverture"
                                accept="image/jpeg,image/png"
                                class="sr-only"
                            >
                        </label>
                    </div>

                    <div>
                        <label for="guide-status" class="block text-sm font-medium text-slate-700">
                            Statut
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <select
                            id="guide-status"
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
