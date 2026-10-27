@extends('layouts.admin')

@section('title', 'Gestion des pages')
@section('search_placeholder', 'Rechercher une page, un titre, un mot-clé...')

@section('content')

    <x-admin.flash />

    <x-admin.page-header
        title="Gestion des pages"
        lead="Créez et modifiez les pages que vous rédigez vous-même."
        action-label="Ajouter une page"
        action-dialog="create-page"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Pages'],
        ]"
    />

    {{--
        The site's own pages are not rows here. Each is a designed template
        with its own sections and its own route, so an "edit" on this screen
        could not change them — modifying their text is a developer change.
        They are listed further down as a reference instead.
    --}}
    <div class="mt-6 flex items-start gap-3 rounded-xl border border-primary-100 bg-primary-50/60 p-4">
        <x-icon name="info" class="mt-0.5 h-5 w-5 shrink-0 text-primary-600" />
        <p class="text-sm leading-relaxed text-slate-700">
            Cet écran gère les pages que vous rédigez vous-même : Cookies, Accessibilité, Crédits
            ou toute autre page à ajouter. Les pages principales du site (Accueil, Services,
            Contact, Mentions légales…) sont des gabarits conçus sur mesure ; elles sont listées
            en bas de page pour information.
        </p>
    </div>

    <div class="mt-5 space-y-5">

        <div class="space-y-5">

            {{-- ============ KPIs ============ --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card icon="document" color="green" :value="$counts['published']" label="Pages publiées" />
                <x-admin.stat-card icon="eye-off"  color="amber" :value="$counts['drafts']"    label="Brouillons" />
                <x-admin.stat-card icon="clock"    color="blue"  :value="$counts['scheduled']" label="Programmées" />
                <x-admin.stat-card icon="trash"    color="red"   :value="$counts['trashed']"   label="À la corbeille" />
            </div>

            {{-- ============ TABLE ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white">

                @php
                    $statut = $filters['statut'] ?? null;

                    $tabLink = fn (array $extra) => route('admin.pages', array_merge(
                        array_filter(['q' => $filters['q'] ?? null]),
                        $extra
                    ));
                @endphp

                <div class="flex flex-col gap-4 p-5 lg:flex-row lg:items-center lg:justify-between">
                    <x-admin.filter-tabs
                        class="lg:border-b-0"
                        :tabs="[
                            ['label' => 'Toutes', 'active' => ! $statut, 'href' => $tabLink([])],
                            ['label' => 'Publiées', 'count' => $counts['published'], 'active' => $statut === 'published', 'href' => $tabLink(['statut' => 'published'])],
                            ['label' => 'Brouillons', 'count' => $counts['drafts'], 'active' => $statut === 'draft', 'href' => $tabLink(['statut' => 'draft'])],
                            ['label' => 'Corbeille', 'count' => $counts['trashed'], 'active' => $statut === 'corbeille', 'href' => $tabLink(['statut' => 'corbeille'])],
                        ]"
                    />

                    <form method="GET" action="{{ route('admin.pages') }}" class="flex shrink-0 flex-wrap items-center gap-3">
                        @if($statut)
                            <input type="hidden" name="statut" value="{{ $statut }}">
                        @endif

                        <div role="search" class="relative w-full min-w-[12rem] flex-1 sm:w-64">
                            <label for="pages-search" class="sr-only">Rechercher une page</label>
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <x-icon name="search" class="h-4 w-4" />
                            </span>
                            <input
                                type="search"
                                id="pages-search"
                                name="q"
                                value="{{ $filters['q'] ?? '' }}"
                                placeholder="Rechercher une page..."
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                        </div>

                        <button type="submit" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50">
                            <x-icon name="search" class="h-4 w-4" />
                            Rechercher
                        </button>

                        @if (array_filter($filters))
                            <a href="{{ route('admin.pages') }}" class="shrink-0 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-500 transition-colors hover:bg-slate-50">
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
                    <table class="w-full min-w-[52rem] text-left">
                        <thead>
                            <tr class="whitespace-nowrap border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <th scope="col" class="w-12 px-5 py-3">#</th>
                                <th scope="col" class="min-w-[14rem] px-3 py-3">Titre de la page</th>
                                <th scope="col" class="px-3 py-3">Adresse (URL)</th>
                                <th scope="col" class="px-3 py-3">Dernière modification</th>
                                <th scope="col" class="px-3 py-3">Statut</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($pages as $page)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3 text-sm text-slate-500">
                                        {{ $loop->iteration + ($pages->firstItem() - 1) }}
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="block max-w-[16rem] text-sm font-semibold leading-snug text-slate-900">
                                            {{ $page->title }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3 font-mono text-sm text-slate-500">/{{ $page->slug }}</td>

                                    <td class="whitespace-nowrap px-3 py-3 text-sm leading-tight text-slate-500">
                                        {{ $page->updated_at->translatedFormat('j F Y') }}<br>
                                        <span class="text-xs">{{ $page->updated_at->format('H:i') }}</span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$page->statusTone()">{{ $page->statusLabel() }}</x-admin.badge>
                                    </td>

                                    <td class="px-5 py-3">
                                        <x-admin.row-actions
                                            :label="$page->title"
                                            edit-dialog="edit-page-{{ $page->id }}"
                                            :view-href="$page->isPublic() ? route('pages.show', $page) : null"
                                            :duplicate-action="route('admin.pages.duplicate', $page)"
                                            :delete-action="route('admin.pages.destroy', $page)"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center">
                                        <p class="text-sm text-slate-500">
                                            @if (array_filter($filters))
                                                Aucune page ne correspond à cette recherche.
                                            @else
                                                Aucune page créée pour le moment.
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-admin.table-footer :paginator="$pages" unit="pages" />
            </section>

            {{-- ============ FIXED SITE PAGES (reference) ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="window" class="h-5 w-5 text-slate-400" />
                    Pages principales du site
                </h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-500">
                    Gabarits conçus sur mesure, avec leurs propres sections. Leur contenu provient
                    des écrans correspondants (Actualités, Web TV, Équipe…) ou des Paramètres du
                    site ; leur structure relève du développement.
                </p>

                <ul class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($fixed as $item)
                        <li>
                            <a
                                href="{{ $item['url'] }}"
                                target="_blank"
                                rel="noopener"
                                class="flex items-center gap-2 rounded-md border border-slate-100 px-3 py-2 text-sm text-slate-600 transition-colors hover:bg-slate-50 hover:text-primary-600"
                            >
                                <x-icon name="eye" class="h-4 w-4 shrink-0 text-slate-400" />
                                <span class="min-w-0 flex-1 truncate">{{ $item['title'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

    </div>

    {{--
        Every row's "Modifier" dialog, rendered here rather than inside the
        table above: a dialog element is not valid content for a table body
        between row elements, and a browser that encounters one there foster-
        parents it out — which was silently corrupting the form markup of
        every dialog on the page (content spilling out as siblings of an
        empty form, breaking its height/scroll layout) without ever raising
        a console error, since this is valid-by-recovery HTML, not a parse
        error. This only concerns the database-backed pages below ($pages);
        the fixed system pages listed above are a read-only reference with
        no edit dialogs of their own.
    --}}
    @foreach ($pages as $page)
        <x-admin.edit-modal id="edit-page-{{ $page->id }}" title="Modifier la page" :action="route('admin.pages.update', $page)">
            <div>
                <label for="edit-page-{{ $page->id }}-title" class="block text-sm font-medium text-slate-700">
                    Titre
                    <span class="text-rose-600" aria-hidden="true">*</span>
                </label>
                <input type="text" id="edit-page-{{ $page->id }}-title" name="title" required value="{{ $page->title }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <div>
                <label for="edit-page-{{ $page->id }}-slug" class="block text-sm font-medium text-slate-700">
                    Adresse de la page
                    <span class="text-rose-600" aria-hidden="true">*</span>
                </label>
                <div class="mt-1.5 flex items-center gap-1">
                    <span class="shrink-0 font-mono text-sm text-slate-400">/</span>
                    <input type="text" id="edit-page-{{ $page->id }}-slug" name="slug" required value="{{ $page->slug }}" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3.5 py-2.5 font-mono text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>
            </div>

            <div>
                <label for="edit-page-{{ $page->id }}-body" class="block text-sm font-medium text-slate-700">Contenu</label>
                <textarea id="edit-page-{{ $page->id }}-body" name="body" rows="8" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">{{ $page->body }}</textarea>
            </div>

            <div>
                <span class="block text-sm font-medium text-slate-700">Image de bannière</span>
                <div class="mt-1.5 flex items-center gap-3">
                    <img src="{{ $page->imageUrl() }}" alt="" class="h-14 w-20 shrink-0 rounded-md object-cover">
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100">
                </div>
            </div>

            <div>
                <label for="edit-page-{{ $page->id }}-seo-title" class="block text-sm font-medium text-slate-700">Titre SEO</label>
                <input type="text" id="edit-page-{{ $page->id }}-seo-title" name="seo_title" maxlength="70" value="{{ $page->seo_title }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>

            <div>
                <label for="edit-page-{{ $page->id }}-seo-description" class="block text-sm font-medium text-slate-700">Description SEO</label>
                <textarea id="edit-page-{{ $page->id }}-seo-description" name="seo_description" rows="2" maxlength="300" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">{{ $page->seo_description }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="edit-page-{{ $page->id }}-status" class="block text-sm font-medium text-slate-700">Statut</label>
                    <select id="edit-page-{{ $page->id }}-status" name="status" required class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                        <option value="draft" @selected($page->status === 'draft')>Brouillon</option>
                        <option value="published" @selected($page->status === 'published')>Publié</option>
                        <option value="archived" @selected($page->status === 'archived')>Archivé</option>
                    </select>
                </div>

                <div>
                    <label for="edit-page-{{ $page->id }}-published-at" class="block text-sm font-medium text-slate-700">Date de publication</label>
                    <input type="date" id="edit-page-{{ $page->id }}-published-at" name="published_at" value="{{ $page->published_at?->format('Y-m-d') }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>
            </div>
        </x-admin.edit-modal>
    @endforeach

    <x-admin.edit-modal id="create-page" title="Ajouter une page" :action="route('admin.pages.store')" method="POST" submit-label="Publier la page">
        <div>
            <label for="page-title" class="block text-sm font-medium text-slate-700">
                Titre de la page
                <span class="text-rose-600" aria-hidden="true">*</span>
                <span class="sr-only">(obligatoire)</span>
            </label>
            <input
                type="text"
                id="page-title"
                name="title"
                required
                placeholder="Ex. Accessibilité"
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
        </div>

        <div>
            <label for="page-slug" class="block text-sm font-medium text-slate-700">
                Adresse de la page
                <span class="text-rose-600" aria-hidden="true">*</span>
                <span class="sr-only">(obligatoire)</span>
            </label>
            <div class="mt-1.5 flex items-center gap-1">
                <span class="shrink-0 font-mono text-sm text-slate-400">/</span>
                <input
                    type="text"
                    id="page-slug"
                    name="slug"
                    required
                    aria-describedby="page-slug-hint"
                    placeholder="accessibilite"
                    class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3.5 py-2.5 font-mono text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                >
            </div>
            <p id="page-slug-hint" class="mt-1.5 text-xs text-slate-500">
                Lettres, chiffres et tirets. Une adresse déjà utilisée par le site est refusée.
            </p>
        </div>

        <div>
            <label for="page-body" class="block text-sm font-medium text-slate-700">Contenu</label>
            <textarea
                id="page-body"
                name="body"
                rows="10"
                aria-describedby="page-body-hint"
                placeholder="Le texte de la page..."
                class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            ></textarea>
            <p id="page-body-hint" class="mt-1.5 text-xs text-slate-500">
                Les sauts de ligne sont conservés tels quels.
            </p>
        </div>

        <div>
            <span class="block text-sm font-medium text-slate-700">Image de bannière</span>

            <label
                for="page-image"
                class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-5 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
            >
                <x-icon name="image" class="h-6 w-6 text-primary-500" />
                <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour importer une image</span>
                <span class="mt-1 text-xs text-slate-400">(JPG, PNG - Max 2MB)</span>

                <input
                    type="file"
                    id="page-image"
                    name="image"
                    accept="image/jpeg,image/png,image/webp"
                    class="sr-only"
                >
            </label>
        </div>

        <div>
            <label for="page-seo-title" class="block text-sm font-medium text-slate-700">Titre SEO</label>
            <input
                type="text"
                id="page-seo-title"
                name="seo_title"
                maxlength="70"
                aria-describedby="page-seo-hint"
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
            <p id="page-seo-hint" class="mt-1.5 text-xs text-slate-500">
                Laissez vide pour utiliser le titre de la page.
            </p>
        </div>

        <div>
            <label for="page-seo-description" class="block text-sm font-medium text-slate-700">Description SEO</label>
            <textarea
                id="page-seo-description"
                name="seo_description"
                rows="2"
                maxlength="300"
                class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            ></textarea>
        </div>

        <div>
            <label for="page-status" class="block text-sm font-medium text-slate-700">
                Statut
                <span class="text-rose-600" aria-hidden="true">*</span>
                <span class="sr-only">(obligatoire)</span>
            </label>
            <select
                id="page-status"
                name="status"
                required
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
                @php
                    $options = ['published' => 'Publié', 'draft' => 'Brouillon', 'archived' => 'Archivé'];
                @endphp
                @foreach ($options as $value => $label)
                    <option value="{{ $value }}" @selected($value === 'draft')>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="page-published-at" class="block text-sm font-medium text-slate-700">Date de publication</label>
            <input
                type="date"
                id="page-published-at"
                name="published_at"
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
        </div>
    </x-admin.edit-modal>

@endsection
