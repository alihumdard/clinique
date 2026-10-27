@extends('layouts.registre')

@section('title', 'Demandes en ligne')

@section('content')

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Demandes en ligne</h1>
            <p class="mt-1.5 text-sm text-slate-600">
                Les pré-saisines déposées sur le site public. Une demande acceptée ouvre un dossier.
            </p>
        </div>
    </div>

    @php
        $tabs = ['' => 'Toutes'] + \App\Models\Registre\PresaisineSubmission::statuses();
    @endphp

    <div class="mt-6 flex flex-wrap gap-1.5">
        @foreach ($tabs as $value => $label)
            @php $active = (string) $filter === (string) $value; @endphp
            <a
                href="{{ route('registre.submissions', $value ? ['statut' => $value] : []) }}"
                class="rounded-full border px-3.5 py-1.5 text-sm font-medium transition {{ $active ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300' }}"
            >
                {{ $label }}
                @if($value && ($counts[$value] ?? 0))
                    <span class="ml-1 text-xs {{ $active ? 'text-slate-300' : 'text-slate-400' }}">{{ $counts[$value] }}</span>
                @endif
            </a>
        @endforeach
    </div>

    <div class="mt-5 overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="whitespace-nowrap border-b border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th scope="col" class="px-4 py-3">Référence</th>
                        <th scope="col" class="px-4 py-3">Demandeur</th>
                        <th scope="col" class="px-4 py-3">Domaine</th>
                        <th scope="col" class="px-4 py-3">Reçue le</th>
                        <th scope="col" class="px-4 py-3">Pièces</th>
                        <th scope="col" class="px-4 py-3">Statut</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($submissions as $submission)
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3">
                                <a href="{{ route('registre.submissions.show', $submission) }}" class="font-semibold text-slate-900 hover:text-primary-600">
                                    {{ $submission->reference }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <span class="block font-medium text-slate-900">{{ $submission->fullName() }}</span>
                                <span class="block text-xs text-slate-500">{{ $submission->phone }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $submission->legal_domain }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                {{ $submission->created_at->translatedFormat('j M Y, H:i') }}
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $submission->attachments_count }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $tone = match ($submission->status) {
                                        \App\Models\Registre\PresaisineSubmission::STATUS_ACCEPTED => 'bg-emerald-50 text-emerald-700',
                                        \App\Models\Registre\PresaisineSubmission::STATUS_REFUSED => 'bg-slate-100 text-slate-600',
                                        default => 'bg-amber-50 text-amber-700',
                                    };
                                @endphp
                                <span class="inline-block whitespace-nowrap rounded px-2.5 py-1 text-xs font-medium {{ $tone }}">
                                    {{ $submission->statusLabel() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                Aucune demande pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($submissions->hasPages())
        <div class="mt-5">{{ $submissions->links() }}</div>
    @endif

@endsection
