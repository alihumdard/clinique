@extends('layouts.registre')

@section('title', 'Dossiers')

@section('content')

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Dossiers</h1>
            <p class="mt-1.5 text-sm text-slate-600">
                {{ $counts['total'] }} au total &middot; {{ $counts['open'] }} ouverts &middot;
                <span class="{{ $counts['overdue'] ? 'font-semibold text-rose-700' : '' }}">{{ $counts['overdue'] }} en retard</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a
                href="{{ route('registre.dossiers.export', request()->query()) }}"
                class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                Exporter (CSV)
            </a>

            <a
                href="{{ route('registre.dossiers.create') }}"
                class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800"
            >
                Nouveau dossier
            </a>
        </div>
    </div>

    {{-- ============ RECHERCHE ET FILTRES ============ --}}
    <form method="GET" action="{{ route('registre.dossiers') }}" class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <label for="q" class="sr-only">Rechercher</label>
            <input
                type="search"
                id="q"
                name="q"
                value="{{ $filters['q'] ?? '' }}"
                placeholder="Référence, demandeur, téléphone, objet…"
                class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            >
        </div>

        <div>
            <label for="statut" class="sr-only">Statut</label>
            <select id="statut" name="statut" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                <option value="">Tous les statuts</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}" @selected(($filters['statut'] ?? null) == $status->id)>{{ $status->label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="urgence" class="sr-only">Urgence</label>
            <select id="urgence" name="urgence" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                <option value="">Toutes urgences</option>
                @foreach (['normale' => 'Normale', 'elevee' => 'Élevée', 'urgente' => 'Urgente'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['urgence'] ?? null) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-3">
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="retard" value="1" @checked($filters['retard'] ?? false) class="h-4 w-4 rounded border-slate-300 text-slate-700 focus:ring-slate-500">
                En retard
            </label>

            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                Filtrer
            </button>
        </div>
    </form>

    {{-- ============ LISTE ============ --}}
    <div class="mt-5 overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="whitespace-nowrap border-b border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th scope="col" class="px-4 py-3">Référence</th>
                        <th scope="col" class="px-4 py-3">Demandeur</th>
                        <th scope="col" class="px-4 py-3">Objet</th>
                        <th scope="col" class="px-4 py-3">Ouvert le</th>
                        <th scope="col" class="px-4 py-3">Statut</th>
                        <th scope="col" class="px-4 py-3">Urgence</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($dossiers as $dossier)
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3">
                                <a href="{{ route('registre.dossiers.show', $dossier) }}" class="font-semibold text-slate-900 hover:text-primary-600">
                                    {{ $dossier->reference }}
                                </a>
                                @if($dossier->source === \App\Models\Registre\Dossier::SOURCE_ONLINE)
                                    <span class="ml-1.5 text-xs text-slate-400">en ligne</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="block font-medium text-slate-900">{{ $dossier->applicant?->fullName() }}</span>
                                <span class="block text-xs text-slate-500">{{ $dossier->applicant?->phone }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="block max-w-xs truncate text-slate-700">{{ $dossier->subject }}</span>
                                <span class="block text-xs text-slate-500">{{ $dossier->legal_domain }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                {{ $dossier->opened_at?->translatedFormat('j M Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block whitespace-nowrap rounded bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                    {{ $dossier->status?->label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $tone = match ($dossier->urgency) {
                                        'urgente' => 'bg-rose-50 text-rose-700',
                                        'elevee' => 'bg-amber-50 text-amber-700',
                                        default => 'bg-slate-100 text-slate-600',
                                    };
                                @endphp
                                <span class="inline-block whitespace-nowrap rounded px-2.5 py-1 text-xs font-medium {{ $tone }}">
                                    {{ ucfirst($dossier->urgency) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                Aucun dossier ne correspond à cette recherche.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($dossiers->hasPages())
        <div class="mt-5">{{ $dossiers->links() }}</div>
    @endif

@endsection
