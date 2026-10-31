@extends('layouts.admin')

@section('title', 'Référentiels')

@section('content')

    <x-admin.flash />

    <x-admin.page-header
        title="Référentiels"
        lead="La grille tarifaire et les domaines du droit, utilisés sur plusieurs pages du site."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Référentiels'],
        ]"
    />

    {{--
        Each row is edited in place and saved on its own, so one mistake
        cannot take the whole list with it.

        The <form> elements sit outside the tables and the controls point at
        them with the `form` attribute. A <form> is not permitted as a child
        of <tr>: the parser would hoist it out of the table and the fields
        would submit nothing. `display: contents` does not change that —
        it is a layout rule, and this is a parsing one.
    --}}
    @foreach ($fees as $fee)
        <form method="POST" action="{{ route('admin.references.fees.update', $fee) }}" id="fee-{{ $fee->id }}" class="hidden">
            @csrf
            @method('PUT')
        </form>
        <form method="POST" action="{{ route('admin.references.fees.destroy', $fee) }}" id="fee-{{ $fee->id }}-delete" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    @foreach ($domains as $domain)
        <form method="POST" action="{{ route('admin.references.domains.update', $domain) }}" id="domain-{{ $domain->id }}" class="hidden">
            @csrf
            @method('PUT')
        </form>
        <form method="POST" action="{{ route('admin.references.domains.destroy', $domain) }}" id="domain-{{ $domain->id }}-delete" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    <div class="mt-6 space-y-6">

        {{-- ============ GRILLE TARIFAIRE ============ --}}
        <section class="rounded-xl border border-slate-200 bg-white">
            <div class="flex items-start gap-3 p-5">
                <x-icon-badge size="sm" class="shrink-0"><x-icon name="wallet" class="h-5 w-5" /></x-icon-badge>
                <div class="min-w-0">
                    <h2 class="text-base font-bold text-slate-900">Grille tarifaire</h2>
                    <p class="mt-0.5 text-xs text-slate-500">
                        Affichée sur la page « Tarifs administratifs ». Les consultations restent gratuites.
                    </p>
                </div>
            </div>

            <p class="flex items-center gap-1.5 px-5 pb-2 text-xs text-slate-500 lg:hidden">
                <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0" />
                Faites glisser le tableau pour voir toutes les colonnes
            </p>

            <div class="overflow-x-auto border-t border-slate-100">
                <table class="w-full min-w-[60rem] text-left">
                    <thead>
                        <tr class="whitespace-nowrap border-b border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th scope="col" class="w-20 px-5 py-3">Ordre</th>
                            <th scope="col" class="min-w-[11rem] px-3 py-3">Nature de la prestation</th>
                            <th scope="col" class="min-w-[11rem] px-3 py-3">Description</th>
                            <th scope="col" class="w-44 px-3 py-3">Montant</th>
                            <th scope="col" class="w-32 px-3 py-3">Options</th>
                            <th scope="col" class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($fees as $fee)
                            @php $f = 'fee-'.$fee->id; @endphp

                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3">
                                    <label for="{{ $f }}-position" class="sr-only">Ordre de « {{ $fee->label }} »</label>
                                    <input
                                        type="number"
                                        form="{{ $f }}"
                                        id="{{ $f }}-position"
                                        name="position"
                                        min="0"
                                        value="{{ $fee->position }}"
                                        class="w-16 rounded-md border border-slate-200 px-2 py-1.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                    >
                                </td>

                                <td class="px-3 py-3">
                                    <label for="{{ $f }}-label" class="sr-only">Nature de la prestation</label>
                                    <input
                                        type="text"
                                        form="{{ $f }}"
                                        id="{{ $f }}-label"
                                        name="label"
                                        required
                                        value="{{ $fee->label }}"
                                        class="w-full rounded-md border border-slate-200 px-3 py-1.5 text-sm font-medium focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                    >
                                </td>

                                <td class="px-3 py-3">
                                    <label for="{{ $f }}-note" class="sr-only">Description</label>
                                    <input
                                        type="text"
                                        form="{{ $f }}"
                                        id="{{ $f }}-note"
                                        name="note"
                                        value="{{ $fee->note }}"
                                        class="w-full rounded-md border border-slate-200 px-3 py-1.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                    >
                                </td>

                                <td class="px-3 py-3">
                                    <div class="space-y-1.5">
                                        <label for="{{ $f }}-amount" class="sr-only">Montant en chiffres</label>
                                        <input
                                            type="number"
                                            form="{{ $f }}"
                                            id="{{ $f }}-amount"
                                            name="amount"
                                            min="0"
                                            value="{{ $fee->amount }}"
                                            placeholder="Montant (FCFA)"
                                            class="w-full rounded-md border border-slate-200 px-3 py-1.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                        >

                                        <label for="{{ $f }}-amount-label" class="sr-only">Montant en texte</label>
                                        <input
                                            type="text"
                                            form="{{ $f }}"
                                            id="{{ $f }}-amount-label"
                                            name="amount_label"
                                            value="{{ $fee->amount_label }}"
                                            placeholder="ou « Selon tarif en vigueur »"
                                            class="w-full rounded-md border border-slate-200 px-3 py-1.5 text-xs focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                        >
                                    </div>
                                </td>

                                <td class="px-3 py-3">
                                    <div class="space-y-1.5">
                                        <label class="flex items-center gap-1.5 text-xs text-slate-600">
                                            <input type="checkbox" form="{{ $f }}" name="is_free" value="1" @checked($fee->is_free) class="h-3.5 w-3.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                                            Gratuit
                                        </label>

                                        <label class="flex items-center gap-1.5 text-xs text-slate-600">
                                            <input type="checkbox" form="{{ $f }}" name="is_visible" value="1" @checked($fee->is_visible) class="h-3.5 w-3.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                                            Visible
                                        </label>
                                    </div>
                                </td>

                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="submit" form="{{ $f }}" class="relative flex h-8 w-8 items-center justify-center rounded-md text-primary-600 transition-colors hover:bg-primary-50">
                                            <x-icon name="save" class="h-4 w-4" />
                                            <span class="sr-only">Enregistrer : {{ $fee->label }}</span>
                                        </button>

                                        <button type="submit" form="{{ $f }}-delete" class="relative flex h-8 w-8 items-center justify-center rounded-md text-rose-600 transition-colors hover:bg-rose-50">
                                            <x-icon name="trash" class="h-4 w-4" />
                                            <span class="sr-only">Supprimer : {{ $fee->label }}</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center">
                                    <p class="text-sm text-slate-500">Aucun tarif enregistré.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Add --}}
            <form method="POST" action="{{ route('admin.references.fees.store') }}" class="border-t border-slate-100 p-5">
                @csrf

                <h3 class="text-sm font-semibold text-slate-900">Ajouter un tarif</h3>

                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="new-fee-label" class="block text-xs font-medium text-slate-600">
                            Nature de la prestation
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <input type="text" id="new-fee-label" name="label" required value="{{ old('label') }}" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>

                    <div>
                        <label for="new-fee-note" class="block text-xs font-medium text-slate-600">Description</label>
                        <input type="text" id="new-fee-note" name="note" value="{{ old('note') }}" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>

                    <div>
                        <label for="new-fee-amount" class="block text-xs font-medium text-slate-600">Montant (FCFA)</label>
                        <input type="number" id="new-fee-amount" name="amount" min="0" value="{{ old('amount') }}" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>

                    <div>
                        <label for="new-fee-amount-label" class="block text-xs font-medium text-slate-600">ou montant en texte</label>
                        <input type="text" id="new-fee-amount-label" name="amount_label" value="{{ old('amount_label') }}" placeholder="Selon tarif en vigueur" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="is_free" value="1" @checked(old('is_free')) class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        Gratuit
                    </label>

                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="is_visible" value="1" @checked(old('is_visible', true)) class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        Visible sur le site
                    </label>

                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary-700">
                        <x-icon name="plus" class="h-4 w-4" />
                        Ajouter
                    </button>
                </div>
            </form>
        </section>

        {{-- ============ DOMAINES DU DROIT ============ --}}
        <section class="rounded-xl border border-slate-200 bg-white">
            <div class="flex items-start gap-3 p-5">
                <x-icon-badge size="sm" class="shrink-0"><x-icon name="scale" class="h-5 w-5" /></x-icon-badge>
                <div class="min-w-0">
                    <h2 class="text-base font-bold text-slate-900">Domaines du droit</h2>
                    <p class="mt-0.5 text-xs leading-relaxed text-slate-500">
                        Affichés sur l'accueil et la page Services. Cette liste alimentera aussi le
                        formulaire de pré-saisine et le registre des dossiers, d'où l'intérêt de la
                        tenir à jour ici plutôt qu'en plusieurs endroits.
                    </p>
                </div>
            </div>

            <p class="flex items-center gap-1.5 px-5 pb-2 text-xs text-slate-500 lg:hidden">
                <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0" />
                Faites glisser le tableau pour voir toutes les colonnes
            </p>

            <div class="overflow-x-auto border-t border-slate-100">
                <table class="w-full min-w-[60rem] text-left">
                    <thead>
                        <tr class="whitespace-nowrap border-b border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th scope="col" class="w-20 px-5 py-3">Ordre</th>
                            <th scope="col" class="min-w-[12rem] px-3 py-3">Nom du domaine</th>
                            <th scope="col" class="min-w-[11rem] px-3 py-3">Formulation simple</th>
                            <th scope="col" class="w-36 px-3 py-3">Icône</th>
                            <th scope="col" class="w-36 px-3 py-3">Couleur</th>
                            <th scope="col" class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($domains as $domain)
                            @php $d = 'domain-'.$domain->id; @endphp

                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3">
                                    <label for="{{ $d }}-position" class="sr-only">Ordre de « {{ $domain->name }} »</label>
                                    <input
                                        type="number"
                                        form="{{ $d }}"
                                        id="{{ $d }}-position"
                                        name="position"
                                        min="0"
                                        value="{{ $domain->position }}"
                                        class="w-16 rounded-md border border-slate-200 px-2 py-1.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                    >
                                </td>

                                <td class="px-3 py-3">
                                    <label for="{{ $d }}-name" class="sr-only">Nom du domaine</label>
                                    <input
                                        type="text"
                                        form="{{ $d }}"
                                        id="{{ $d }}-name"
                                        name="name"
                                        required
                                        value="{{ $domain->name }}"
                                        class="w-full rounded-md border border-slate-200 px-3 py-1.5 text-sm font-medium focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                    >
                                    <span class="mt-1 block text-xs text-slate-400">{{ $domain->slug }}</span>
                                </td>

                                <td class="px-3 py-3">
                                    <label for="{{ $d }}-plain" class="sr-only">Formulation simple</label>
                                    <input
                                        type="text"
                                        form="{{ $d }}"
                                        id="{{ $d }}-plain"
                                        name="plain_language"
                                        value="{{ $domain->plain_language }}"
                                        placeholder="Ex. Mariage, divorce, enfants"
                                        class="w-full rounded-md border border-slate-200 px-3 py-1.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                    >
                                </td>

                                <td class="px-3 py-3">
                                    <label for="{{ $d }}-icon" class="sr-only">Icône</label>
                                    <select form="{{ $d }}" id="{{ $d }}-icon" name="icon" class="w-full rounded-md border border-slate-200 px-2 py-1.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                        @foreach ($icons as $value => $label)
                                            <option value="{{ $value }}" @selected($domain->icon === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="px-3 py-3">
                                    <label for="{{ $d }}-tone" class="sr-only">Couleur</label>
                                    <select form="{{ $d }}" id="{{ $d }}-tone" name="tone" class="w-full rounded-md border border-slate-200 px-2 py-1.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                        @foreach ($tones as $value => $label)
                                            <option value="{{ $value }}" @selected($domain->tone === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>

                                    <label class="mt-1.5 flex items-center gap-1.5 text-xs text-slate-600">
                                        <input type="checkbox" form="{{ $d }}" name="is_active" value="1" @checked($domain->is_active) class="h-3.5 w-3.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                                        Actif
                                    </label>
                                </td>

                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="submit" form="{{ $d }}" class="relative flex h-8 w-8 items-center justify-center rounded-md text-primary-600 transition-colors hover:bg-primary-50">
                                            <x-icon name="save" class="h-4 w-4" />
                                            <span class="sr-only">Enregistrer : {{ $domain->name }}</span>
                                        </button>

                                        <button type="submit" form="{{ $d }}-delete" class="relative flex h-8 w-8 items-center justify-center rounded-md text-rose-600 transition-colors hover:bg-rose-50">
                                            <x-icon name="trash" class="h-4 w-4" />
                                            <span class="sr-only">Supprimer : {{ $domain->name }}</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center">
                                    <p class="text-sm text-slate-500">Aucun domaine enregistré.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <form method="POST" action="{{ route('admin.references.domains.store') }}" class="border-t border-slate-100 p-5">
                @csrf

                <h3 class="text-sm font-semibold text-slate-900">Ajouter un domaine</h3>

                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="new-domain-name" class="block text-xs font-medium text-slate-600">
                            Nom du domaine
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <input type="text" id="new-domain-name" name="name" required value="{{ old('name') }}" placeholder="Ex. Droit de la famille" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>

                    <div>
                        <label for="new-domain-plain" class="block text-xs font-medium text-slate-600">Formulation simple</label>
                        <input type="text" id="new-domain-plain" name="plain_language" value="{{ old('plain_language') }}" placeholder="Mariage, divorce, enfants" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>

                    <div>
                        <label for="new-domain-icon" class="block text-xs font-medium text-slate-600">Icône</label>
                        <select id="new-domain-icon" name="icon" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                            @foreach ($icons as $value => $label)
                                <option value="{{ $value }}" @selected(old('icon') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="new-domain-tone" class="block text-xs font-medium text-slate-600">Couleur</label>
                        <select id="new-domain-tone" name="tone" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                            @foreach ($tones as $value => $label)
                                <option value="{{ $value }}" @selected(old('tone') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        Actif
                    </label>

                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary-700">
                        <x-icon name="plus" class="h-4 w-4" />
                        Ajouter
                    </button>
                </div>
            </form>
        </section>
    </div>

@endsection
