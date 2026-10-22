@extends('layouts.admin')

@section('title', 'Gestion de la newsletter')
@section('search_placeholder', 'Rechercher une newsletter, un sujet, un destinataire...')

@section('content')

    <x-admin.page-header
        title="Gestion de la newsletter"
        lead="Créez, gérez et envoyez les newsletters de la Clinique Juridique FSJP-UAM."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Newsletter'],
        ]"
    />

    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-4">

        <div class="space-y-5 xl:col-span-3">

            {{-- ============ KPIs ============ --}}
            {{-- Figures are illustrative until Module 2 supplies real sending data. --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card icon="mail"        color="blue"  value="12"    label="Newsletters envoyées" trend="+33%" note="cette année" />
                <x-admin.stat-card icon="users"       color="green" value="2 856" label="Abonnés"              trend="+18%" note="par rapport au mois dernier" />
                <x-admin.stat-card icon="paper-plane" color="amber" value="68%"   label="Taux d'ouverture"     note="moyenne" />
                <x-admin.stat-card icon="link"        color="red"   value="12%"   label="Taux de clic"         note="moyenne" />
            </div>

            {{-- ============ TABLE ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white">

                <div class="space-y-3 p-5">
                    <form role="search" class="relative w-full lg:max-w-xs">
                        <label for="newsletter-search" class="sr-only">Rechercher une newsletter</label>
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input
                            type="search"
                            id="newsletter-search"
                            name="q"
                            placeholder="Rechercher une newsletter..."
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </form>

                    @php
                        $selects = [
                            ['id' => 'newsletter-status', 'label' => 'Filtrer par statut',  'options' => ['Tous les statuts', 'Envoyée', 'Brouillon', 'Programmée']],
                            ['id' => 'newsletter-year',   'label' => 'Filtrer par année',   'options' => ['Toutes les années', '2025', '2024', '2023']],
                            ['id' => 'newsletter-sort',   'label' => 'Trier les résultats', 'options' => ['Trier par date', "Taux d'ouverture", 'Nombre de destinataires']],
                        ];
                    @endphp

                    <div class="grid grid-cols-2 gap-3 sm:flex sm:flex-wrap sm:items-center">
                        @foreach ($selects as $select)
                            <div class="min-w-0">
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
                    $newsletters = [
                        ['image' => 'placeholders/news-1',             'subject' => "Lettre d'information – Juin 2025",  'preview' => 'Actualités, formations...',      'date' => '15 juin 2025',    'time' => '10:00', 'recipients' => '2 856', 'opens' => '72%', 'clicks' => '14%'],
                        ['image' => 'placeholders/news-caravan',       'subject' => 'Caravane juridique à Zinder',       'preview' => "Retour sur l'événement",         'date' => '28 mai 2025',     'time' => '14:30', 'recipients' => '2 542', 'opens' => '68%', 'clicks' => '11%'],
                        ['image' => 'placeholders/news-guide',         'subject' => 'Nouvelles fiches pratiques disponibles', 'preview' => 'Accès aux guides PDF',       'date' => '12 mai 2025',     'time' => '09:15', 'recipients' => '2 481', 'opens' => '65%', 'clicks' => '10%'],
                        ['image' => 'placeholders/about-team',         'subject' => 'Appels à candidatures',             'preview' => 'Rejoignez notre équipe',         'date' => '25 avril 2025',   'time' => '11:00', 'recipients' => '2 320', 'opens' => '70%', 'clicks' => '16%'],
                        ['image' => 'placeholders/article-highlight-1','subject' => 'Rapport annuel 2024',               'preview' => 'Découvrez nos réalisations',     'date' => '10 mars 2025',    'time' => '10:20', 'recipients' => '2 180', 'opens' => '63%', 'clicks' => '9%'],
                        ['image' => 'placeholders/about-legal',        'subject' => 'Journée portes ouvertes',           'preview' => "Participez à l'événement",       'date' => '18 février 2025', 'time' => '14:45', 'recipients' => '2 120', 'opens' => '60%', 'clicks' => '8%'],
                        ['image' => 'placeholders/video-featured',     'subject' => 'Web TV : nouvelles vidéos',         'preview' => 'Capsules juridiques',            'date' => '5 janvier 2025',  'time' => '11:30', 'recipients' => '1 980', 'opens' => '58%', 'clicks' => '7%'],
                        ['image' => 'placeholders/news-3',             'subject' => 'Bonne année 2025',                  'preview' => 'Nos meilleurs vœux',             'date' => '2 janvier 2025',  'time' => '09:00', 'recipients' => '1 950', 'opens' => '75%', 'clicks' => '18%'],
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
                                        aria-label="Sélectionner toutes les newsletters"
                                        class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                    >
                                </th>
                                <th scope="col" class="w-12 px-2 py-3">#</th>
                                <th scope="col" class="min-w-[14rem] px-3 py-3">Sujet de la newsletter</th>
                                <th scope="col" class="px-3 py-3">Date d'envoi</th>
                                <th scope="col" class="px-3 py-3">Destinataires</th>
                                <th scope="col" class="px-3 py-3">Ouvertures</th>
                                <th scope="col" class="px-3 py-3">Clics</th>
                                <th scope="col" class="px-3 py-3">Statut</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($newsletters as $i => $letter)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <input
                                            type="checkbox"
                                            aria-label="Sélectionner : {{ $letter['subject'] }}"
                                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                    </td>

                                    <td class="px-2 py-3 text-sm text-slate-500">{{ $i + 1 }}</td>

                                    <td class="px-3 py-3">
                                        <span class="flex items-center gap-3">
                                            <x-picture
                                                :path="$letter['image']"
                                                alt=""
                                                width="96" height="72"
                                                class="h-11 w-14 shrink-0 rounded-md object-cover"
                                            />
                                            <span class="min-w-0 max-w-[13rem]">
                                                <span class="block text-sm font-semibold leading-snug text-slate-900">{{ $letter['subject'] }}</span>
                                                <span class="mt-0.5 block truncate text-xs text-slate-500">{{ $letter['preview'] }}</span>
                                            </span>
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-3 text-sm leading-tight text-slate-500">
                                        {{ $letter['date'] }}<br>
                                        <span class="text-xs">{{ $letter['time'] }}</span>
                                    </td>

                                    <td class="px-3 py-3 text-sm text-slate-700">{{ $letter['recipients'] }}</td>

                                    <td class="px-3 py-3 text-sm font-medium text-slate-700">{{ $letter['opens'] }}</td>

                                    <td class="px-3 py-3 text-sm font-medium text-slate-700">{{ $letter['clicks'] }}</td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge tone="green">Envoyée</x-admin.badge>
                                    </td>

                                    <td class="px-5 py-3">
                                        {{-- A sent newsletter cannot be re-sent, so the row
                                             offers preview, stats, duplicate, edit and delete. --}}
                                        <div class="flex items-center justify-end gap-1">
                                            @php $base = 'relative flex h-8 w-8 items-center justify-center rounded-md transition-colors'; @endphp

                                            <a href="#" class="{{ $base }} text-slate-500 hover:bg-slate-100">
                                                <x-icon name="eye" class="h-4 w-4" />
                                                <span class="sr-only">Aperçu : {{ $letter['subject'] }}</span>
                                            </a>

                                            <a href="#" class="{{ $base }} text-slate-500 hover:bg-slate-100">
                                                <x-icon name="chart-bar" class="h-4 w-4" />
                                                <span class="sr-only">Statistiques : {{ $letter['subject'] }}</span>
                                            </a>

                                            <button type="button" class="{{ $base }} text-slate-500 hover:bg-slate-100">
                                                <x-icon name="copy" class="h-4 w-4" />
                                                <span class="sr-only">Dupliquer : {{ $letter['subject'] }}</span>
                                            </button>

                                            <a href="#" class="{{ $base }} text-primary-600 hover:bg-primary-50">
                                                <x-icon name="pencil" class="h-4 w-4" />
                                                <span class="sr-only">Modifier : {{ $letter['subject'] }}</span>
                                            </a>

                                            <form method="POST" action="#" class="contents">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="{{ $base }} text-rose-600 hover:bg-rose-50">
                                                    <x-icon name="trash" class="h-4 w-4" />
                                                    <span class="sr-only">Supprimer : {{ $letter['subject'] }}</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-admin.table-footer
                    :from="1" :to="8" :total="12" unit="newsletters"
                    :current="1" :pages="2"
                />
            </section>
        </div>

        {{-- ============ COMPOSE ============ --}}
        {{--
            Presentation only. Sending is the one action here that cannot be
            undone, so Module 2 must add: a confirmation step before send,
            a real unsubscribe link in every message (required by the
            retention rules), queued delivery rather than a blocking request,
            and server-side validation of every field below.
        --}}
        <aside>
            <section class="rounded-xl border border-slate-200 bg-white p-5">

                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="paper-plane" class="h-5 w-5 text-primary-600" />
                    Créer / Envoyer une newsletter
                </h2>

                <x-admin.filter-tabs
                    class="mt-4"
                    :tabs="[
                        ['label' => 'Contenu', 'active' => true],
                        ['label' => 'Destinataires'],
                        ['label' => 'Aperçu'],
                    ]"
                />

                <form method="POST" action="#" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="letter-subject" class="block text-sm font-medium text-slate-700">
                            Sujet de la newsletter
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <input
                            type="text"
                            id="letter-subject"
                            name="sujet"
                            required
                            placeholder="Ex. Nos dernières actualités - Juin 2025"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="letter-preheader" class="block text-sm font-medium text-slate-700">Aperçu du message (préheader)</label>
                        <input
                            type="text"
                            id="letter-preheader"
                            name="preheader"
                            placeholder="Texte court affiché dans la boîte de réception..."
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="letter-content" class="block text-sm font-medium text-slate-700">
                            Contenu
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
                                id="letter-content"
                                name="contenu"
                                rows="5"
                                required
                                placeholder="Rédigez le contenu de votre newsletter..."
                                class="w-full resize-y border-0 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-0"
                            ></textarea>
                        </div>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-slate-700">Image principale</span>

                        <label
                            for="letter-image"
                            class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
                        >
                            <x-icon name="image" class="h-7 w-7 text-primary-500" />
                            <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour importer une image</span>
                            <span class="mt-1 text-xs text-slate-400">(JPG, PNG - Max 2MB)</span>

                            <input
                                type="file"
                                id="letter-image"
                                name="image"
                                accept="image/jpeg,image/png"
                                class="sr-only"
                            >
                        </label>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-slate-700">Pièces jointes (optionnel)</span>

                        <label
                            for="letter-attachments"
                            class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
                        >
                            <x-icon name="document" class="h-7 w-7 text-primary-500" />
                            <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour ajouter un fichier</span>
                            <span class="mt-1 text-xs text-slate-400">(PDF, DOC, etc. - Max 10MB)</span>

                            <input
                                type="file"
                                id="letter-attachments"
                                name="pieces_jointes[]"
                                accept="application/pdf,.doc,.docx"
                                multiple
                                class="sr-only"
                            >
                        </label>
                    </div>

                    <div class="pt-1">
                        <x-admin.toggle name="envoyer_immediatement" label="Envoyer immédiatement" :checked="true" />
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button
                            type="submit"
                            name="brouillon"
                            value="1"
                            class="flex-1 rounded-lg border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-50"
                        >
                            Enregistrer en brouillon
                        </button>

                        <button
                            type="submit"
                            class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-primary-700"
                        >
                            <x-icon name="paper-plane" class="h-4 w-4" />
                            Envoyer la newsletter
                        </button>
                    </div>
                </form>
            </section>
        </aside>
    </div>

@endsection
