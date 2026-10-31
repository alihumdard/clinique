@extends('layouts.admin')

@section('title', 'Gestion de la Web TV')
@section('search_action', route('admin.webtv'))
@section('search_placeholder', 'Rechercher une vidéo, un titre, une catégorie...')

@section('content')

    <x-admin.flash />

    <x-admin.page-header
        title="Gestion de la Web TV"
        lead="Ajoutez, modifiez et organisez toutes les capsules vidéo de la Clinique Juridique FSJP-UAM."
        action-label="Ajouter une vidéo"
        action-dialog="create-video"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Web TV'],
        ]"
    />

    <div class="mt-6 space-y-5">

        <div class="space-y-5">

            {{-- ============ KPIs ============ --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card icon="play-circle" color="blue"  :value="$counts['published']" label="Vidéos publiées" />
                <x-admin.stat-card icon="eye"         color="green" :value="number_format($counts['views'], 0, ',', ' ')" label="Vues totales" />
                <x-admin.stat-card icon="clock"       color="amber" :value="$counts['drafts']"    label="Vidéos en brouillon" note="Non publiées" />
                <x-admin.stat-card icon="trash"       color="red"   :value="$counts['trashed']"   :href="route('admin.webtv', ['statut' => 'corbeille'])"
                                   label="À la corbeille" />
            </div>

            {{-- ============ TABLE ============ --}}
            <section class="rounded-xl border border-slate-200 bg-white">

                {{-- One GET form: search, rubrique and statut submit together,
                     so a filtered list is a URL that can be shared. --}}
                <form method="GET" action="{{ route('admin.webtv') }}" class="space-y-3 p-5">
                    <div role="search" class="relative w-full lg:max-w-xs">
                        <label for="webtv-search" class="sr-only">Rechercher une vidéo</label>
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input
                            type="search"
                            id="webtv-search"
                            name="q"
                            value="{{ $filters['q'] ?? '' }}"
                            placeholder="Rechercher une vidéo..."
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
                            <label for="webtv-category" class="sr-only">Filtrer par rubrique</label>
                            <select
                                id="webtv-category"
                                name="categorie"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-3.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-auto"
                            >
                                <option value="">Toutes les rubriques</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) ($filters['categorie'] ?? 0) === $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="min-w-0 sm:shrink-0">
                            <label for="webtv-status" class="sr-only">Filtrer par statut</label>
                            <select
                                id="webtv-status"
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
                            <a href="{{ route('admin.webtv') }}" class="col-span-2 inline-flex w-full shrink-0 items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-500 transition-colors hover:bg-slate-50 sm:col-span-1 sm:w-auto">
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
                                        aria-label="Sélectionner toutes les vidéos"
                                        class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                    >
                                </th>
                                <th scope="col" class="w-12 px-2 py-3">#</th>
                                <th scope="col" class="w-24 px-2 py-3">Miniature</th>
                                <th scope="col" class="min-w-[11rem] px-3 py-3">Titre de la vidéo</th>
                                <th scope="col" class="px-3 py-3">Rubrique</th>
                                <th scope="col" class="px-3 py-3">Durée</th>
                                <th scope="col" class="px-3 py-3">Vues</th>
                                <th scope="col" class="px-3 py-3">Date</th>
                                <th scope="col" class="px-3 py-3">Statut</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($videos as $video)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <input
                                            type="checkbox"
                                            aria-label="Sélectionner : {{ $video->title }}"
                                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                    </td>

                                    <td class="px-2 py-3 text-sm text-slate-500">
                                        {{ $loop->iteration + ($videos->firstItem() - 1) }}
                                    </td>

                                    <td class="px-2 py-3">
                                        {{-- Duration sits on the thumbnail, as it does on the public site --}}
                                        <span class="relative block w-16">
                                            <img
                                                src="{{ $video->coverUrl() }}"
                                                alt=""
                                                width="128" height="72"
                                                loading="lazy"
                                                class="h-11 w-16 rounded-md object-cover"
                                            >
                                            @if($video->durationLabel())
                                                <span class="absolute bottom-0.5 right-0.5 rounded bg-slate-900/80 px-1 text-[10px] font-medium text-white">
                                                    {{ $video->durationLabel() }}
                                                </span>
                                            @endif
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="block max-w-[14rem] text-sm font-semibold leading-snug text-slate-900">
                                            {{ $video->title }}
                                        </span>
                                        <span class="mt-0.5 block text-xs text-slate-400">
                                            {{ $video->provider === 'vimeo' ? 'Vimeo' : 'YouTube' }} &middot; {{ $video->provider_id }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3">
                                        @if($video->category)
                                            <x-admin.badge :tone="$video->category->tone">{{ $video->category->name }}</x-admin.badge>
                                        @else
                                            <span class="text-sm text-slate-400">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3 text-sm text-slate-500">
                                        {{ $video->durationLabel() ?? '—' }}
                                    </td>

                                    <td class="px-3 py-3 text-sm text-slate-500">
                                        {{ number_format($video->views, 0, ',', ' ') }}
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-3 text-sm leading-tight text-slate-500">
                                        @if($video->published_at)
                                            {{ $video->published_at->translatedFormat('j F Y') }}<br>
                                            <span class="text-xs">{{ $video->published_at->format('H:i') }}</span>
                                        @else
                                            <span class="text-slate-400">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3">
                                        <x-admin.badge :tone="$video->statusTone()">{{ $video->statusLabel() }}</x-admin.badge>
                                    </td>

                                    <td class="px-5 py-3">
                                        <x-admin.row-actions
                                            :label="$video->title"
                                            edit-dialog="edit-video-{{ $video->id }}"
                                            :view-href="$video->isPublic() ? route('webtv.show', $video) : null"
                                            :duplicate-action="route('admin.webtv.duplicate', $video)"
                                            :delete-action="route('admin.webtv.destroy', $video)"
                                            :restore-action="$video->trashed() ? route('admin.webtv.restore', $video->id) : null"
                                            :force-delete-action="$video->trashed() && auth()->user()->isAdmin() ? route('admin.webtv.force-destroy', $video->id) : null"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-5 py-12 text-center">
                                        <p class="text-sm text-slate-500">Aucune vidéo ne correspond à cette recherche.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-admin.table-footer :paginator="$videos" unit="vidéos" />
            </section>
        </div>

        {{--
            Every row's "Modifier" dialog is rendered here, outside the
            table above, instead of right after each row's closing tr tag
            inside tbody: a dialog element is not valid content between tr
            elements, and a browser that meets one there foster-parents it
            out while recovering from the parse, which was silently
            corrupting the form markup inside every dialog on this page
            without ever raising a console error.
        --}}
        @foreach ($videos as $video)
            <x-admin.edit-modal id="edit-video-{{ $video->id }}" title="Modifier la vidéo" :action="route('admin.webtv.update', $video)">
                <div>
                    <label for="edit-video-{{ $video->id }}-title" class="block text-sm font-medium text-slate-700">
                        Titre de la vidéo
                        <span class="text-rose-600" aria-hidden="true">*</span>
                    </label>
                    <input type="text" id="edit-video-{{ $video->id }}-title" name="title" required value="{{ $video->title }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>

                <div>
                    <label for="edit-video-{{ $video->id }}-category" class="block text-sm font-medium text-slate-700">Rubrique</label>
                    <select id="edit-video-{{ $video->id }}-category" name="category_id" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                        <option value="">Sélectionner une rubrique</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($video->category_id === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="edit-video-{{ $video->id }}-summary" class="block text-sm font-medium text-slate-700">Description</label>
                    <textarea id="edit-video-{{ $video->id }}-summary" name="summary" rows="3" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">{{ $video->summary }}</textarea>
                </div>

                <div>
                    <label for="edit-video-{{ $video->id }}-url" class="block text-sm font-medium text-slate-700">
                        Lien vidéo
                        <span class="text-rose-600" aria-hidden="true">*</span>
                    </label>
                    <input type="url" id="edit-video-{{ $video->id }}-url" name="video_url" required value="{{ $video->video_url }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="edit-video-{{ $video->id }}-duration" class="block text-sm font-medium text-slate-700">Durée (mm:ss)</label>
                        <input type="text" id="edit-video-{{ $video->id }}-duration" name="duration" value="{{ $video->duration }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>

                    <div>
                        <label for="edit-video-{{ $video->id }}-status" class="block text-sm font-medium text-slate-700">Statut</label>
                        <select id="edit-video-{{ $video->id }}-status" name="status" required class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                            <option value="draft" @selected($video->status === 'draft')>Brouillon</option>
                            <option value="published" @selected($video->status === 'published')>Publié</option>
                            <option value="archived" @selected($video->status === 'archived')>Archivé</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="edit-video-{{ $video->id }}-published-at" class="block text-sm font-medium text-slate-700">Date de publication</label>
                    <input type="date" id="edit-video-{{ $video->id }}-published-at" name="published_at" value="{{ $video->published_at?->format('Y-m-d') }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>

                <div>
                    <label for="edit-video-{{ $video->id }}-transcript" class="block text-sm font-medium text-slate-700">Transcription</label>
                    <textarea id="edit-video-{{ $video->id }}-transcript" name="transcript" rows="3" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">{{ $video->transcript }}</textarea>
                </div>

                <div class="pt-1">
                    <x-admin.toggle name="allow_comments" label="Autoriser les commentaires" :checked="(bool) $video->allow_comments" />
                </div>

                {{-- Each listing keeps its own thumbnail; the edit modal only ever
                     replaces this row's own cover, never a shared/static image. --}}
                <x-admin.image-field
                    name="cover"
                    :id="'edit-video-'.$video->id.'-cover'"
                    label="Image de couverture"
                    :current="$video->cover"
                    :preview="$video->coverUrl()"
                    hint="Sans image, la miniature de la chaîne est utilisée."
                />
            </x-admin.edit-modal>
        @endforeach

        {{-- The "Ajouter" dialog itself; not visually positioned, so it
             doesn't need a sidebar column to sit in. --}}
        <x-admin.edit-modal id="create-video" title="Ajouter une vidéo" :action="route('admin.webtv.store')" method="POST" submit-label="Publier la vidéo">
                <div>
                    <label for="create-video-title" class="block text-sm font-medium text-slate-700">
                        Titre de la vidéo
                        <span class="text-rose-600" aria-hidden="true">*</span>
                        <span class="sr-only">(obligatoire)</span>
                    </label>
                    <input
                        type="text"
                        id="create-video-title"
                        name="title"
                        required
                        placeholder="Saisissez le titre..."
                        class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    >
                </div>

                <div>
                    <label for="create-video-category" class="block text-sm font-medium text-slate-700">Rubrique</label>
                    <select
                        id="create-video-category"
                        name="category_id"
                        class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    >
                        <option value="">Sélectionner une rubrique</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="create-video-description" class="block text-sm font-medium text-slate-700">Description</label>
                    <textarea
                        id="create-video-description"
                        name="summary"
                        rows="4"
                        placeholder="Décrivez brièvement la vidéo..."
                        class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    ></textarea>
                </div>

                <div>
                    <label for="create-video-link" class="block text-sm font-medium text-slate-700">
                        Lien vidéo
                        <span class="text-rose-600" aria-hidden="true">*</span>
                        <span class="sr-only">(obligatoire)</span>
                    </label>
                    <div class="relative mt-1.5">
                        <input
                            type="url"
                            id="create-video-link"
                            name="video_url"
                            required
                            aria-describedby="create-video-link-hint"
                            placeholder="https://youtu.be/..."
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-3.5 pr-10 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                        <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-icon name="link" class="h-4 w-4" />
                        </span>
                    </div>
                    <p id="create-video-link-hint" class="mt-1.5 text-xs text-slate-500">
                        Collez simplement le lien YouTube ou Vimeo : la vidéo reste hébergée sur la chaîne de la Clinique.
                    </p>
                </div>

                <div>
                    <span class="block text-sm font-medium text-slate-700">Image de couverture</span>

                    <label
                        for="create-video-cover"
                        class="mt-1.5 flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-slate-200 px-4 py-6 text-center transition-colors hover:border-primary-300 hover:bg-primary-50/40"
                    >
                        <x-icon name="upload" class="h-7 w-7 text-primary-500" />
                        <span class="mt-2 text-sm font-medium text-slate-700">Cliquez pour télécharger</span>
                        <span class="mt-0.5 text-xs text-slate-500">ou glissez une image ici</span>
                        <span class="mt-1 text-xs text-slate-400">(JPG, PNG - Max 2MB)</span>

                        <input
                            type="file"
                            id="create-video-cover"
                            name="cover"
                            accept="image/jpeg,image/png,image/webp"
                            class="sr-only"
                        >
                    </label>
                    <p class="mt-1.5 text-xs text-slate-500">
                        Facultatif : sans image, la miniature de la chaîne est utilisée.
                    </p>
                </div>

                <div>
                    <label for="create-video-duration" class="block text-sm font-medium text-slate-700">Durée (mm:ss)</label>
                    <div class="relative mt-1.5">
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-icon name="clock" class="h-4 w-4" />
                        </span>
                        <input
                            type="text"
                            id="create-video-duration"
                            name="duration"
                            inputmode="numeric"
                            pattern="[0-9]{1,3}:[0-5][0-9]"
                            placeholder="Ex: 12:35"
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>
                </div>

                <div>
                    <label for="create-video-transcript" class="block text-sm font-medium text-slate-700">Transcription</label>
                    <textarea
                        id="create-video-transcript"
                        name="transcript"
                        rows="4"
                        placeholder="Collez ici la transcription écrite, lorsqu'elle existe..."
                        class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    ></textarea>
                    <p class="mt-1.5 text-xs text-slate-500">
                        Affichée sous la vidéo, pour l'accessibilité et le référencement.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="create-video-status" class="block text-sm font-medium text-slate-700">Statut</label>
                        <select id="create-video-status" name="status" required class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                            <option value="draft">Brouillon</option>
                            <option value="published" selected>Publié</option>
                            <option value="archived">Archivé</option>
                        </select>
                    </div>

                    <div>
                        <label for="create-video-published-at" class="block text-sm font-medium text-slate-700">Date de publication</label>
                        <input type="date" id="create-video-published-at" name="published_at" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>
                </div>

                <div class="pt-1">
                    <x-admin.toggle name="allow_comments" label="Autoriser les commentaires" :checked="false" />
                </div>
            </x-admin.edit-modal>
    </div>

@endsection
