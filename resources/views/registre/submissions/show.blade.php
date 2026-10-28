@extends('layouts.registre')

@section('title', 'Demande ' . $submission->reference)

@section('content')

    <p class="text-sm text-slate-500">
        <a href="{{ route('registre.submissions') }}" class="hover:text-slate-700">Demandes en ligne</a>
        <span aria-hidden="true">&rsaquo;</span>
        <span class="text-slate-700">{{ $submission->reference }}</span>
    </p>

    <div class="mt-3 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $submission->subject }}</h1>
            <p class="mt-1.5 text-sm text-slate-600">
                {{ $submission->reference }} &middot; reçue le {{ $submission->created_at->translatedFormat('j F Y à H:i') }}
            </p>
        </div>

        @php
            $tone = match ($submission->status) {
                \App\Models\Registre\PresaisineSubmission::STATUS_ACCEPTED => 'bg-emerald-50 text-emerald-700',
                \App\Models\Registre\PresaisineSubmission::STATUS_REFUSED => 'bg-slate-100 text-slate-600',
                default => 'bg-amber-50 text-amber-700',
            };
        @endphp
        <span class="rounded px-3 py-1.5 text-sm font-medium {{ $tone }}">{{ $submission->statusLabel() }}</span>
    </div>

    @if($submission->dossier)
        <p class="mt-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
            Cette demande a ouvert le dossier <strong>{{ $submission->dossier->reference }}</strong>
            le {{ $submission->reviewed_at?->translatedFormat('j F Y') }}.
        </p>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-3">

        {{-- ============ LA DEMANDE ============ --}}
        <div class="space-y-5 lg:col-span-2">

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-bold text-slate-900">Exposé de la situation</h2>
                <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-700">
                    {{ $submission->narrative ?: 'Aucun exposé fourni.' }}
                </p>
            </div>

            @php $questions = array_filter([$submission->question_1, $submission->question_2, $submission->question_3]); @endphp

            @if(count($questions))
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <h2 class="text-sm font-bold text-slate-900">Questions posées</h2>
                    <ol class="mt-3 list-inside list-decimal space-y-1.5 text-sm leading-relaxed text-slate-700">
                        @foreach ($questions as $question)
                            <li>{{ $question }}</li>
                        @endforeach
                    </ol>
                </div>
            @endif

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-bold text-slate-900">Pièces jointes</h2>

                @forelse ($submission->attachments as $attachment)
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-100 px-3.5 py-2.5">
                        <span class="flex min-w-0 items-center gap-2 text-sm text-slate-700">
                            <x-icon name="document" class="h-4 w-4 shrink-0 text-slate-400" />
                            <span class="truncate">{{ $attachment->original_name }}</span>
                            <span class="shrink-0 text-xs text-slate-400">{{ $attachment->humanSize() }}</span>
                        </span>

                        @if($attachment->isPurged())
                            {{-- "Supprimer du serveur de réception après transfert." --}}
                            <span class="shrink-0 text-xs text-slate-400">
                                Transférée au dossier le {{ $attachment->purged_at->translatedFormat('j M Y') }}
                            </span>
                        @else
                            <a
                                href="{{ route('registre.submissions.attachment', [$submission, $attachment]) }}"
                                class="shrink-0 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                            >
                                Télécharger
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="mt-3 text-sm text-slate-500">Aucune pièce jointe.</p>
                @endforelse
            </div>

            {{-- ============ DÉCISION ============ --}}
            @if($submission->status === \App\Models\Registre\PresaisineSubmission::STATUS_PENDING)
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <h2 class="text-sm font-bold text-slate-900">Décision</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                        Accepter ouvre un dossier et y transfère les pièces jointes, qui sont
                        ensuite supprimées du serveur de réception. Refuser supprime les pièces
                        jointes sans ouvrir de dossier.
                    </p>

                    <div class="mt-4 flex flex-wrap items-start gap-3">
                        <form method="POST" action="{{ route('registre.submissions.accept', $submission) }}">
                            @csrf
                            <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                                Accepter et ouvrir un dossier
                            </button>
                        </form>

                        <form method="POST" action="{{ route('registre.submissions.refuse', $submission) }}" class="flex flex-wrap items-start gap-2">
                            @csrf
                            <label for="note" class="sr-only">Motif du refus</label>
                            <input
                                type="text"
                                id="note"
                                name="note"
                                placeholder="Motif du refus (facultatif)"
                                class="w-64 max-w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                            <button type="submit" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                                Refuser
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($submission->review_note)
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <h2 class="text-sm font-bold text-slate-900">Motif du refus</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-700">{{ $submission->review_note }}</p>
                </div>
            @endif
        </div>

        {{-- ============ LE DEMANDEUR ============ --}}
        <aside class="space-y-5">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-bold text-slate-900">Demandeur</h2>

                @php
                    $lines = [
                        'Nom' => $submission->fullName(),
                        'Téléphone' => $submission->phone,
                        'E-mail' => $submission->email,
                        'Commune' => $submission->commune,
                        'Étudiant UAM' => $submission->is_uam_student ? ($submission->student_card_number ?: 'Oui') : 'Non',
                        'Domaine' => $submission->legal_domain,
                    ];
                @endphp

                <dl class="mt-3 space-y-2.5 text-sm">
                    @foreach ($lines as $label => $value)
                        @continue(blank($value))
                        <div>
                            <dt class="text-xs text-slate-500">{{ $label }}</dt>
                            <dd class="font-medium text-slate-900">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-bold text-slate-900">Consentement</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    Accord donné le {{ $submission->consent_at->translatedFormat('j F Y à H:i') }}.
                </p>
            </div>

            @if($submission->orientation)
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <h2 class="text-sm font-bold text-slate-900">Orientation</h2>
                    <dl class="mt-3 space-y-2 text-sm">
                        @foreach ($submission->orientation as $key => $answer)
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                                <dd class="font-medium text-slate-900">{{ ucfirst($answer) }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif
        </aside>
    </div>

@endsection
