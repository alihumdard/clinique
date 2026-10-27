@extends('layouts.admin')

@section('title', 'Messages de contact')
@section('search_placeholder', 'Rechercher un message, un nom, une adresse...')

@section('content')

    <x-admin.flash />

    <x-admin.page-header
        title="Messages de contact"
        lead="Messages reçus via le formulaire de contact du site."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Messages de contact'],
        ]"
    />

    {{--
        These rows hold personal data a visitor typed in. There is no
        corbeille: a deletion is permanent, which is the deletion the
        retention rules ask for. Mail notification is not wired — no SMTP
        credentials have been supplied for this deployment — so this screen
        is where the messages arrive.
    --}}
    <div class="mt-6 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
        <x-icon name="info" class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" />
        <p class="text-sm leading-relaxed text-slate-700">
            Ces messages contiennent des données personnelles. La suppression est définitive
            (pas de corbeille). L'envoi d'une notification par e-mail sera activé dès que les
            paramètres SMTP de la Clinique seront fournis.
        </p>
    </div>

    <div class="mt-5 space-y-5">

        {{-- ============ KPIs ============ --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-admin.stat-card icon="chat"         color="blue"  :value="$counts['new']"     label="Nouveaux messages" note="à traiter" />
            <x-admin.stat-card icon="check-circle" color="green" :value="$counts['handled']" label="Résolus" />
            <x-admin.stat-card icon="clock"        color="amber" :value="$counts['week']"    label="Reçus cette semaine" />
            <x-admin.stat-card icon="mail"         color="red"   :value="$counts['all']"     label="Total enregistré" />
        </div>

        {{-- ============ LIST ============ --}}
        <section class="rounded-xl border border-slate-200 bg-white">

            @php
                $statut = $filters['statut'] ?? null;

                $tabLink = fn (array $extra) => route('admin.messages', array_merge(
                    array_filter([
                        'q' => $filters['q'] ?? null,
                        'sujet' => $filters['sujet'] ?? null,
                    ]),
                    $extra
                ));
            @endphp

            <div class="flex flex-col gap-4 p-5 lg:flex-row lg:items-center lg:justify-between">
                <x-admin.filter-tabs
                    class="lg:border-b-0"
                    :tabs="[
                        ['label' => 'Tous', 'count' => $counts['all'], 'active' => ! $statut, 'href' => $tabLink([])],
                        ['label' => 'Nouveaux', 'count' => $counts['new'], 'active' => $statut === 'nouveaux', 'href' => $tabLink(['statut' => 'nouveaux'])],
                        ['label' => 'Résolus', 'count' => $counts['handled'], 'active' => $statut === 'traites', 'href' => $tabLink(['statut' => 'traites'])],
                    ]"
                />

                <form method="GET" action="{{ route('admin.messages') }}" class="flex shrink-0 flex-wrap items-center gap-3">
                    @if($statut)
                        <input type="hidden" name="statut" value="{{ $statut }}">
                    @endif

                    <div role="search" class="relative w-full min-w-[12rem] flex-1 sm:w-56">
                        <label for="messages-search" class="sr-only">Rechercher un message</label>
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input
                            type="search"
                            id="messages-search"
                            name="q"
                            value="{{ $filters['q'] ?? '' }}"
                            placeholder="Nom, adresse, contenu..."
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div class="min-w-0 shrink-0">
                        <label for="messages-subject" class="sr-only">Filtrer par sujet</label>
                        <select
                            id="messages-subject"
                            name="sujet"
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-3.5 pr-9 text-sm text-slate-600 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-auto"
                        >
                            <option value="">Tous les sujets</option>
                            @foreach ($subjects as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['sujet'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50">
                        <x-icon name="filter" class="h-4 w-4" />
                        Filtrer
                    </button>

                    @if (array_filter($filters))
                        <a href="{{ route('admin.messages') }}" class="shrink-0 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-500 transition-colors hover:bg-slate-50">
                            Réinitialiser
                        </a>
                    @endif
                </form>
            </div>

            {{--
                A list of cards rather than a table: a message is a
                paragraph, and a paragraph in a table cell is unreadable at
                any width.
            --}}
            <div class="divide-y divide-slate-100 border-t border-slate-100">
                @forelse ($messages as $message)
                    <article class="p-5">
                        {{-- flex-nowrap + a horizontal scroller on narrow
                             screens, rather than flex-wrap: wrapping pushed
                             "Marquer résolu"/"Supprimer" onto their own row
                             below the name, which read as if they belonged
                             to the message body rather than this header. A
                             name/subject/badges block this long doesn't fit
                             a phone either way, so it scrolls sideways
                             instead of breaking the header's shape. --}}
                        <div class="-mx-5 overflow-x-auto px-5 pb-1 sm:mx-0 sm:overflow-visible sm:px-0 sm:pb-0">
                            <div class="flex min-w-max flex-nowrap items-start justify-between gap-3 sm:min-w-0 sm:flex-wrap">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-sm font-bold text-slate-900">{{ $message->name }}</h2>

                                        <x-admin.badge tone="blue">
                                            {{ $subjects[$message->subject] ?? $message->subject }}
                                        </x-admin.badge>

                                        @if($message->status === \App\Models\ContactMessage::STATUS_NEW)
                                            <x-admin.badge tone="amber">Nouveau</x-admin.badge>
                                        @else
                                            <x-admin.badge tone="green">Résolu</x-admin.badge>
                                        @endif
                                    </div>

                                    <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                                        <a href="mailto:{{ $message->email }}" class="inline-flex items-center gap-1.5 text-primary-600 hover:text-primary-700">
                                            <x-icon name="mail" class="h-3.5 w-3.5 shrink-0" />
                                            {{ $message->email }}
                                        </a>

                                        @if($message->phone)
                                            <a href="tel:{{ $message->phone }}" class="inline-flex items-center gap-1.5">
                                                <x-icon name="phone" class="h-3.5 w-3.5 shrink-0" />
                                                {{ $message->phone }}
                                            </a>
                                        @endif

                                        <span class="inline-flex items-center gap-1.5">
                                            <x-icon name="calendar" class="h-3.5 w-3.5 shrink-0" />
                                            {{ $message->created_at->translatedFormat('j F Y à H:i') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex shrink-0 items-center gap-1">
                                    @if($message->status === \App\Models\ContactMessage::STATUS_NEW)
                                        <form method="POST" action="{{ route('admin.messages.handle', $message) }}" class="contents">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-md border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition-colors hover:bg-emerald-100">
                                                <x-icon name="check-circle" class="h-4 w-4" />
                                                Marquer résolu
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" class="contents">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="relative flex h-8 w-8 items-center justify-center rounded-md text-rose-600 transition-colors hover:bg-rose-50">
                                            <x-icon name="trash" class="h-4 w-4" />
                                            <span class="sr-only">Supprimer définitivement le message de {{ $message->name }}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $message->message }}</p>

                        <p class="mt-3 text-xs text-slate-400">
                            Consentement donné le {{ $message->consent_at?->translatedFormat('j F Y à H:i') ?? '—' }}
                            @if($message->handled_at)
                                &middot; résolu le {{ $message->handled_at->translatedFormat('j F Y') }}
                                @if($message->handler) par {{ $message->handler->name }} @endif
                            @endif
                        </p>
                    </article>
                @empty
                    <div class="p-12 text-center">
                        <p class="text-sm text-slate-500">
                            @if (array_filter($filters))
                                Aucun message ne correspond à cette recherche.
                            @else
                                Aucun message pour le moment.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

            <x-admin.table-footer :paginator="$messages" unit="messages" />
        </section>
    </div>

@endsection
