@extends('layouts.admin')

@section('title', 'Gestion des activités & galerie')
@section('search_action', route('admin.activities'))
@section('search_placeholder', 'Rechercher une activité, un lieu, une date...')

@section('content')

    <x-admin.flash />

    <x-admin.page-header
        title="Gestion des activités & galerie"
        lead="Gérez les activités, caravanes juridiques et leur galerie photos."
        action-label="Ajouter une activité"
        action-dialog="create-activity"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Activités & caravanes'],
        ]"
    />
    <div class="mt-6 space-y-5">

        <div class="space-y-5">

            {{-- ============ KPIs ============ --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card icon="calendar"     color="blue"  :value="$counts['published']" label="Activités publiées" keep-at-zero />
                <x-admin.stat-card icon="clock"        color="amber" :value="$counts['upcoming']"  label="À venir" note="prochaines activités" />
                <x-admin.stat-card icon="check-circle" color="green" :value="$counts['past']"      label="Activités passées" />
                <x-admin.stat-card icon="image"        color="red"   :value="$counts['photos']"    label="Photos" note="dans la galerie" />
            </div>

            {{-- ============ TABLE ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white">

                @php
                    $vue = $filters['vue'] ?? null;

                    // The tabs keep whatever else is filtered, so switching
                    // between "À venir" and "Passées" does not clear a search.
                    $tabLink = fn (array $extra) => route('admin.activities', array_merge(
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
                            ['label' => 'Toutes les activités', 'count' => $counts['all'], 'active' => ! $vue, 'href' => $tabLink([])],
                            ['label' => 'À venir', 'count' => $counts['upcoming'], 'active' => $vue === 'a-venir', 'href' => $tabLink(['vue' => 'a-venir'])],
                            ['label' => 'Passées', 'count' => $counts['past'], 'active' => $vue === 'passees', 'href' => $tabLink(['vue' => 'passees'])],
                        ]"
                    />

                    <form method="GET" action="{{ route('admin.activities') }}" class="flex shrink-0 flex-wrap items-center gap-3">
                        @if($vue)
                            <input type="hidden" name="vue" value="{{ $vue }}">
                        @endif

                        <div role="search" class="relative w-full min-w-[12rem] flex-1 sm:w-56">
                            <label for="activities-search" class="sr-only">Rechercher une activité</label>
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <x-icon name="search" class="h-4 w-4" />
                            </span>
                            <input
                                type="search"
                                id="activities-search"
                                name="q"
                                value="{{ $filters['q'] ?? '' }}"
                                placeholder="Rechercher une activité..."
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                        </div>

                        <div class="min-w-0 shrink-0">
                            <label for="activities-type" class="sr-only">Filtrer par type</label>
                            <select
                                id="activities-type"
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
                            <a href="{{ route('admin.activities') }}" class="shrink-0 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-500 transition-colors hover:bg-slate-50">
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
                                <th scope="col" class="w-12 px-5 py-3">
                                    <input
                                        type="checkbox"
                                        aria-label="Sélectionner toutes les activités"
                                        class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                    >
                                </th>
                                <th scope="col" class="w-12 px-2 py-3">#</th>
                                <th scope="col" class="w-20 px-2 py-3">Image</th>
                                <th scope="col" class="min-w-[9rem] px-3 py-3">Titre de l'activité</th>
                                <th scope="col" class="px-3 py-3">Type</th>
                                <th scope="col" class="px-3 py-3">Lieu</th>
                                <th scope="col" class="px-3 py-3">Date</th>
                                <th scope="col" class="px-3 py-3">Statut</th>
                                <th scope="col" class="px-3 py-3">Galerie</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($activities as $activity)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <input
                                            type="checkbox"
                                            aria-label="Sélectionner : {{ $activity->title }}"
                                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                    </td>

                                    <td class="px-2 py-3 text-sm text-slate-500">
                                        {{ $loop->iteration + ($activities->firstItem() - 1) }}
                                    </td>

                                    <td class="px-2 py-3">
                                        <img
                                            src="{{ $activity->coverUrl() }}"
                                            alt=""
                                            width="96" height="72"
                                            loading="lazy"
                                            class="h-11 w-14 rounded-md object-cover"
                                        >
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="block max-w-[13rem] text-sm font-semibold leading-snug text-slate-900">
                                            {{ $activity->title }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        @if($activity->category)
                                            <x-admin.badge :tone="$activity->category->tone">{{ $activity->category->name }}</x-admin.badge>
                                        @else
                                            <span class="text-sm text-slate-400">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3">
                                        @if($activity->location)
                                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap text-sm text-slate-600">
                                                <x-icon name="map-pin" class="h-4 w-4 shrink-0 text-slate-400" />
                                                {{ $activity->location }}
                                            </span>
                                        @else
                                            <span class="text-sm text-slate-400">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3">
                                        @if($activity->held_on)
                                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap text-sm text-slate-600">
                                                <x-icon name="calendar" class="h-4 w-4 shrink-0 text-slate-400" />
                                                {{ $activity->held_on->translatedFormat('j M Y') }}
                                            </span>
                                        @else
                                            <span class="text-sm text-slate-400">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <x-admin.badge :tone="$activity->statusTone()">{{ $activity->statusLabel() }}</x-admin.badge>
                                            @if($activity->isUpcoming())
                                                <x-admin.badge tone="amber">À venir</x-admin.badge>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-3 py-3">
                                        <button
                                            type="button"
                                            data-open-dialog="edit-activity-{{ $activity->id }}"
                                            data-scroll-to="edit-activity-{{ $activity->id }}-gallery-section"
                                            class="inline-flex items-center gap-1.5 text-sm text-slate-600 hover:text-primary-600"
                                        >
                                            <x-icon name="image" class="h-4 w-4 shrink-0 text-slate-400" />
                                            {{ $activity->photos_count }}
                                            <span class="sr-only">photos dans la galerie &mdash; ouvrir pour les gérer</span>
                                        </button>
                                    </td>

                                    <td class="px-5 py-3">
                                        <x-admin.row-actions
                                            :label="$activity->title"
                                            edit-dialog="edit-activity-{{ $activity->id }}"
                                            :view-href="$activity->isPublic() ? route('activities.show', $activity) : null"
                                            :duplicate-action="route('admin.activities.duplicate', $activity)"
                                            :delete-action="route('admin.activities.destroy', $activity)"
                                            :restore-action="$activity->trashed() ? route('admin.activities.restore', $activity->id) : null"
                                            :force-delete-action="$activity->trashed() && auth()->user()->isAdmin() ? route('admin.activities.force-destroy', $activity->id) : null"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-5 py-12 text-center">
                                        <p class="text-sm text-slate-500">Aucune activité ne correspond à cette recherche.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-admin.table-footer :paginator="$activities" unit="activités" />
            </section>
        </div>

    </div>

    {{--
        Every row's "Modifier" dialog, rendered here rather than inside the
        table above: a dialog element is not valid content for a table body
        between row elements, and a browser that encounters one there
        foster-parents it out of the table during parsing - which was
        silently corrupting the form markup of every dialog on the page
        (content spilling out as siblings of an empty form, breaking its
        height/scroll layout) without ever raising a console error, since
        this is valid-by-recovery HTML, not a parse error.
    --}}
    @foreach ($activities as $activity)
        <x-admin.edit-modal id="edit-activity-{{ $activity->id }}" title="Modifier l'activité" :action="route('admin.activities.update', $activity)">
            <div>
                <label for="edit-activity-{{ $activity->id }}-title" class="block text-sm font-medium text-slate-700">
                    Titre
                    <span class="text-rose-600" aria-hidden="true">*</span>
                </label>
                <input type="text" id="edit-activity-{{ $activity->id }}-title" name="title" required value="{{ $activity->title }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <div>
                <label for="edit-activity-{{ $activity->id }}-category" class="block text-sm font-medium text-slate-700">Type d'activité</label>
                <select id="edit-activity-{{ $activity->id }}-category" name="category_id" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    <option value="">Sélectionner un type</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($activity->category_id === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="edit-activity-{{ $activity->id }}-location" class="block text-sm font-medium text-slate-700">Lieu</label>
                    <input type="text" id="edit-activity-{{ $activity->id }}-location" name="location" value="{{ $activity->location }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>

                <div>
                    <label for="edit-activity-{{ $activity->id }}-held-on" class="block text-sm font-medium text-slate-700">Date de l'activité</label>
                    <input type="date" id="edit-activity-{{ $activity->id }}-held-on" name="held_on" value="{{ $activity->held_on?->format('Y-m-d') }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>
            </div>

            <div>
                <label for="edit-activity-{{ $activity->id }}-excerpt" class="block text-sm font-medium text-slate-700">Description</label>
                <textarea id="edit-activity-{{ $activity->id }}-excerpt" name="excerpt" rows="2" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">{{ $activity->excerpt }}</textarea>
            </div>

            <div>
                <label for="edit-activity-{{ $activity->id }}-body" class="block text-sm font-medium text-slate-700">Contenu</label>
                <textarea id="edit-activity-{{ $activity->id }}-body" name="body" rows="6" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">{{ $activity->body }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="edit-activity-{{ $activity->id }}-status" class="block text-sm font-medium text-slate-700">Statut</label>
                    <select id="edit-activity-{{ $activity->id }}-status" name="status" required class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                        <option value="draft" @selected($activity->status === 'draft')>Brouillon</option>
                        <option value="published" @selected($activity->status === 'published')>Publié</option>
                        <option value="archived" @selected($activity->status === 'archived')>Archivé</option>
                    </select>
                </div>

                <div>
                    <label for="edit-activity-{{ $activity->id }}-published-at" class="block text-sm font-medium text-slate-700">Date de publication</label>
                    <input type="date" id="edit-activity-{{ $activity->id }}-published-at" name="published_at" value="{{ $activity->published_at?->format('Y-m-d') }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>
            </div>

                <x-admin.image-field
                    name="image"
                    :id="'edit-activity-'.$activity->id.'-image'"
                    label="Image principale"
                    :current="$activity->image"
                    :preview="$activity->coverUrl()"
                    accept="image/jpeg,image/png,image/webp"
                />

            <div id="edit-activity-{{ $activity->id }}-gallery-section">
                <div class="flex items-center justify-between gap-3">
                    <span class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                        <x-icon name="image" class="h-5 w-5 text-primary-600" />
                        Galerie photos
                    </span>
                    <span class="text-xs text-slate-500">{{ $activity->photos_count }} actuellement &middot; 20 à la fois max</span>
                </div>

                <label
                    for="edit-activity-{{ $activity->id }}-gallery"
                    class="mt-2 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
                >
                    <x-icon name="upload" class="h-7 w-7 text-primary-500" />
                    <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour ajouter des photos</span>
                    <span class="mt-0.5 text-xs text-slate-500">ou glissez vos images ici &mdash; s'ajoutent à la galerie existante</span>
                    <span class="mt-1 text-xs text-slate-400">(JPG, PNG - Max 5MB chacune)</span>

                    <input
                        type="file"
                        id="edit-activity-{{ $activity->id }}-gallery"
                        name="photos[]"
                        accept="image/jpeg,image/png,image/webp"
                        multiple
                        class="sr-only"
                    >
                </label>
            </div>
        </x-admin.edit-modal>
    @endforeach

    {{--
        "Ajouter une activité" in the page header opens this dialog rather
        than scrolling to a sidebar form, matching the Actualités pattern.
    --}}
    <x-admin.edit-modal id="create-activity" title="Ajouter une activité" :action="route('admin.activities.store')" method="POST" submit-label="Publier l'activité">
        <div>
            <label for="activity-title" class="block text-sm font-medium text-slate-700">
                Titre de l'activité
                <span class="text-rose-600" aria-hidden="true">*</span>
                <span class="sr-only">(obligatoire)</span>
            </label>
            <input
                type="text"
                id="activity-title"
                name="title"
                required
                placeholder="Ex. Caravane juridique à Zinder"
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
        </div>

        <div>
            <label for="activity-type" class="block text-sm font-medium text-slate-700">Type d'activité</label>
            <select
                id="activity-type"
                name="category_id"
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
                <option value="">Sélectionner un type</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Lieu & date: the two fields that separate an activity
             from an actualité. --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="activity-location" class="block text-sm font-medium text-slate-700">Lieu</label>
                <div class="relative mt-1.5">
                    <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <x-icon name="map-pin" class="h-4 w-4" />
                    </span>
                    <input
                        type="text"
                        id="activity-location"
                        name="location"
                        placeholder="Ex. Zinder"
                        class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-3.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    >
                </div>
            </div>

            <div>
                <label for="activity-date" class="block text-sm font-medium text-slate-700">Date de l'activité</label>
                <input
                    type="date"
                    id="activity-date"
                    name="held_on"
                    aria-describedby="activity-date-hint"
                    class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                >
            </div>
        </div>

        <p id="activity-date-hint" class="-mt-2 text-xs text-slate-500">
            Une date à venir classe l'activité dans « À venir » ; une date passée dans « Passées ».
        </p>

        <div>
            <label for="activity-description" class="block text-sm font-medium text-slate-700">Description</label>
            <textarea
                id="activity-description"
                name="excerpt"
                rows="3"
                placeholder="Résumez l'activité en quelques lignes..."
                class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            ></textarea>
        </div>

        <div>
            <label for="activity-body" class="block text-sm font-medium text-slate-700">Contenu</label>
            <textarea id="activity-body" name="body" rows="6" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"></textarea>
        </div>

        <div>
            <label for="activity-published-at" class="block text-sm font-medium text-slate-700">Date de publication</label>
            <input type="date" id="activity-published-at" name="published_at" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
        </div>

        <div>
            <span class="block text-sm font-medium text-slate-700">Image principale</span>

            <label
                for="activity-image"
                class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-5 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
            >
                <x-icon name="image" class="h-6 w-6 text-primary-500" />
                <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour importer une image</span>
                <span class="mt-1 text-xs text-slate-400">(JPG, PNG - Max 2MB)</span>

                <input
                    type="file"
                    id="activity-image"
                    name="image"
                    accept="image/jpeg,image/png,image/webp"
                    class="sr-only"
                >
            </label>
        </div>

        <div>
            <div class="flex items-center justify-between gap-3">
                <span class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                    <x-icon name="image" class="h-5 w-5 text-primary-600" />
                    Galerie photos
                </span>
                <span class="text-xs text-slate-500">jusqu'à 20 à la fois</span>
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
                    accept="image/jpeg,image/png,image/webp"
                    multiple
                    class="sr-only"
                >
            </label>
        </div>

        <div>
            <label for="create-activity-status" class="block text-sm font-medium text-slate-700">
                Statut
                <span class="text-rose-600" aria-hidden="true">*</span>
                <span class="sr-only">(obligatoire)</span>
            </label>
            <select
                id="create-activity-status"
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
