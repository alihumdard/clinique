@extends('layouts.admin')

@section('title', 'Gestion des partenaires')
@section('search_action', route('admin.partners'))
@section('search_placeholder', 'Rechercher un partenaire, une organisation...')

@section('content')

    <x-admin.flash />

    <x-admin.page-header
        title="Gestion des partenaires"
        lead="Gérez les organisations et institutions partenaires de la Clinique Juridique FSJP-UAM."
        action-label="Ajouter un partenaire"
        action-dialog="create-partner"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Partenaires'],
        ]"
    />

    <div class="mt-6 space-y-5">

        <div class="space-y-5">

            {{-- ============ KPIs ============ --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card icon="check-circle" color="green" :value="$counts['published']" label="Partenaires visibles" note="affichés sur le site" />
                <x-admin.stat-card icon="eye-off"      color="amber" :value="$counts['hidden']"    label="Masqués" note="enregistrés, non affichés" />
                <x-admin.stat-card icon="document"     color="blue"  :value="$counts['drafts']"    label="Brouillons" />
                <x-admin.stat-card icon="trash"        color="red"   :value="$counts['trashed']"   :href="route('admin.partners', ['statut' => 'corbeille'])"
                                   label="À la corbeille" />
            </div>

            {{-- ============ TABLE ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white">

                {{-- One GET form: search, catégorie and statut submit together,
                     so a filtered list is a URL that can be shared. --}}
                <form method="GET" action="{{ route('admin.partners') }}" class="space-y-3 p-5">
                    <div role="search" class="relative w-full lg:max-w-xs">
                        <label for="partners-search" class="sr-only">Rechercher un partenaire</label>
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input
                            type="search"
                            id="partners-search"
                            name="q"
                            value="{{ $filters['q'] ?? '' }}"
                            placeholder="Rechercher un partenaire..."
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
                            <label for="partners-category" class="sr-only">Filtrer par catégorie</label>
                            <select
                                id="partners-category"
                                name="categorie"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-3.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-auto"
                            >
                                <option value="">Toutes les catégories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) ($filters['categorie'] ?? 0) === $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="min-w-0 sm:shrink-0">
                            <label for="partners-status" class="sr-only">Filtrer par statut</label>
                            <select
                                id="partners-status"
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
                            <a href="{{ route('admin.partners') }}" class="col-span-2 inline-flex w-full shrink-0 items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-500 transition-colors hover:bg-slate-50 sm:col-span-1 sm:w-auto">
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
                    <table class="w-full min-w-[56rem] text-left">
                        <thead>
                            <tr class="whitespace-nowrap border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <th scope="col" class="w-12 px-5 py-3">
                                    <input
                                        type="checkbox"
                                        aria-label="Sélectionner tous les partenaires"
                                        class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                    >
                                </th>
                                <th scope="col" class="w-12 px-2 py-3">#</th>
                                <th scope="col" class="w-20 px-2 py-3">Logo</th>
                                <th scope="col" class="min-w-[14rem] px-3 py-3">Nom du partenaire</th>
                                <th scope="col" class="px-3 py-3">Catégorie</th>
                                <th scope="col" class="px-3 py-3">Site web</th>
                                <th scope="col" class="px-3 py-3">Statut</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($partners as $partner)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <input
                                            type="checkbox"
                                            aria-label="Sélectionner : {{ $partner->name }}"
                                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                    </td>

                                    <td class="px-2 py-3 text-sm text-slate-500">
                                        {{ $loop->iteration + ($partners->firstItem() - 1) }}
                                    </td>

                                    <td class="px-2 py-3">
                                        {{-- Logo tile: white background, since partner marks
                                             are supplied as transparent artwork. --}}
                                        <span class="flex h-11 w-14 items-center justify-center rounded-md border border-slate-200 bg-white px-1">
                                            @if($partner->logo)
                                                <img
                                                    src="{{ asset('images/partners/'.$partner->logo.'.png') }}"
                                                    alt=""
                                                    loading="lazy"
                                                    class="max-h-9 max-w-full object-contain"
                                                >
                                            @else
                                                <span class="text-[10px] font-bold text-slate-500">
                                                    {{ mb_strtoupper(mb_substr($partner->name, 0, 4)) }}
                                                </span>
                                            @endif
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="block max-w-[15rem] text-sm font-semibold leading-snug text-slate-900">
                                            {{ $partner->name }}
                                        </span>
                                        @if($partner->description)
                                            <span class="mt-0.5 block max-w-[15rem] truncate text-xs text-slate-400">
                                                {{ $partner->description }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3">
                                        @if($partner->category)
                                            <x-admin.badge :tone="$partner->category->tone">{{ $partner->category->name }}</x-admin.badge>
                                        @else
                                            <span class="text-sm text-slate-400">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3">
                                        @if($partner->website)
                                            <a
                                                href="{{ $partner->website }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center gap-1.5 text-sm text-primary-600 hover:text-primary-700"
                                            >
                                                <x-icon name="link" class="h-4 w-4 shrink-0" />
                                                <span class="sr-only">Ouvrir le site de {{ $partner->name }}</span>
                                                Voir
                                            </a>
                                        @else
                                            <span class="text-sm text-slate-400">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$partner->statusTone()">{{ $partner->statusLabel() }}</x-admin.badge>
                                    </td>

                                    <td class="px-5 py-3">
                                        <x-admin.row-actions
                                            :label="$partner->name"
                                            edit-dialog="edit-partner-{{ $partner->id }}"
                                            :view-href="route('partners')"
                                            :duplicate-action="route('admin.partners.duplicate', $partner)"
                                            :toggle-action="route('admin.partners.toggle', $partner)"
                                            :is-visible="$partner->is_visible"
                                            :delete-action="route('admin.partners.destroy', $partner)"
                                            :restore-action="$partner->trashed() ? route('admin.partners.restore', $partner->id) : null"
                                            :force-delete-action="$partner->trashed() && auth()->user()->isAdmin() ? route('admin.partners.force-destroy', $partner->id) : null"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-12 text-center">
                                        <p class="text-sm text-slate-500">Aucun partenaire ne correspond à cette recherche.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-admin.table-footer :paginator="$partners" unit="partenaires" />
            </section>
        </div>

    </div>

    {{--
        Every row's "Modifier" dialog, rendered here rather than inside the
        table above: a <dialog> is not valid content for <tbody> between
        <tr> elements, and a browser that encounters one there foster-
        parents it out — which was silently corrupting the <form> markup
        of every dialog on the page (content spilling out as siblings of
        an empty <form>, breaking its height/scroll layout) without ever
        raising a console error, since this is valid-by-recovery HTML, not
        a parse error.
    --}}
    @foreach ($partners as $partner)
        <x-admin.edit-modal id="edit-partner-{{ $partner->id }}" title="Modifier le partenaire" :action="route('admin.partners.update', $partner)">
            <div>
                <label for="edit-partner-{{ $partner->id }}-name" class="block text-sm font-medium text-slate-700">
                    Nom du partenaire
                    <span class="text-rose-600" aria-hidden="true">*</span>
                </label>
                <input type="text" id="edit-partner-{{ $partner->id }}-name" name="name" required value="{{ $partner->name }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <div>
                <label for="edit-partner-{{ $partner->id }}-category" class="block text-sm font-medium text-slate-700">Catégorie</label>
                <select id="edit-partner-{{ $partner->id }}-category" name="category_id" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    <option value="">Sélectionner une catégorie</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($partner->category_id === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="edit-partner-{{ $partner->id }}-description" class="block text-sm font-medium text-slate-700">Description</label>
                <textarea id="edit-partner-{{ $partner->id }}-description" name="description" rows="3" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">{{ $partner->description }}</textarea>
            </div>

            <div>
                {{-- Partner logos are stored on disk under public/images/partners and
                     referenced on this screen by a bare filename, not through
                     MediaStorage like articles or reports. The edit form keeps that
                     same convention: an uploaded file still goes through
                     PartnerController's storeImage call, same as the quick-create form. --}}
                <span class="block text-sm font-medium text-slate-700">Logo</span>
                <div class="mt-1.5 flex items-center gap-3">
                    <span class="flex h-14 w-20 shrink-0 items-center justify-center rounded-md border border-slate-200 bg-white px-1">
                        @if($partner->logo)
                            <img src="{{ asset('images/partners/'.$partner->logo.'.png') }}" alt="" class="max-h-11 max-w-full object-contain">
                        @else
                            <span class="text-[10px] font-bold text-slate-500">{{ mb_strtoupper(mb_substr($partner->name, 0, 4)) }}</span>
                        @endif
                    </span>
                    <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100">
                </div>

                {{-- The checkbox rather than x-admin.image-field: the preview
                     above is this screen's own, a bundled asset by bare
                     filename with the partner's initials when there is none.
                     Removing the logo falls back to those initials. --}}
                @if($partner->logo)
                    <label for="edit-partner-{{ $partner->id }}-remove-logo" class="mt-2 inline-flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" id="edit-partner-{{ $partner->id }}-remove-logo" name="remove_logo" value="1" class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                        <span>Retirer le logo actuel</span>
                    </label>
                @endif
            </div>

            <div>
                <label for="edit-partner-{{ $partner->id }}-website" class="block text-sm font-medium text-slate-700">Site web</label>
                <input type="url" id="edit-partner-{{ $partner->id }}-website" name="website" value="{{ $partner->website }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="edit-partner-{{ $partner->id }}-status" class="block text-sm font-medium text-slate-700">Statut</label>
                    <select id="edit-partner-{{ $partner->id }}-status" name="status" required class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                        <option value="draft" @selected($partner->status === 'draft')>Brouillon</option>
                        <option value="published" @selected($partner->status === 'published')>Publié</option>
                        <option value="archived" @selected($partner->status === 'archived')>Archivé</option>
                    </select>
                </div>

                <div>
                    <label for="edit-partner-{{ $partner->id }}-position" class="block text-sm font-medium text-slate-700">Ordre</label>
                    <input type="number" id="edit-partner-{{ $partner->id }}-position" name="position" min="0" value="{{ $partner->position }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>
            </div>

            <div class="pt-1">
                <x-admin.toggle name="is_visible" label="Afficher sur la page publique" :checked="(bool) $partner->is_visible" />
            </div>
        </x-admin.edit-modal>
    @endforeach

    {{--
        "Ajouter un partenaire" in the page header opens this dialog rather
        than scrolling to a sidebar form, matching the Actualités pattern.
    --}}
    <x-admin.edit-modal id="create-partner" title="Ajouter un partenaire" :action="route('admin.partners.store')" method="POST" submit-label="Ajouter le partenaire">
        <div>
            <label for="partner-name" class="block text-sm font-medium text-slate-700">
                Nom du partenaire
                <span class="text-rose-600" aria-hidden="true">*</span>
                <span class="sr-only">(obligatoire)</span>
            </label>
            <input
                type="text"
                id="partner-name"
                name="name"
                required
                placeholder="Ex. Union Européenne"
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
        </div>

        <div>
            <label for="partner-category" class="block text-sm font-medium text-slate-700">Catégorie</label>
            <select
                id="partner-category"
                name="category_id"
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
                <option value="">Sélectionner une catégorie</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="partner-description" class="block text-sm font-medium text-slate-700">Description</label>
            <textarea
                id="partner-description"
                name="description"
                rows="3"
                aria-describedby="partner-description-hint"
                placeholder="Ex. Programme des Nations Unies pour le Développement"
                class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            ></textarea>
            <p id="partner-description-hint" class="mt-1.5 text-xs text-slate-500">
                Affichée sous le nom lorsqu'aucun logo n'a été importé.
            </p>
        </div>

        <div>
            <span class="block text-sm font-medium text-slate-700">Logo</span>

            <label
                for="partner-logo"
                class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
            >
                <x-icon name="upload" class="h-7 w-7 text-primary-500" />
                <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour importer le logo</span>
                <span class="mt-1 text-xs text-slate-400">(PNG transparent, JPG ou WebP - Max 1MB)</span>

                <input
                    type="file"
                    id="partner-logo"
                    name="logo"
                    accept="image/png,image/jpeg,image/webp"
                    class="sr-only"
                >
            </label>
        </div>

        <div>
            <label for="partner-website" class="block text-sm font-medium text-slate-700">Site web</label>
            <div class="relative mt-1.5">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <x-icon name="link" class="h-4 w-4" />
                </span>
                <input
                    type="url"
                    id="partner-website"
                    name="website"
                    placeholder="https://www.exemple.org"
                    class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                >
            </div>
        </div>

        <div>
            <label for="partner-position" class="block text-sm font-medium text-slate-700">Ordre d'affichage</label>
            <input
                type="number"
                id="partner-position"
                name="position"
                min="0"
                aria-describedby="partner-position-hint"
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
            <p id="partner-position-hint" class="mt-1.5 text-xs text-slate-500">
                Laissez vide pour ajouter à la fin de la liste.
            </p>
        </div>

        <div>
            <label for="partner-status" class="block text-sm font-medium text-slate-700">
                Statut
                <span class="text-rose-600" aria-hidden="true">*</span>
                <span class="sr-only">(obligatoire)</span>
            </label>
            <select
                id="partner-status"
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

        <div class="pt-1">
            <x-admin.toggle name="is_visible" label="Afficher sur la page publique"
                :checked="session()->hasOldInput() ? (bool) old('is_visible') : true" />
        </div>
    </x-admin.edit-modal>

@endsection
