@extends('layouts.admin')

@section('title', 'Gestion des activités & galerie')
@section('search_placeholder', 'Rechercher une activité, un lieu, une date...')

@section('content')

    <x-admin.page-header
        title="Gestion des activités &amp; galerie"
        lead="Gérez les activités, caravanes juridiques et leur galerie photos."
        action-label="Ajouter une activité"
        :action-href="route('admin.activities.create')"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Activités & caravanes'],
        ]"
    />
    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-4">

        <div class="space-y-5 xl:col-span-3">

            {{-- ============ KPIs ============ --}}
            {{-- Counts are illustrative until Module 2 supplies real queries. --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card icon="calendar"     color="blue"  value="24"  label="Activités publiées"  trend="+20%" note="ce mois-ci" />
                <x-admin.stat-card icon="clock"        color="amber" value="3"   label="À venir"             note="prochaines activités" />
                <x-admin.stat-card icon="check-circle" color="green" value="18"  label="Activités terminées" note="cette année" />
                <x-admin.stat-card icon="image"        color="red"   value="356" label="Photos"              note="dans la galerie" />
            </div>

            {{-- ============ TABLE ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white">

                <div class="flex flex-col gap-4 p-5 lg:flex-row lg:items-center lg:justify-between">
                    <x-admin.filter-tabs
                        class="lg:border-b-0"
                        :tabs="[
                            ['label' => 'Toutes les activités', 'count' => 24, 'active' => true],
                            ['label' => 'À venir',              'count' => 3],
                            ['label' => 'Passées',              'count' => 18],
                            ['label' => 'Caravanes juridiques', 'count' => 8],
                        ]"
                    />

                    <div class="flex shrink-0 flex-wrap items-center gap-3">
                        <form role="search" class="relative w-full min-w-[12rem] flex-1 sm:w-56">
                            <label for="activities-search" class="sr-only">Rechercher une activité</label>
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <x-icon name="search" class="h-4 w-4" />
                            </span>
                            <input
                                type="search"
                                id="activities-search"
                                name="q"
                                placeholder="Rechercher une activité..."
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                        </form>

                        <button type="button" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50">
                            <x-icon name="filter" class="h-4 w-4" />
                            Filtrer
                        </button>
                    </div>
                </div>

                @php
                    $activities = [
                        ['image' => 'placeholders/news-caravan',      'title' => 'Caravane juridique à Zinder',      'type' => 'Caravane',       'tone' => 'purple', 'place' => 'Zinder',     'date' => '15 juin 2025',  'status' => 'Publié',  'statusTone' => 'green', 'photos' => 12],
                        ['image' => 'placeholders/news-3',            'title' => 'Sensibilisation sur les droits des femmes', 'type' => 'Sensibilisation', 'tone' => 'pink', 'place' => 'Niamey', 'date' => '8 juin 2025', 'status' => 'Publié', 'statusTone' => 'green', 'photos' => 18],
                        ['image' => 'placeholders/news-formation',    'title' => 'Formation des étudiants en droit', 'type' => 'Formation',      'tone' => 'blue',   'place' => 'UAM Niamey', 'date' => '28 mai 2025',   'status' => 'Publié',  'statusTone' => 'green', 'photos' => 24],
                        ['image' => 'placeholders/news-2',            'title' => 'Caravane juridique à Maradi',      'type' => 'Caravane',       'tone' => 'purple', 'place' => 'Maradi',     'date' => '20 mai 2025',   'status' => 'Publié',  'statusTone' => 'green', 'photos' => 15],
                        ['image' => 'placeholders/about-team',        'title' => 'Table ronde : Justice et société', 'type' => 'Conférence',     'tone' => 'amber',  'place' => 'Niamey',     'date' => '12 mai 2025',   'status' => 'Publié',  'statusTone' => 'green', 'photos' => 20],
                        ['image' => 'placeholders/news-1',            'title' => 'Service juridique de proximité',   'type' => 'Terrain',        'tone' => 'green',  'place' => 'Dosso',      'date' => '5 mai 2025',    'status' => 'Publié',  'statusTone' => 'green', 'photos' => 10],
                        ['image' => 'placeholders/news-candidatures', 'title' => "Atelier sur l'aide juridique",     'type' => 'Formation',      'tone' => 'blue',   'place' => 'Tahoua',     'date' => '25 avril 2025', 'status' => 'Terminé', 'statusTone' => 'blue',  'photos' => 14],
                        ['image' => 'placeholders/about-hero',        'title' => 'Caravane à Agadez',                'type' => 'Caravane',       'tone' => 'purple', 'place' => 'Agadez',     'date' => '18 avril 2025', 'status' => 'Terminé', 'statusTone' => 'blue',  'photos' => 16],
                        ['image' => 'placeholders/news-partenariat',  'title' => 'Rencontre avec les communautés',   'type' => 'Événement',      'tone' => 'pink',   'place' => 'Tillabéri',  'date' => '10 avril 2025', 'status' => 'Terminé', 'statusTone' => 'blue',  'photos' => 22],
                        ['image' => 'placeholders/about-legal',       'title' => 'Journée portes ouvertes',          'type' => 'Événement',      'tone' => 'pink',   'place' => 'Niamey',     'date' => '2 avril 2025',  'status' => 'Publié',  'statusTone' => 'green', 'photos' => 28],
                    ];
                @endphp

                <p class="flex items-center gap-1.5 px-5 pt-1 text-xs text-slate-500 lg:hidden">
                    <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0" />
                    Faites glisser le tableau pour voir toutes les colonnes
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[62rem] text-left">
                        <thead>
                            <tr class="whitespace-nowrap border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <th scope="col" class="w-12 px-5 py-3">
                                    <input
                                        type="checkbox"
                                        aria-label="Sélectionner toutes les activités"
                                        class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                    >
                                </th>
                                <th scope="col" class="w-12 px-2 py-3">#</th>
                                <th scope="col" class="w-20 px-2 py-3">Image</th>
                                <th scope="col" class="min-w-[14rem] px-3 py-3">Titre de l'activité</th>
                                <th scope="col" class="px-3 py-3">Type</th>
                                <th scope="col" class="px-3 py-3">Lieu</th>
                                <th scope="col" class="px-3 py-3">Date</th>
                                <th scope="col" class="px-3 py-3">Statut</th>
                                <th scope="col" class="px-3 py-3">Galerie</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($activities as $i => $activity)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <input
                                            type="checkbox"
                                            aria-label="Sélectionner : {{ $activity['title'] }}"
                                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                    </td>

                                    <td class="px-2 py-3 text-sm text-slate-500">{{ $i + 1 }}</td>

                                    <td class="px-2 py-3">
                                        <x-picture
                                            :path="$activity['image']"
                                            alt=""
                                            width="96" height="72"
                                            class="h-11 w-14 rounded-md object-cover"
                                        />
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="block max-w-[13rem] text-sm font-semibold leading-snug text-slate-900">
                                            {{ $activity['title'] }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$activity['tone']">{{ $activity['type'] }}</x-admin.badge>
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap text-sm text-slate-600">
                                            <x-icon name="map-pin" class="h-4 w-4 shrink-0 text-slate-400" />
                                            {{ $activity['place'] }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap text-sm text-slate-600">
                                            <x-icon name="calendar" class="h-4 w-4 shrink-0 text-slate-400" />
                                            {{ $activity['date'] }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$activity['statusTone']">{{ $activity['status'] }}</x-admin.badge>
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="inline-flex items-center gap-1.5 text-sm text-slate-600">
                                            <x-icon name="image" class="h-4 w-4 shrink-0 text-slate-400" />
                                            {{ $activity['photos'] }}
                                            <span class="sr-only">photos dans la galerie</span>
                                        </span>
                                    </td>

                                    <td class="px-5 py-3">
                                        <x-admin.row-actions :label="$activity['title']" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-admin.table-footer
                    :from="1" :to="10" :total="24" unit="activités"
                    :current="1" :pages="3"
                />
            </section>
        </div>

        {{-- ============ ADD / EDIT ACTIVITY ============ --}}
        {{--
            Presentation only: Module 2 adds the POST route, server-side
            validation and upload handling. The accept and size hints below
            are conveniences, never the security boundary.
        --}}
        <aside>
            <section class="rounded-xl border border-slate-200 bg-white p-5">

                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="calendar" class="h-5 w-5 text-primary-600" />
                    Ajouter / Modifier une activité
                </h2>

                <x-admin.filter-tabs
                    class="mt-4"
                    :tabs="[
                        ['label' => 'Informations générales', 'active' => true],
                        ['label' => 'Galerie photos'],
                        ['label' => 'Lieu &amp; date'],
                    ]"
                />

                <form method="POST" action="#" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="activity-title" class="block text-sm font-medium text-slate-700">
                            Titre de l'activité
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <input
                            type="text"
                            id="activity-title"
                            name="titre"
                            required
                            placeholder="Ex. Caravane juridique à Zinder"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="activity-type" class="block text-sm font-medium text-slate-700">
                            Type d'activité
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <select
                            id="activity-type"
                            name="type"
                            required
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option value="">Sélectionner un type</option>
                            <option>Caravane</option>
                            <option>Sensibilisation</option>
                            <option>Formation</option>
                            <option>Conférence</option>
                            <option>Terrain</option>
                            <option>Événement</option>
                        </select>
                    </div>

                    <div>
                        <label for="activity-description" class="block text-sm font-medium text-slate-700">
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
                                id="activity-description"
                                name="description"
                                rows="4"
                                required
                                placeholder="Décrivez l'activité en détail..."
                                class="w-full resize-y border-0 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-0"
                            ></textarea>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                                <x-icon name="image" class="h-5 w-5 text-primary-600" />
                                Galerie photos
                            </span>
                            <span class="text-xs text-slate-500">0 photo(s)</span>
                        </div>

                        <label
                            for="activity-gallery"
                            class="mt-2 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
                        >
                            <x-icon name="upload" class="h-7 w-7 text-primary-500" />
                            <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour ajouter des photos</span>
                            <span class="mt-0.5 text-xs text-slate-500">ou glissez vos images ici</span>
                            <span class="mt-1 text-xs text-slate-400">(JPG, PNG - Max 5MB chacune)</span>

                            <input
                                type="file"
                                id="activity-gallery"
                                name="photos[]"
                                accept="image/jpeg,image/png"
                                multiple
                                class="sr-only"
                            >
                        </label>
                    </div>

                    <div>
                        <label for="activity-status" class="block text-sm font-medium text-slate-700">
                            Statut
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <select
                            id="activity-status"
                            name="statut"
                            required
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option>Publié</option>
                            <option>À venir</option>
                            <option>Terminé</option>
                            <option>Brouillon</option>
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
