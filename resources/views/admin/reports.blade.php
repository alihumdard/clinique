@extends('layouts.admin')

@section('title', "Gestion des rapports annuels d'activité")
@section('search_placeholder', 'Rechercher un rapport, une année...')

@section('content')

    <x-admin.flash />

    <x-admin.page-header
        title="Gestion des rapports annuels d'activité"
        lead="Ajoutez, modifiez et organisez les rapports annuels d'activité de la Clinique Juridique FSJP-UAM."
        action-label="Ajouter un rapport annuel"
        action-dialog="create-report"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Rapports annuels'],
        ]"
    />

    <div class="mt-6 space-y-5">

        <div class="space-y-5">

            {{-- ============ KPIs ============ --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card icon="document" color="blue"  :value="$counts['published']" label="Rapports publiés" />
                <x-admin.stat-card icon="download" color="green" :value="number_format($counts['downloads'], 0, ',', ' ')" label="Téléchargements" />
                <x-admin.stat-card icon="clock"    color="amber" :value="$counts['drafts']"    label="En brouillon" note="non publiés" />
                <x-admin.stat-card icon="trash"    color="red"   :value="$counts['trashed']"   label="À la corbeille" />
            </div>

            {{-- ============ TABLE ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white">

                {{-- One GET form: search, année and statut submit together,
                     so a filtered list is a URL that can be shared. --}}
                <form method="GET" action="{{ route('admin.reports') }}" class="space-y-3 p-5">
                    <div role="search" class="relative w-full lg:max-w-xs">
                        <label for="reports-search" class="sr-only">Rechercher un rapport</label>
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input
                            type="search"
                            id="reports-search"
                            name="q"
                            value="{{ $filters['q'] ?? '' }}"
                            placeholder="Rechercher un rapport..."
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
                            <label for="reports-year" class="sr-only">Filtrer par année</label>
                            <select
                                id="reports-year"
                                name="annee"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-3.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-auto"
                            >
                                <option value="">Toutes les années</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" @selected((int) ($filters['annee'] ?? 0) === (int) $year)>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="min-w-0 sm:shrink-0">
                            <label for="reports-status" class="sr-only">Filtrer par statut</label>
                            <select
                                id="reports-status"
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
                            <a href="{{ route('admin.reports') }}" class="col-span-2 inline-flex w-full shrink-0 items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-500 transition-colors hover:bg-slate-50 sm:col-span-1 sm:w-auto">
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
                    <table class="w-full min-w-[62rem] text-left">
                        <thead>
                            <tr class="whitespace-nowrap border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
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
                            @forelse ($reports as $report)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <input
                                            type="checkbox"
                                            aria-label="Sélectionner : {{ $report->title }}"
                                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                    </td>

                                    <td class="px-2 py-3 text-sm text-slate-500">
                                        {{ $loop->iteration + ($reports->firstItem() - 1) }}
                                    </td>

                                    <td class="px-2 py-3">
                                        <img
                                            src="{{ $report->coverUrl() }}"
                                            alt=""
                                            width="80" height="100"
                                            loading="lazy"
                                            class="h-12 w-10 rounded border border-slate-200 object-cover"
                                        >
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="block max-w-[12rem] text-sm font-semibold leading-snug text-slate-900">
                                            {{ $report->title }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3 text-sm font-medium text-slate-700">{{ $report->year }}</td>

                                    <td class="whitespace-nowrap px-3 py-3">
                                        <span class="flex items-start gap-1.5">
                                            <x-icon name="document" class="mt-0.5 h-4 w-4 shrink-0 text-rose-500" />
                                            <span class="leading-tight">
                                                <span class="block text-sm text-slate-700">PDF</span>
                                                <span class="block text-xs text-slate-400">{{ $report->fileSizeForHumans() }}</span>
                                            </span>
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="inline-flex items-center gap-1.5 text-sm text-slate-600">
                                            <x-icon name="download" class="h-4 w-4 shrink-0 text-slate-400" />
                                            {{ number_format($report->downloads, 0, ',', ' ') }}
                                            <span class="sr-only">téléchargements</span>
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-3 text-sm leading-tight text-slate-500">
                                        @if($report->published_at)
                                            {{ $report->published_at->translatedFormat('j F Y') }}<br>
                                            <span class="text-xs">{{ $report->published_at->format('H:i') }}</span>
                                        @else
                                            <span class="text-slate-400">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$report->statusTone()">{{ $report->statusLabel() }}</x-admin.badge>
                                    </td>

                                    <td class="px-5 py-3">
                                        <x-admin.row-actions
                                            :label="$report->title"
                                            edit-dialog="edit-report-{{ $report->id }}"
                                            :download-href="$report->file_path ? route('admin.reports.download', $report) : null"
                                            :duplicate-action="route('admin.reports.duplicate', $report)"
                                            :delete-action="route('admin.reports.destroy', $report)"
                                        />
                                    </td>
                                </tr>

                                <x-admin.edit-modal id="edit-report-{{ $report->id }}" title="Modifier le rapport" :action="route('admin.reports.update', $report)">
                                    <div>
                                        <label for="edit-report-{{ $report->id }}-title" class="block text-sm font-medium text-slate-700">
                                            Titre
                                            <span class="text-rose-600" aria-hidden="true">*</span>
                                        </label>
                                        <input type="text" id="edit-report-{{ $report->id }}-title" name="title" required value="{{ $report->title }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                    </div>

                                    <div>
                                        <label for="edit-report-{{ $report->id }}-year" class="block text-sm font-medium text-slate-700">
                                            Année
                                            <span class="text-rose-600" aria-hidden="true">*</span>
                                        </label>
                                        <select id="edit-report-{{ $report->id }}-year" name="year" required class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                            @for ($year = (int) date('Y') + 1; $year >= 2019; $year--)
                                                <option value="{{ $year }}" @selected($report->year === $year)>{{ $year }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div>
                                        <label for="edit-report-{{ $report->id }}-description" class="block text-sm font-medium text-slate-700">Description</label>
                                        <textarea id="edit-report-{{ $report->id }}-description" name="description" rows="4" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">{{ $report->description }}</textarea>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label for="edit-report-{{ $report->id }}-status" class="block text-sm font-medium text-slate-700">Statut</label>
                                            <select id="edit-report-{{ $report->id }}-status" name="status" required class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                                <option value="draft" @selected($report->status === 'draft')>Brouillon</option>
                                                <option value="published" @selected($report->status === 'published')>Publié</option>
                                                <option value="archived" @selected($report->status === 'archived')>Archivé</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="edit-report-{{ $report->id }}-published-at" class="block text-sm font-medium text-slate-700">Date de publication</label>
                                            <input type="date" id="edit-report-{{ $report->id }}-published-at" name="published_at" value="{{ $report->published_at?->format('Y-m-d') }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                        </div>
                                    </div>

                                    <div>
                                        <span class="block text-sm font-medium text-slate-700">Image de couverture</span>
                                        <div class="mt-1.5 flex items-center gap-3">
                                            <img src="{{ $report->coverUrl() }}" alt="" class="h-14 w-11 shrink-0 rounded-md border border-slate-200 object-cover">
                                            <input type="file" name="cover" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100">
                                        </div>
                                    </div>

                                    <div>
                                        <span class="block text-sm font-medium text-slate-700">Fichier PDF</span>
                                        <div class="mt-1.5 flex items-center gap-1.5 text-xs text-slate-500">
                                            <x-icon name="document" class="h-4 w-4 shrink-0 text-rose-500" />
                                            {{ $report->file_name }} ({{ $report->fileSizeForHumans() }})
                                        </div>
                                        <input type="file" name="document" accept="application/pdf" class="mt-1.5 block w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100">
                                        <p class="mt-1 text-xs text-slate-400">Laissez vide pour conserver le fichier actuel.</p>
                                    </div>
                                </x-admin.edit-modal>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-5 py-12 text-center">
                                        <p class="text-sm text-slate-500">Aucun rapport ne correspond à cette recherche.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-admin.table-footer :paginator="$reports" unit="rapports" />
            </section>
        </div>

    </div>

    {{--
        "Ajouter un rapport annuel" in the page header opens this dialog
        rather than scrolling to a sidebar form, matching the Actualités
        pattern.
    --}}
    <x-admin.edit-modal id="create-report" title="Ajouter un rapport annuel" :action="route('admin.reports.store')" method="POST" submit-label="Publier le rapport">
        <div>
            <label for="report-title" class="block text-sm font-medium text-slate-700">
                Titre du rapport
                <span class="text-rose-600" aria-hidden="true">*</span>
                <span class="sr-only">(obligatoire)</span>
            </label>
            <input
                type="text"
                id="report-title"
                name="title"
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
                name="year"
                required
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
                <option value="">Sélectionner l'année</option>
                {{-- The Clinic opened in 2019; the validator holds
                     the same bounds. --}}
                @for ($year = (int) date('Y') + 1; $year >= 2019; $year--)
                    <option value="{{ $year }}" @selected($year === (int) date('Y'))>{{ $year }}</option>
                @endfor
            </select>
        </div>

        <div>
            <label for="report-description" class="block text-sm font-medium text-slate-700">Description</label>
            <textarea
                id="report-description"
                name="description"
                rows="4"
                placeholder="Décrivez brièvement le contenu du rapport..."
                class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            ></textarea>
        </div>

        <div>
            <label for="create-report-published-at" class="block text-sm font-medium text-slate-700">Date de publication</label>
            <input type="date" id="create-report-published-at" name="published_at" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
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
                    name="document"
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
                    name="cover"
                    accept="image/jpeg,image/png,image/webp"
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
    </x-admin.edit-modal>

@endsection
