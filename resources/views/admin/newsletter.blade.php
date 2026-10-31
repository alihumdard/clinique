@extends('layouts.admin')

@section('title', 'Newsletter')
@section('search_action', route('admin.newsletter'))
@section('search_placeholder', 'Rechercher un abonné, une adresse e-mail...')

@section('content')

    <x-admin.flash />

    <x-admin.page-header
        title="Newsletter"
        lead="Gérez la liste des abonnés et exportez-la pour vos envois."
        action-label="Exporter en CSV"
        :action-href="route('admin.newsletter.export')"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Newsletter'],
        ]"
    />

    {{--
        The envois themselves are not sent from here. Phase 2 collects the
        addresses and hands them over as a file: there is no campaigns
        table, no mail transport and no delivery statistics behind this
        screen, and showing an open rate it cannot measure would be worse
        than not showing one.
    --}}
    <div class="mt-6 flex items-start gap-3 rounded-xl border border-primary-100 bg-primary-50/60 p-4">
        <x-icon name="info" class="mt-0.5 h-5 w-5 shrink-0 text-primary-600" />
        <p class="text-sm leading-relaxed text-slate-700">
            Les abonnés sont collectés depuis la page Actualités. Exportez la liste en CSV
            pour l'importer dans votre outil d'envoi. Un abonné désinscrit reste enregistré
            comme tel afin qu'un import ultérieur ne le réinscrive pas.
        </p>
    </div>

    <div class="mt-5 space-y-5">

        {{-- ============ KPIs ============ --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-admin.stat-card icon="users"   color="green" :value="number_format($counts['active'], 0, ',', ' ')" label="Abonnés actifs" note="recevront vos envois" keep-at-zero />
            <x-admin.stat-card icon="mail"    color="blue"  :value="$counts['month']" label="Nouveaux ce mois-ci" />
            <x-admin.stat-card icon="eye-off" color="amber" :value="$counts['unsubscribed']" label="Désinscrits" note="à ne pas réimporter" />
            <x-admin.stat-card icon="users"   color="red"   :value="number_format($counts['all'], 0, ',', ' ')" label="Total enregistré" />
        </div>

        {{-- ============ TABLE ============ --}}
        <section class="rounded-xl border border-slate-200 bg-white">

            @php
                $statut = $filters['statut'] ?? null;

                $tabLink = fn (array $extra) => route('admin.newsletter', array_merge(
                    array_filter(['q' => $filters['q'] ?? null]),
                    $extra
                ));
            @endphp

            <div class="flex flex-col gap-4 p-5 lg:flex-row lg:items-center lg:justify-between">
                <x-admin.filter-tabs
                    class="lg:border-b-0"
                    :tabs="[
                        ['label' => 'Tous', 'count' => $counts['all'], 'active' => ! $statut, 'href' => $tabLink([])],
                        ['label' => 'Actifs', 'count' => $counts['active'], 'active' => $statut === 'actifs', 'href' => $tabLink(['statut' => 'actifs'])],
                        ['label' => 'Désinscrits', 'count' => $counts['unsubscribed'], 'active' => $statut === 'desinscrits', 'href' => $tabLink(['statut' => 'desinscrits'])],
                    ]"
                />

                <form method="GET" action="{{ route('admin.newsletter') }}" class="flex shrink-0 flex-wrap items-center gap-3">
                    @if($statut)
                        <input type="hidden" name="statut" value="{{ $statut }}">
                    @endif

                    <div role="search" class="relative w-full min-w-[12rem] flex-1 sm:w-64">
                        <label for="newsletter-search" class="sr-only">Rechercher un abonné</label>
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input
                            type="search"
                            id="newsletter-search"
                            name="q"
                            value="{{ $filters['q'] ?? '' }}"
                            placeholder="Rechercher une adresse..."
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <button type="submit" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50">
                        <x-icon name="search" class="h-4 w-4" />
                        Rechercher
                    </button>

                    @if (array_filter($filters))
                        <a href="{{ route('admin.newsletter') }}" class="shrink-0 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-500 transition-colors hover:bg-slate-50">
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
                <table class="w-full min-w-[48rem] text-left">
                    <thead>
                        <tr class="whitespace-nowrap border-y border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th scope="col" class="w-12 px-5 py-3">#</th>
                            <th scope="col" class="min-w-[11rem] px-3 py-3">Adresse e-mail</th>
                            <th scope="col" class="px-3 py-3">Nom</th>
                            <th scope="col" class="px-3 py-3">Inscrit le</th>
                            <th scope="col" class="px-3 py-3">Statut</th>
                            <th scope="col" class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($subscribers as $subscriber)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 text-sm text-slate-500">
                                    {{ $loop->iteration + ($subscribers->firstItem() - 1) }}
                                </td>

                                <td class="px-3 py-3">
                                    <span class="block text-sm font-medium text-slate-900">{{ $subscriber->email }}</span>
                                </td>

                                <td class="px-3 py-3 text-sm text-slate-600">
                                    {{ $subscriber->name ?? '—' }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-3 text-sm leading-tight text-slate-500">
                                    {{ $subscriber->created_at->translatedFormat('j F Y') }}<br>
                                    <span class="text-xs">{{ $subscriber->created_at->format('H:i') }}</span>
                                </td>

                                <td class="px-3 py-3">
                                    @if($subscriber->isSubscribed())
                                        <x-admin.badge tone="green">Actif</x-admin.badge>
                                    @else
                                        <x-admin.badge tone="slate">
                                            Désinscrit le {{ $subscriber->unsubscribed_at->format('d/m/Y') }}
                                        </x-admin.badge>
                                    @endif
                                </td>

                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        @if($subscriber->isSubscribed())
                                            <form method="POST" action="{{ route('admin.newsletter.unsubscribe', $subscriber) }}" class="contents">
                                                @csrf
                                                <button type="submit" class="relative flex h-8 w-8 items-center justify-center rounded-md text-slate-500 transition-colors hover:bg-slate-100">
                                                    <x-icon name="eye-off" class="h-4 w-4" />
                                                    <span class="sr-only">Désinscrire : {{ $subscriber->email }}</span>
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.newsletter.destroy', $subscriber) }}" class="contents">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="relative flex h-8 w-8 items-center justify-center rounded-md text-rose-600 transition-colors hover:bg-rose-50">
                                                <x-icon name="trash" class="h-4 w-4" />
                                                <span class="sr-only">Supprimer définitivement : {{ $subscriber->email }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <p class="text-sm text-slate-500">
                                        @if (array_filter($filters))
                                            Aucun abonné ne correspond à cette recherche.
                                        @else
                                            Aucun abonné pour le moment. Le formulaire d'inscription se trouve sur la page Actualités.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-admin.table-footer :paginator="$subscribers" unit="abonnés" />
        </section>
    </div>

@endsection
