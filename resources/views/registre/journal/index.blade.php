@extends('layouts.registre')

@section('title', "Journal d'accès")

@section('content')

    <h1 class="text-2xl font-bold text-slate-900">Journal d'accès</h1>
    <p class="mt-1.5 text-sm text-slate-600">
        Les consultations, téléchargements, décisions et exports du registre. Ce journal est
        en lecture seule : rien dans l'application ne modifie ni ne supprime une ligne.
    </p>

    <form method="GET" action="{{ route('registre.journal') }}" class="mt-6 flex flex-wrap items-end gap-3">
        <div>
            <label for="action" class="block text-xs font-semibold text-slate-600">Action</label>
            <select id="action" name="action" class="mt-1 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                <option value="">Toutes</option>
                @foreach ($actions as $action)
                    <option value="{{ $action }}" @selected(($filters['action'] ?? null) === $action)>{{ $action }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="compte" class="block text-xs font-semibold text-slate-600">Compte</label>
            <select id="compte" name="compte" class="mt-1 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                <option value="">Tous</option>
                @foreach ($agents as $agent)
                    <option value="{{ $agent->id }}" @selected(($filters['compte'] ?? null) == $agent->id)>{{ $agent->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Filtrer
        </button>
    </form>

    <div class="mt-5 overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="whitespace-nowrap border-b border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th scope="col" class="px-4 py-3">Quand</th>
                        <th scope="col" class="px-4 py-3">Compte</th>
                        <th scope="col" class="px-4 py-3">Action</th>
                        <th scope="col" class="px-4 py-3">Objet</th>
                        <th scope="col" class="px-4 py-3">Adresse IP</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($entries as $entry)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                {{ $entry->created_at?->translatedFormat('j M Y, H:i:s') }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $entry->user?->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">{{ $entry->action }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $entry->context['reference'] ?? $entry->context['dossier'] ?? $entry->context['submission'] ?? ($entry->subject_type ? $entry->subject_type.' #'.$entry->subject_id : '—') }}
                                @if($entry->context['file'] ?? null)
                                    <span class="block text-xs text-slate-400">{{ $entry->context['file'] }}</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-xs text-slate-500">{{ $entry->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">
                                Aucune entrée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($entries->hasPages())
        <div class="mt-5">{{ $entries->links() }}</div>
    @endif

@endsection
