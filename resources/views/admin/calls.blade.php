@extends('layouts.admin')

@section('title', 'Appels à candidatures')
@section('search_placeholder', 'Rechercher un appel, un profil...')

@section('content')

    <x-admin.flash />

    <x-admin.page-header
        title="Appels à candidatures"
        lead="Stages, volontariat, consultances et partenariats proposés par la Clinique."
        action-label="Ajouter un appel"
        action-href="#ajouter-un-appel"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Appels à candidatures'],
        ]"
    />

    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-4">

        <div class="space-y-5 xl:col-span-3">

            {{-- ============ KPIs ============ --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card icon="user-plus"    color="green" :value="$counts['open']"    label="Appels ouverts" note="visibles sur le site" />
                <x-admin.stat-card icon="clock"        color="amber" :value="$counts['closed']"  label="Clôturés" note="date dépassée" />
                <x-admin.stat-card icon="document"     color="blue"  :value="$counts['drafts']"  label="Brouillons" />
                <x-admin.stat-card icon="trash"        color="red"   :value="$counts['trashed']" label="À la corbeille" />
            </div>

            {{-- ============ TABLE ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white">

                @php
                    $vue = $filters['vue'] ?? null;

                    $tabLink = fn (array $extra) => route('admin.calls', array_merge(
                        array_filter([
                            'q' => $filters['q'] ?? null,
                            'categorie' => $filters['categorie'] ?? null,
                        ]),
                        $extra
                    ));
                @endphp

                <div class="flex flex-col gap-4 p-5 lg:flex-row lg:items-center lg:justify-between">
                    <x-admin.filter-tabs
                        class="lg:border-b-0"
                        :tabs="[
                            ['label' => 'Tous', 'count' => $counts['all'], 'active' => ! $vue, 'href' => $tabLink([])],
                            ['label' => 'Ouverts', 'count' => $counts['open'], 'active' => $vue === 'ouverts', 'href' => $tabLink(['vue' => 'ouverts'])],
                            ['label' => 'Clôturés', 'count' => $counts['closed'], 'active' => $vue === 'clotures', 'href' => $tabLink(['vue' => 'clotures'])],
                        ]"
                    />

                    <form method="GET" action="{{ route('admin.calls') }}" class="flex shrink-0 flex-wrap items-center gap-3">
                        @if($vue)
                            <input type="hidden" name="vue" value="{{ $vue }}">
                        @endif

                        <div role="search" class="relative w-full min-w-[12rem] flex-1 sm:w-56">
                            <label for="calls-search" class="sr-only">Rechercher un appel</label>
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <x-icon name="search" class="h-4 w-4" />
                            </span>
                            <input
                                type="search"
                                id="calls-search"
                                name="q"
                                value="{{ $filters['q'] ?? '' }}"
                                placeholder="Rechercher un appel..."
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                        </div>

                        <div class="min-w-0 shrink-0">
                            <label for="calls-type" class="sr-only">Filtrer par type</label>
                            <select
                                id="calls-type"
                                name="categorie"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-3.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-auto"
                            >
                                <option value="">Tous les types</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) ($filters['categorie'] ?? 0) === $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50">
                            <x-icon name="filter" class="h-4 w-4" />
                            Filtrer
                        </button>

                        @if (array_filter($filters))
                            <a href="{{ route('admin.calls') }}" class="shrink-0 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-500 transition-colors hover:bg-slate-50">
                                Réinitialiser
                            </a>
                        @endif
                    </form>
                </div>

                <p class="flex items-center gap-1.5 px-5 pt-1 text-xs text-slate-500 lg:hidden">
                    <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0" />
                    Faites glisser le tableau pour voir toutes les colonnes
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[56rem] text-left">
                        <thead>
                            <tr class="whitespace-nowrap border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <th scope="col" class="w-12 px-5 py-3">#</th>
                                <th scope="col" class="w-20 px-2 py-3">Image</th>
                                <th scope="col" class="min-w-[16rem] px-3 py-3">Titre de l'appel</th>
                                <th scope="col" class="px-3 py-3">Type</th>
                                <th scope="col" class="px-3 py-3">Clôture</th>
                                <th scope="col" class="px-3 py-3">Statut</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($calls as $call)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3 text-sm text-slate-500">
                                        {{ $loop->iteration + ($calls->firstItem() - 1) }}
                                    </td>

                                    <td class="px-2 py-3">
                                        <img
                                            src="{{ $call->imageUrl($call->image, 'placeholders/join-call-stage') }}"
                                            alt=""
                                            width="96" height="72"
                                            loading="lazy"
                                            class="h-11 w-14 rounded-md object-cover"
                                        >
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="block max-w-[18rem] text-sm font-semibold leading-snug text-slate-900">
                                            {{ $call->title }}
                                        </span>
                                        @if($call->excerpt)
                                            <span class="mt-0.5 block max-w-[18rem] truncate text-xs text-slate-400">
                                                {{ $call->excerpt }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3">
                                        @if($call->category)
                                            <x-admin.badge :tone="$call->category->tone">{{ $call->category->name }}</x-admin.badge>
                                        @else
                                            <span class="text-sm text-slate-400">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3">
                                        @if($call->closes_on)
                                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap text-sm text-slate-600">
                                                <x-icon name="calendar" class="h-4 w-4 shrink-0 text-slate-400" />
                                                {{ $call->closes_on->translatedFormat('j M Y') }}
                                            </span>
                                        @else
                                            <span class="text-sm text-slate-400">Sans date</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <x-admin.badge :tone="$call->statusTone()">{{ $call->statusLabel() }}</x-admin.badge>
                                            @if($call->isPublic())
                                                @if($call->isOpen())
                                                    <x-admin.badge tone="green">Ouvert</x-admin.badge>
                                                @else
                                                    <x-admin.badge tone="slate">Clôturé</x-admin.badge>
                                                @endif
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-5 py-3">
                                        <x-admin.row-actions
                                            :label="$call->title"
                                            :duplicate="false"
                                            :view-href="$call->isOpen() ? route('join', ['type' => $call->category?->slug]) : null"
                                            :delete-action="route('admin.calls.destroy', $call)"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center">
                                        <p class="text-sm text-slate-500">Aucun appel ne correspond à cette recherche.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-admin.table-footer :paginator="$calls" unit="appels" />
            </section>
        </div>

        {{-- ============ ADD CALL ============ --}}
        <aside>
            <section id="ajouter-un-appel" class="rounded-xl border border-slate-200 bg-white p-5">

                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="user-plus" class="h-5 w-5 text-primary-600" />
                    Ajouter un appel
                </h2>

                <form method="POST" action="{{ route('admin.calls.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="call-title" class="block text-sm font-medium text-slate-700">
                            Titre de l'appel
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <input
                            type="text"
                            id="call-title"
                            name="title"
                            required
                            value="{{ old('title') }}"
                            placeholder="Ex. Stage pour étudiants en droit"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="call-type" class="block text-sm font-medium text-slate-700">Type d'appel</label>
                        <select
                            id="call-type"
                            name="category_id"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option value="">Sélectionner un type</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="call-closes" class="block text-sm font-medium text-slate-700">Date de clôture</label>
                        <input
                            type="date"
                            id="call-closes"
                            name="closes_on"
                            value="{{ old('closes_on') }}"
                            aria-describedby="call-closes-hint"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                        <p id="call-closes-hint" class="mt-1.5 text-xs text-slate-500">
                            Passée cette date, l'appel disparaît du site de lui-même. Laissez vide pour un appel sans échéance.
                        </p>
                    </div>

                    <div>
                        <label for="call-excerpt" class="block text-sm font-medium text-slate-700">Résumé</label>
                        <textarea
                            id="call-excerpt"
                            name="excerpt"
                            rows="3"
                            placeholder="Quelques lignes affichées dans la liste..."
                            class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >{{ old('excerpt') }}</textarea>
                    </div>

                    <div>
                        <label for="call-body" class="block text-sm font-medium text-slate-700">Détail de l'appel</label>
                        <textarea
                            id="call-body"
                            name="body"
                            rows="5"
                            placeholder="Conditions, pièces à fournir, modalités de candidature..."
                            class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >{{ old('body') }}</textarea>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-slate-700">Image</span>

                        <label
                            for="call-image"
                            class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-5 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
                        >
                            <x-icon name="image" class="h-6 w-6 text-primary-500" />
                            <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour importer une image</span>
                            <span class="mt-1 text-xs text-slate-400">(JPG, PNG - Max 2MB)</span>

                            <input
                                type="file"
                                id="call-image"
                                name="image"
                                accept="image/jpeg,image/png,image/webp"
                                class="sr-only"
                            >
                        </label>
                    </div>

                    <div>
                        <label for="call-status" class="block text-sm font-medium text-slate-700">
                            Statut
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <select
                            id="call-status"
                            name="status"
                            required
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            @php
                                $options = ['published' => 'Publié', 'draft' => 'Brouillon', 'archived' => 'Archivé'];
                            @endphp
                            @foreach ($options as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', 'published') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button
                            type="reset"
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
