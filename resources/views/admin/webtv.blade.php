@extends('layouts.admin')

@section('title', 'Gestion de la Web TV')
@section('search_placeholder', 'Rechercher une vidéo, un titre, une catégorie...')

@section('content')

    <x-admin.page-header
        title="Gestion de la Web TV"
        lead="Ajoutez, modifiez et organisez toutes les capsules vidéo de la Clinique Juridique FSJP-UAM."
        action-label="Ajouter une vidéo"
        :action-href="route('admin.webtv')"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Web TV'],
        ]"
    />

    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-4">

        <div class="space-y-5 xl:col-span-3">

            {{-- ============ KPIs ============ --}}
            {{-- Counts are illustrative until Module 2 supplies real queries. --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card icon="play-circle" color="blue"  value="12"    label="Vidéos publiées"    trend="+20%" note="ce mois-ci" />
                <x-admin.stat-card icon="eye"         color="green" value="1 245" label="Vues totales"       trend="+32%" note="par rapport au mois dernier" />
                <x-admin.stat-card icon="clock"       color="amber" value="3"     label="Vidéos en brouillon" note="Non publiées" />
                <x-admin.stat-card icon="trash"       color="red"   value="1"     label="Vidéo archivée" />
            </div>

            {{-- ============ TABLE ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white">

                <div class="flex flex-wrap items-center gap-3 p-5">
                    <form role="search" class="relative min-w-0 flex-1 sm:max-w-xs">
                        <label for="webtv-search" class="sr-only">Rechercher une vidéo</label>
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input
                            type="search"
                            id="webtv-search"
                            name="q"
                            placeholder="Rechercher une vidéo..."
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </form>

                    @php
                        $selects = [
                            ['id' => 'webtv-category', 'label' => 'Filtrer par catégorie', 'options' => ['Toutes les catégories', 'Conférences', 'Sensibilisation', 'Terrain', 'Interviews', 'Capsules', 'Événements', 'Fiches pratiques', 'FAQ', 'Témoignages']],
                            ['id' => 'webtv-status',   'label' => 'Filtrer par statut',    'options' => ['Tous les statuts', 'Publié', 'Brouillon', 'Archivé']],
                            ['id' => 'webtv-date',     'label' => 'Filtrer par date',      'options' => ['Toutes les dates', 'Ce mois-ci', 'Ce trimestre', 'Cette année']],
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
                    $videos = [
                        ['image' => 'placeholders/news-3',            'title' => 'Accès à la justice pour tous',     'category' => 'Conférences',      'tone' => 'blue',   'duration' => '12:35', 'views' => '452', 'date' => '10 juin 2025', 'time' => '14:32', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['image' => 'placeholders/about-legal',       'title' => 'Les droits des femmes en pratique','category' => 'Sensibilisation',  'tone' => 'pink',   'duration' => '08:20', 'views' => '320', 'date' => '8 juin 2025',  'time' => '11:20', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['image' => 'placeholders/news-caravan',      'title' => 'Caravane juridique à Zinder',      'category' => 'Terrain',          'tone' => 'green',  'duration' => '15:10', 'views' => '615', 'date' => '5 juin 2025',  'time' => '09:15', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['image' => 'placeholders/about-team',        'title' => 'Entretien avec un magistrat',      'category' => 'Interviews',       'tone' => 'amber',  'duration' => '18:45', 'views' => '298', 'date' => '1 juin 2025',  'time' => '12:10', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['image' => 'placeholders/video-featured',    'title' => 'Capsule : Comprendre la justice',  'category' => 'Capsules',         'tone' => 'purple', 'duration' => '06:30', 'views' => '501', 'date' => '28 mai 2025',  'time' => '16:45', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['image' => 'placeholders/news-1',            'title' => 'Table ronde : Justice sociale',    'category' => 'Événements',       'tone' => 'blue',   'duration' => '25:40', 'views' => '722', 'date' => '25 mai 2025',  'time' => '10:22', 'status' => 'Brouillon', 'statusTone' => 'amber'],
                        ['image' => 'placeholders/news-guide',        'title' => 'Fiche pratique en vidéo',          'category' => 'Fiches pratiques', 'tone' => 'green',  'duration' => '09:15', 'views' => '410', 'date' => '20 mai 2025',  'time' => '15:30', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['image' => 'placeholders/news-2',            'title' => 'Caravane de Maradi',               'category' => 'Terrain',          'tone' => 'green',  'duration' => '13:36', 'views' => '369', 'date' => '16 mai 2025',  'time' => '13:18', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['image' => 'placeholders/article-highlight-2','title' => 'Questions fréquentes en droit',   'category' => 'FAQ',              'tone' => 'red',    'duration' => '07:50', 'views' => '287', 'date' => '12 mai 2025',  'time' => '09:40', 'status' => 'Publié',    'statusTone' => 'green'],
                        ['image' => 'placeholders/news-candidatures', 'title' => 'Témoignages des bénéficiaires',    'category' => 'Témoignages',      'tone' => 'purple', 'duration' => '11:20', 'views' => '533', 'date' => '8 mai 2025',   'time' => '11:05', 'status' => 'Archivé',   'statusTone' => 'red'],
                    ];
                @endphp

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[60rem] text-left">
                        <thead>
                            <tr class="border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <th scope="col" class="w-12 px-5 py-3">
                                    <input
                                        type="checkbox"
                                        aria-label="Sélectionner toutes les vidéos"
                                        class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                    >
                                </th>
                                <th scope="col" class="w-12 px-2 py-3">#</th>
                                <th scope="col" class="w-24 px-2 py-3">Miniature</th>
                                <th scope="col" class="px-3 py-3">Titre de la vidéo</th>
                                <th scope="col" class="px-3 py-3">Catégorie</th>
                                <th scope="col" class="px-3 py-3">Durée</th>
                                <th scope="col" class="px-3 py-3">Vues</th>
                                <th scope="col" class="px-3 py-3">Date</th>
                                <th scope="col" class="px-3 py-3">Statut</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($videos as $i => $video)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <input
                                            type="checkbox"
                                            aria-label="Sélectionner : {{ $video['title'] }}"
                                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                    </td>

                                    <td class="px-2 py-3 text-sm text-slate-500">{{ $i + 1 }}</td>

                                    <td class="px-2 py-3">
                                        {{-- Duration sits on the thumbnail, as it does on the public site --}}
                                        <span class="relative block w-16">
                                            <x-picture
                                                :path="$video['image']"
                                                alt=""
                                                width="128" height="72"
                                                class="h-11 w-16 rounded-md object-cover"
                                            />
                                            <span class="absolute bottom-0.5 right-0.5 rounded bg-slate-900/80 px-1 text-[10px] font-medium text-white">
                                                {{ $video['duration'] }}
                                            </span>
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="block max-w-[14rem] text-sm font-semibold leading-snug text-slate-900">
                                            {{ $video['title'] }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$video['tone']">{{ $video['category'] }}</x-admin.badge>
                                    </td>

                                    <td class="px-3 py-3 text-sm text-slate-500">{{ $video['duration'] }}</td>

                                    <td class="px-3 py-3 text-sm text-slate-500">{{ $video['views'] }}</td>

                                    <td class="px-3 py-3 text-sm leading-tight text-slate-500">
                                        {{ $video['date'] }}<br>
                                        <span class="text-xs">{{ $video['time'] }}</span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$video['statusTone']">{{ $video['status'] }}</x-admin.badge>
                                    </td>

                                    <td class="px-5 py-3">
                                        <x-admin.row-actions :label="$video['title']" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-admin.table-footer
                    :from="1" :to="10" :total="12" unit="vidéos"
                    :current="1" :pages="2"
                />
            </section>
        </div>

        {{-- ============ ADD VIDEO ============ --}}
        {{--
            Presentation only: Module 2 adds the POST route, server-side
            validation and the video-link parser. The accept and size hints
            are conveniences, never the security boundary.
        --}}
        <aside>
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="play-circle" class="h-5 w-5 text-primary-600" />
                    Ajouter une vidéo
                </h2>

                <form method="POST" action="#" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label for="video-title" class="block text-sm font-medium text-slate-700">
                            Titre de la vidéo
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <input
                            type="text"
                            id="video-title"
                            name="titre"
                            required
                            placeholder="Saisissez le titre..."
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="video-category" class="block text-sm font-medium text-slate-700">
                            Catégorie
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <select
                            id="video-category"
                            name="categorie"
                            required
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option value="">Sélectionner une catégorie</option>
                            <option>Conférences</option>
                            <option>Sensibilisation</option>
                            <option>Terrain</option>
                            <option>Interviews</option>
                            <option>Capsules</option>
                            <option>Événements</option>
                            <option>Témoignages</option>
                        </select>
                    </div>

                    <div>
                        <label for="video-description" class="block text-sm font-medium text-slate-700">Description</label>

                        {{-- Formatting controls are inert until Module 2 wires
                             an editor; they are buttons, never links. --}}
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
                                id="video-description"
                                name="description"
                                rows="4"
                                placeholder="Décrivez brièvement la vidéo..."
                                class="w-full resize-y border-0 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-0"
                            ></textarea>
                        </div>
                    </div>

                    <div>
                        <label for="video-link" class="block text-sm font-medium text-slate-700">
                            Lien vidéo
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <div class="relative mt-1.5">
                            <input
                                type="url"
                                id="video-link"
                                name="lien"
                                required
                                placeholder="Lien YouTube, Vimeo ou fichier vidéo"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-3.5 pr-10 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                            <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <x-icon name="link" class="h-4 w-4" />
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-slate-700">Image de couverture</span>

                        <label
                            for="video-cover"
                            class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
                        >
                            <x-icon name="upload" class="h-7 w-7 text-primary-500" />
                            <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour télécharger</span>
                            <span class="mt-0.5 text-xs text-slate-500">ou glissez une image ici</span>
                            <span class="mt-1 text-xs text-slate-400">(JPG, PNG - Max 2MB)</span>

                            <input
                                type="file"
                                id="video-cover"
                                name="couverture"
                                accept="image/jpeg,image/png"
                                class="sr-only"
                            >
                        </label>
                    </div>

                    <div>
                        <label for="video-duration" class="block text-sm font-medium text-slate-700">Durée (mm:ss)</label>
                        <div class="relative mt-1.5">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <x-icon name="clock" class="h-4 w-4" />
                            </span>
                            <input
                                type="text"
                                id="video-duration"
                                name="duree"
                                inputmode="numeric"
                                pattern="[0-9]{1,3}:[0-9]{2}"
                                placeholder="Ex: 12:35"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                        </div>
                    </div>

                    <div class="space-y-3 pt-1">
                        <x-admin.toggle name="publier" label="Publier immédiatement" :checked="true" />
                        <x-admin.toggle name="commentaires" label="Autoriser les commentaires" />
                    </div>

                    <div class="space-y-2.5 pt-1">
                        <button
                            type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-primary-700"
                        >
                            <x-icon name="paper-plane" class="h-4 w-4" />
                            Publier la vidéo
                        </button>

                        <button
                            type="submit"
                            name="brouillon"
                            value="1"
                            class="flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-50"
                        >
                            <x-icon name="save" class="h-4 w-4" />
                            Enregistrer en brouillon
                        </button>
                    </div>
                </form>
            </section>
        </aside>
    </div>

@endsection
