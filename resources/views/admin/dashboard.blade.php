@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')

    {{-- ============ WELCOME ============ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold text-navy-900 sm:text-3xl">
                Bienvenue, {{ auth()->user()?->name ?? 'Administrateur' }}
                <span aria-hidden="true">&#128075;</span>
            </h1>
            <p class="mt-1.5 text-sm text-slate-600">
                Voici un aperçu des activités de la Clinique Juridique FSJP-UAM.
            </p>
        </div>

        <div class="flex shrink-0 items-center gap-3 rounded-xl border border-slate-200 bg-white px-5 py-3">
            <x-icon-badge size="sm"><x-icon name="calendar" class="h-5 w-5" /></x-icon-badge>
            <div class="leading-tight">
                <p class="text-sm font-semibold text-slate-900">{{ ucfirst(now()->translatedFormat('l j F Y')) }}</p>
                <p class="text-xs text-slate-500">{{ now()->format('H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- ============ KPIs ============ --}}
    {{-- Figures are illustrative until Module 2 supplies real counts. --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card icon="document"       color="blue"   value="24" label="Actualités"            trend="+12%" note="par rapport au mois dernier" />
        <x-admin.stat-card icon="play-circle"    color="green"  value="18" label="Capsules Web TV"       trend="+28%" note="par rapport au mois dernier" />
        <x-admin.stat-card icon="users"          color="amber"  value="42" label="Candidatures reçues"   trend="+15%" note="ce mois-ci" />
        <x-admin.stat-card icon="calendar"       color="purple" value="8"  label="Activités organisées"  trend="+33%" note="par rapport au mois dernier" />
    </div>

    {{-- ============ CHARTS ============ --}}
    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-3">

        <section class="min-w-0 rounded-xl border border-slate-200 bg-white p-5 xl:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="chart-bar" class="h-5 w-5 text-primary-600" />
                    Statistiques des publications
                </h2>

                <div>
                    <label for="stats-range" class="sr-only">Période affichée</label>
                    <select
                        id="stats-range"
                        class="rounded-lg border border-slate-200 py-2 pl-3 pr-8 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    >
                        <option>Derniers 30 jours</option>
                        <option>Derniers 7 jours</option>
                        <option>Cette année</option>
                    </select>
                </div>
            </div>

            <div class="mt-5">
                <x-admin.line-chart
                    :labels="['10 Mai', '15 Mai', '20 Mai', '25 Mai', '30 Mai', '5 Juin', '10 Juin']"
                    :series="[
                        ['label' => 'Actualités',   'color' => '#2563eb', 'points' => [5, 9, 17, 14, 22, 31, 29]],
                        ['label' => 'Web TV',       'color' => '#059669', 'points' => [4, 5, 7, 11, 9, 18, 25]],
                        ['label' => 'Candidatures', 'color' => '#f59e0b', 'points' => [2, 8, 12, 15, 10, 19, 16]],
                    ]"
                />
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                <x-icon name="chart-pie" class="h-5 w-5 text-primary-600" />
                Répartition du contenu
            </h2>

            <div class="mt-5">
                <x-admin.donut-chart
                    :total="92"
                    total-label="Éléments"
                    :items="[
                        ['label' => 'Actualités',           'value' => 24, 'color' => '#2563eb'],
                        ['label' => 'Web TV',               'value' => 18, 'color' => '#059669'],
                        ['label' => 'Fiches pratiques',     'value' => 12, 'color' => '#f59e0b'],
                        ['label' => 'Activités',            'value' => 8,  'color' => '#fb923c'],
                        ['label' => 'Appels à candidatures','value' => 15, 'color' => '#ec4899'],
                        ['label' => 'Galerie photos',       'value' => 10, 'color' => '#60a5fa'],
                        ['label' => 'Autres',               'value' => 5,  'color' => '#94a3b8'],
                    ]"
                />
            </div>
        </section>
    </div>

    {{-- ============ RECENT LISTS ============ --}}
    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-3">

        <section class="min-w-0 rounded-xl border border-slate-200 bg-white xl:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-5">
                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="document" class="h-5 w-5 text-primary-600" />
                    Dernières actualités
                </h2>

                <a href="{{ route('admin.news') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700">
                    Voir toutes
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            @php
                $latest = [
                    ['title' => 'Lancement de la caravane juridique à Zinder', 'category' => 'Activités',            'color' => 'bg-primary-50 text-primary-700', 'date' => '10 juin 2025'],
                    ['title' => 'Formation sur les droits des femmes',          'category' => 'Actualité',            'color' => 'bg-rose-50 text-rose-700',       'date' => '8 juin 2025'],
                    ['title' => 'Nouveau guide pratique disponible',            'category' => 'Fiches pratiques',     'color' => 'bg-emerald-50 text-emerald-700', 'date' => '5 juin 2025'],
                    ['title' => 'Appel à candidatures – Stagiaires 2025',       'category' => 'Appels à candidatures','color' => 'bg-violet-50 text-violet-700',   'date' => '1 juin 2025'],
                    ['title' => 'Retour sur la clinique mobile à Maradi',       'category' => 'Activités',            'color' => 'bg-primary-50 text-primary-700', 'date' => '28 mai 2025'],
                ];
            @endphp

            <p class="flex items-center gap-1.5 px-5 pt-1 text-xs text-slate-500 lg:hidden">
                <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0" />
                Faites glisser le tableau pour voir toutes les colonnes
            </p>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[42rem] text-left">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th scope="col" class="px-5 py-3">#</th>
                            <th scope="col" class="px-5 py-3">Titre</th>
                            <th scope="col" class="px-5 py-3">Catégorie</th>
                            <th scope="col" class="px-5 py-3">Statut</th>
                            <th scope="col" class="px-5 py-3">Date</th>
                            <th scope="col" class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($latest as $i => $row)
                            <tr class="align-middle hover:bg-slate-50">
                                <td class="px-5 py-3.5 align-middle text-sm text-slate-500">{{ $i + 1 }}</td>
                                <td class="px-5 py-3.5 align-middle text-sm font-medium text-slate-900">{{ $row['title'] }}</td>
                                <td class="px-5 py-3.5 align-middle">
                                    <span class="inline-block rounded px-2 py-0.5 text-xs font-medium {{ $row['color'] }}">{{ $row['category'] }}</span>
                                </td>
                                <td class="px-5 py-3.5 align-middle">
                                    <span class="inline-block rounded bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Publié</span>
                                </td>
                                <td class="px-5 py-3.5 align-middle text-sm text-slate-500">{{ $row['date'] }}</td>
                                <td class="px-5 py-3.5 align-middle">
                                    <x-admin.row-actions :label="$row['title']" :duplicate="false" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-5">
                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="users" class="h-5 w-5 text-primary-600" />
                    Dernières candidatures
                </h2>

                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700">
                    Voir toutes
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            @php
                $applications = [
                    ['initials' => 'AM', 'name' => 'Aicha Moussa',     'role' => 'Stagiaire – Droit public',       'date' => '9 juin 2025', 'status' => 'Nouveau',  'tone' => 'bg-primary-50 text-primary-700'],
                    ['initials' => 'IB', 'name' => 'Ibrahim Boureima', 'role' => 'Volontaire – Communication',     'date' => '7 juin 2025', 'status' => 'En cours', 'tone' => 'bg-amber-50 text-amber-700'],
                    ['initials' => 'FA', 'name' => 'Fatoumata Ali',    'role' => 'Stagiaire – Droit privé',        'date' => '6 juin 2025', 'status' => 'En cours', 'tone' => 'bg-amber-50 text-amber-700'],
                    ['initials' => 'MO', 'name' => 'Moussa Oumarou',   'role' => 'Bénévole – Assistance juridique','date' => '4 juin 2025', 'status' => 'Traité',   'tone' => 'bg-emerald-50 text-emerald-700'],
                    ['initials' => 'SD', 'name' => 'Salamatou Danladi','role' => 'Stagiaire – Recherche',          'date' => '2 juin 2025', 'status' => 'Traité',   'tone' => 'bg-emerald-50 text-emerald-700'],
                ];
            @endphp

            <ul class="divide-y divide-slate-100">
                @foreach ($applications as $application)
                    <li class="flex items-center gap-3 p-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                            {{ $application['initials'] }}
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $application['name'] }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $application['role'] }}</p>
                        </div>

                        <div class="shrink-0 text-right">
                            <p class="text-xs text-slate-500">{{ $application['date'] }}</p>
                            <span class="mt-1 inline-block rounded px-2 py-0.5 text-xs font-medium {{ $application['tone'] }}">
                                {{ $application['status'] }}
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

@endsection
