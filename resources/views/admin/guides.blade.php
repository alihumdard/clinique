@extends('layouts.admin')

@section('title', 'Gestion des fiches pratiques')
@section('search_placeholder', 'Rechercher une fiche pratique, un sujet, un mot-clé...')

@section('content')

    <x-admin.flash />

    <x-admin.page-header
        title="Gestion des fiches pratiques"
        lead="Ajoutez, modifiez et organisez les guides pratiques juridiques (PDF)."
        action-label="Ajouter un guide pratique"
        action-href="#ajouter-une-fiche"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Fiches pratiques'],
        ]"
    />

    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-4">

        <div class="space-y-5 xl:col-span-3">

            {{-- ============ KPIs ============ --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card icon="document" color="blue"  :value="$counts['published']" label="Fiches publiées" />
                <x-admin.stat-card icon="download" color="green" :value="number_format($counts['downloads'], 0, ',', ' ')" label="Téléchargements" />
                <x-admin.stat-card icon="folder"   color="amber" :value="$categories->count()" label="Domaines" note="organisés" />
                <x-admin.stat-card icon="clock"    color="red"   :value="$counts['drafts']"    label="En brouillon" note="non publiées" />
            </div>

            {{-- ============ TABLE ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white">

                {{-- One GET form: search, domaine and statut submit together,
                     so a filtered list is a URL that can be shared. --}}
                <form method="GET" action="{{ route('admin.guides') }}" class="space-y-3 p-5">
                    <div role="search" class="relative w-full lg:max-w-xs">
                        <label for="guides-search" class="sr-only">Rechercher une fiche pratique</label>
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input
                            type="search"
                            id="guides-search"
                            name="q"
                            value="{{ $filters['q'] ?? '' }}"
                            placeholder="Rechercher une fiche..."
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    @php
                        $statuses = [
                            '' => 'Tous les statuts',
                            'published' => 'Publiées',
                            'draft' => 'Brouillons',
                            'archived' => 'Archivées',
                            'corbeille' => 'Corbeille',
                        ];
                    @endphp

                    <div class="grid grid-cols-2 gap-3 sm:flex sm:flex-wrap sm:items-center">
                        <div class="min-w-0 sm:shrink-0">
                            <label for="guides-domain" class="sr-only">Filtrer par domaine</label>
                            <select
                                id="guides-domain"
                                name="categorie"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-3.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-auto"
                            >
                                <option value="">Tous les domaines</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) ($filters['categorie'] ?? 0) === $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="min-w-0 sm:shrink-0">
                            <label for="guides-status" class="sr-only">Filtrer par statut</label>
                            <select
                                id="guides-status"
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
                            <a href="{{ route('admin.guides') }}" class="col-span-2 inline-flex w-full shrink-0 items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-500 transition-colors hover:bg-slate-50 sm:col-span-1 sm:w-auto">
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
                    <table class="w-full min-w-[64rem] text-left">
                        <thead>
                            <tr class="whitespace-nowrap border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <th scope="col" class="w-12 px-5 py-3">
                                    <input
                                        type="checkbox"
                                        aria-label="Sélectionner toutes les fiches"
                                        class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                    >
                                </th>
                                <th scope="col" class="w-12 px-2 py-3">#</th>
                                <th scope="col" class="w-20 px-2 py-3">Aperçu</th>
                                <th scope="col" class="min-w-[14rem] px-3 py-3">Titre de la fiche pratique</th>
                                <th scope="col" class="px-3 py-3">Domaine</th>
                                <th scope="col" class="px-3 py-3">Format</th>
                                <th scope="col" class="px-3 py-3">Téléchargements</th>
                                <th scope="col" class="px-3 py-3">Date d'ajout</th>
                                <th scope="col" class="px-3 py-3">Statut</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($guides as $guide)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <input
                                            type="checkbox"
                                            aria-label="Sélectionner : {{ $guide->title }}"
                                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                    </td>

                                    <td class="px-2 py-3 text-sm text-slate-500">
                                        {{ $loop->iteration + ($guides->firstItem() - 1) }}
                                    </td>

                                    <td class="px-2 py-3">
                                        <img
                                            src="{{ $guide->coverUrl() }}"
                                            alt=""
                                            width="80" height="100"
                                            loading="lazy"
                                            class="h-12 w-10 rounded border border-slate-200 object-cover"
                                        >
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="block max-w-[13rem] text-sm font-semibold leading-snug text-slate-900">
                                            {{ $guide->title }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        @if($guide->category)
                                            <x-admin.badge :tone="$guide->category->tone">{{ $guide->category->name }}</x-admin.badge>
                                        @else
                                            <span class="text-sm text-slate-400">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-3">
                                        <span class="flex items-start gap-1.5">
                                            <x-icon name="document" class="mt-0.5 h-4 w-4 shrink-0 text-rose-500" />
                                            <span class="leading-tight">
                                                <span class="block text-sm text-slate-700">PDF</span>
                                                <span class="block text-xs text-slate-400">{{ $guide->fileSizeForHumans() }}</span>
                                            </span>
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="inline-flex items-center gap-1.5 text-sm text-slate-600">
                                            <x-icon name="download" class="h-4 w-4 shrink-0 text-slate-400" />
                                            {{ number_format($guide->downloads, 0, ',', ' ') }}
                                            <span class="sr-only">téléchargements</span>
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-3 text-sm leading-tight text-slate-500">
                                        @if($guide->published_at)
                                            {{ $guide->published_at->translatedFormat('j F Y') }}<br>
                                            <span class="text-xs">{{ $guide->published_at->format('H:i') }}</span>
                                        @else
                                            <span class="text-slate-400">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$guide->statusTone()">{{ $guide->statusLabel() }}</x-admin.badge>
                                    </td>

                                    <td class="px-5 py-3">
                                        <x-admin.row-actions
                                            :label="$guide->title"
                                            edit-dialog="edit-guide-{{ $guide->id }}"
                                            :view-href="$guide->isPublic() ? route('resources.guides.show', $guide) : null"
                                            :duplicate-action="route('admin.guides.duplicate', $guide)"
                                            :delete-action="route('admin.guides.destroy', $guide)"
                                        />
                                    </td>
                                </tr>

                                <x-admin.edit-modal id="edit-guide-{{ $guide->id }}" title="Modifier la fiche pratique" :action="route('admin.guides.update', $guide)">
                                    <div>
                                        <label for="edit-guide-{{ $guide->id }}-title" class="block text-sm font-medium text-slate-700">
                                            Titre de la fiche
                                            <span class="text-rose-600" aria-hidden="true">*</span>
                                        </label>
                                        <input type="text" id="edit-guide-{{ $guide->id }}-title" name="title" required value="{{ $guide->title }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                    </div>

                                    <div>
                                        <label for="edit-guide-{{ $guide->id }}-category" class="block text-sm font-medium text-slate-700">Domaine du droit</label>
                                        <select id="edit-guide-{{ $guide->id }}-category" name="category_id" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                            <option value="">Sélectionner un domaine</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" @selected($guide->category_id === $category->id)>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="edit-guide-{{ $guide->id }}-description" class="block text-sm font-medium text-slate-700">Description</label>
                                        <textarea id="edit-guide-{{ $guide->id }}-description" name="description" rows="2" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">{{ $guide->description }}</textarea>
                                    </div>

                                    <div>
                                        <label for="edit-guide-{{ $guide->id }}-body" class="block text-sm font-medium text-slate-700">Contenu de la page</label>
                                        <textarea id="edit-guide-{{ $guide->id }}-body" name="body" rows="5" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">{{ $guide->body }}</textarea>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label for="edit-guide-{{ $guide->id }}-status" class="block text-sm font-medium text-slate-700">Statut</label>
                                            <select id="edit-guide-{{ $guide->id }}-status" name="status" required class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                                <option value="draft" @selected($guide->status === 'draft')>Brouillon</option>
                                                <option value="published" @selected($guide->status === 'published')>Publié</option>
                                                <option value="archived" @selected($guide->status === 'archived')>Archivé</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="edit-guide-{{ $guide->id }}-published-at" class="block text-sm font-medium text-slate-700">Date de publication</label>
                                            <input type="date" id="edit-guide-{{ $guide->id }}-published-at" name="published_at" value="{{ $guide->published_at?->format('Y-m-d') }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                        </div>
                                    </div>

                                    <div>
                                        <span class="block text-sm font-medium text-slate-700">Image de couverture</span>
                                        <div class="mt-1.5 flex items-center gap-3">
                                            <img src="{{ $guide->coverUrl() }}" alt="" class="h-14 w-11 shrink-0 rounded-md border border-slate-200 object-cover">
                                            <input type="file" name="cover" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100">
                                        </div>
                                    </div>

                                    <div>
                                        <span class="block text-sm font-medium text-slate-700">Fichier PDF</span>
                                        <div class="mt-1.5 flex items-center gap-3">
                                            <span class="flex items-center gap-1.5 text-sm text-slate-600">
                                                <x-icon name="document" class="h-4 w-4 shrink-0 text-rose-500" />
                                                {{ $guide->file_name }}
                                            </span>
                                        </div>
                                        <input type="file" name="document" accept="application/pdf" class="mt-1.5 block w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100">
                                        <p class="mt-1.5 text-xs text-slate-500">Laissez vide pour conserver le fichier actuel.</p>
                                    </div>
                                </x-admin.edit-modal>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-5 py-12 text-center">
                                        <p class="text-sm text-slate-500">Aucune fiche pratique ne correspond à cette recherche.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-admin.table-footer :paginator="$guides" unit="fiches pratiques" />
            </section>
        </div>

        {{-- ============ ADD GUIDE ============ --}}
        <aside>
            <section id="ajouter-une-fiche" class="rounded-xl border border-slate-200 bg-white p-5">

                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="document" class="h-5 w-5 text-primary-600" />
                    Ajouter un guide pratique
                </h2>

                {{--
                    The accept and size hints below are conveniences, never
                    the security boundary: GuideRequest re-checks every field,
                    MediaStorage re-reads the PDF's own bytes, and the file
                    lands on a private disk outside the web root.
                --}}
                <form method="POST" action="{{ route('admin.guides.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
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
                            name="title"
                            required
                            value="{{ old('title') }}"
                            placeholder="Ex. Accès à la justice pour tous"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="guide-category" class="block text-sm font-medium text-slate-700">Domaine du droit</label>
                        <select
                            id="guide-category"
                            name="category_id"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option value="">Sélectionner un domaine</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="guide-description" class="block text-sm font-medium text-slate-700">Description</label>
                        <textarea
                            id="guide-description"
                            name="description"
                            rows="3"
                            placeholder="Décrivez brièvement le contenu de la fiche..."
                            class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label for="guide-body" class="block text-sm font-medium text-slate-700">Contenu de la page</label>
                        <textarea
                            id="guide-body"
                            name="body"
                            rows="5"
                            aria-describedby="guide-body-hint"
                            placeholder="Le texte affiché sur la page de la fiche..."
                            class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >{{ old('body') }}</textarea>
                        <p id="guide-body-hint" class="mt-1.5 text-xs text-slate-500">
                            Les sauts de ligne sont conservés tels quels.
                        </p>
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
                            for="guide-cover"
                            class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
                        >
                            <x-icon name="image" class="h-7 w-7 text-primary-500" />
                            <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour importer une image</span>
                            <span class="mt-1 text-xs text-slate-400">(JPG, PNG - Max 2MB)</span>

                            <input
                                type="file"
                                id="guide-cover"
                                name="cover"
                                accept="image/jpeg,image/png,image/webp"
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
