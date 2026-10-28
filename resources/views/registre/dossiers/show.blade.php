@extends('layouts.registre')

@section('title', 'Dossier ' . $dossier->reference)

@section('content')

    <p class="text-sm text-slate-500">
        <a href="{{ route('registre.dossiers') }}" class="hover:text-slate-700">Dossiers</a>
        <span aria-hidden="true">&rsaquo;</span>
        <span class="text-slate-700">{{ $dossier->reference }}</span>
    </p>

    <div class="mt-3 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $dossier->subject }}</h1>
            <p class="mt-1.5 text-sm text-slate-600">
                {{ $dossier->reference }} &middot; {{ $dossier->legal_domain }} &middot;
                ouvert le {{ $dossier->opened_at?->translatedFormat('j F Y') }}
                @if($dossier->source === \App\Models\Registre\Dossier::SOURCE_ONLINE)
                    &middot; depuis la pré-saisine {{ $dossier->presaisine_reference }}
                @endif
            </p>
        </div>

        <span class="rounded bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700">
            {{ $dossier->status?->label }}
        </span>
    </div>

    @if($dossier->status?->is_provisional)
        {{-- The label on display is ours, not the Clinic's. Saying so on
             the dossier itself is the honest place for it. --}}
        <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Le libellé de ce statut est provisoire, et aucune transition entre statuts n'est
            encore déclarée : la liste approuvée des treize statuts est attendue de la Clinique.
        </p>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-3">

        <div class="space-y-5 lg:col-span-2">

            {{-- ============ LA DEMANDE ============ --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-bold text-slate-900">Exposé de la situation</h2>
                <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-700">
                    {{ $dossier->narrative ?: 'Aucun exposé enregistré.' }}
                </p>

                @php $questions = array_filter([$dossier->question_1, $dossier->question_2, $dossier->question_3]); @endphp

                @if(count($questions))
                    <h3 class="mt-5 text-sm font-bold text-slate-900">Questions posées</h3>
                    <ol class="mt-2 list-inside list-decimal space-y-1.5 text-sm leading-relaxed text-slate-700">
                        @foreach ($questions as $question)
                            <li>{{ $question }}</li>
                        @endforeach
                    </ol>
                @endif
            </div>

            {{-- ============ AFFECTATIONS ============ --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-bold text-slate-900">Affectations</h2>

                @forelse ($dossier->assignments->whereNull('ended_at') as $assignment)
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-100 px-3.5 py-2.5">
                        <span class="text-sm text-slate-700">
                            <span class="font-medium text-slate-900">{{ $assignment->user?->name }}</span>
                            <span class="text-slate-500">— {{ $assignment->role === 'encadrant' ? 'Encadrant' : 'Étudiant clinicien' }}</span>
                        </span>

                        <form method="POST" action="{{ route('registre.assignments.end', [$dossier, $assignment]) }}">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-slate-500 hover:text-rose-700">
                                Terminer
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="mt-3 text-sm text-slate-500">Personne n'est affecté à ce dossier.</p>
                @endforelse

                @php $ended = $dossier->assignments->whereNotNull('ended_at'); @endphp
                @if($ended->count())
                    <p class="mt-3 text-xs text-slate-400">
                        Affectations terminées :
                        {{ $ended->map(fn ($a) => $a->user?->name)->filter()->implode(', ') }}
                    </p>
                @endif

                <form method="POST" action="{{ route('registre.assignments.store', $dossier) }}" class="mt-4 flex flex-wrap items-end gap-2">
                    @csrf
                    <div>
                        <label for="user_id" class="block text-xs font-semibold text-slate-600">Affecter</label>
                        <select id="user_id" name="user_id" class="mt-1 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }} ({{ $agent->roleLabel() }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="role" class="block text-xs font-semibold text-slate-600">En tant que</label>
                        <select id="role" name="role" class="mt-1 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                            <option value="etudiant">Étudiant clinicien</option>
                            <option value="encadrant">Encadrant</option>
                        </select>
                    </div>

                    <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        Ajouter
                    </button>
                </form>
            </div>

            {{-- ============ ÉCHÉANCES ============ --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-bold text-slate-900">Échéances</h2>

                @forelse ($dossier->deadlines->sortBy('due_at') as $deadline)
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3 rounded-lg border px-3.5 py-2.5 {{ $deadline->isOverdue() ? 'border-rose-200 bg-rose-50' : 'border-slate-100' }}">
                        <span class="text-sm">
                            <span class="font-medium text-slate-900">{{ $deadline->label }}</span>
                            <span class="{{ $deadline->isOverdue() ? 'text-rose-700' : 'text-slate-500' }}">
                                — {{ $deadline->due_at->translatedFormat('j F Y') }}
                                @if($deadline->completed_at) (faite) @elseif($deadline->isOverdue()) (en retard) @endif
                            </span>
                        </span>

                        @unless($deadline->completed_at)
                            <form method="POST" action="{{ route('registre.deadlines.complete', [$dossier, $deadline]) }}">
                                @csrf
                                <button type="submit" class="text-xs font-semibold text-slate-500 hover:text-emerald-700">
                                    Marquer faite
                                </button>
                            </form>
                        @endunless
                    </div>
                @empty
                    <p class="mt-3 text-sm text-slate-500">Aucune échéance enregistrée.</p>
                @endforelse

                <form method="POST" action="{{ route('registre.deadlines.store', $dossier) }}" class="mt-4 flex flex-wrap items-end gap-2">
                    @csrf
                    <div class="min-w-48 flex-1">
                        <label for="label" class="block text-xs font-semibold text-slate-600">Intitulé</label>
                        <input type="text" id="label" name="label" required placeholder="Audience, rappel, dépôt…" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                    </div>

                    <div>
                        <label for="due_at" class="block text-xs font-semibold text-slate-600">Date</label>
                        <input type="date" id="due_at" name="due_at" required class="mt-1 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                    </div>

                    <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        Ajouter
                    </button>
                </form>
            </div>

            {{-- ============ STATUT ============ --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-sm font-bold text-slate-900">Statut du dossier</h2>
                    <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                        {{ $dossier->status?->label }}
                    </span>
                </div>

                @if($dossier->closed_at)
                    <p class="mt-3 text-sm text-slate-600">
                        Dossier clôturé le {{ $dossier->closed_at->translatedFormat('j F Y') }}.
                    </p>
                @endif

                @forelse ($nextStatuses as $status)
                    @if($loop->first)
                        <form method="POST" action="{{ route('registre.status.update', $dossier) }}" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label for="status_id" class="block text-xs font-semibold text-slate-600">Passer au statut</label>
                                <select id="status_id" name="status_id" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                    @endif

                                    <option value="{{ $status->id }}">
                                        {{ $status->label }}@if($status->is_terminal) — clôture @endif
                                        @if($status->requires_supervisor_validation) — exige la validation de l'encadrant @endif
                                    </option>

                    @if($loop->last)
                                </select>
                            </div>

                            <div>
                                <label for="status-note" class="block text-xs font-semibold text-slate-600">Note (facultatif)</label>
                                <input type="text" id="status-note" name="note" placeholder="Motif, décision, précision…" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                            </div>

                            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                Changer le statut
                            </button>
                        </form>
                    @endif
                @empty
                    <p class="mt-3 text-sm text-slate-500">
                        Aucun passage n'est déclaré depuis ce statut.
                    </p>
                @endforelse
            </div>

            {{-- ============ ANALYSE ============ --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-sm font-bold text-slate-900">Analyse</h2>

                    @if($dossier->isValidated())
                        <span class="rounded bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                            Validée le {{ $dossier->analysis_validated_at->translatedFormat('j M Y') }}
                            @if($dossier->validator) par {{ $dossier->validator->name }} @endif
                        </span>
                    @elseif($dossier->analysisAwaitsValidation())
                        <span class="rounded bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                            En attente de validation
                        </span>
                    @endif
                </div>

                @if($dossier->isValidated())
                    <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $dossier->analysis }}</p>
                @else
                    <form method="POST" action="{{ route('registre.analysis.save', $dossier) }}" class="mt-3 space-y-3">
                        @csrf
                        <label for="analysis" class="sr-only">Analyse</label>
                        <textarea
                            id="analysis"
                            name="analysis"
                            rows="8"
                            placeholder="Analyse juridique de la situation, réponse aux questions posées, suites proposées…"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                        >{{ old('analysis', $dossier->analysis) }}</textarea>

                        <div class="flex flex-wrap items-center gap-3">
                            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                Enregistrer et soumettre
                            </button>
                        </div>
                    </form>

                    @if($dossier->analysisAwaitsValidation())
                        <form method="POST" action="{{ route('registre.analysis.validate', $dossier) }}" class="mt-3 border-t border-slate-100 pt-3">
                            @csrf
                            <button type="submit" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-800 hover:bg-emerald-100">
                                Valider l'analyse (encadrant)
                            </button>
                        </form>
                    @endif
                @endif

                {{--
                    The gate the plan requires: the return to the applicant
                    waits on the supervisor. The status that marks it is one
                    of the thirteen the Clinic has yet to name, so what is
                    shown here is the condition, not the transition.
                --}}
                <p class="mt-4 border-t border-slate-100 pt-3 text-xs leading-relaxed {{ $dossier->returnableToApplicant() ? 'text-emerald-700' : 'text-slate-500' }}">
                    @if($dossier->returnableToApplicant())
                        L'analyse est validée : le retour au demandeur est possible.
                    @else
                        Le retour au demandeur reste bloqué tant que l'encadrant n'a pas validé l'analyse.
                    @endif
                </p>
            </div>

            {{-- ============ DOCUMENTS ============ --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-bold text-slate-900">Documents</h2>

                @forelse ($dossier->documents as $document)
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-100 px-3.5 py-2.5">
                        <span class="flex min-w-0 items-center gap-2 text-sm text-slate-700">
                            <x-icon name="document" class="h-4 w-4 shrink-0 text-slate-400" />
                            <span class="truncate">{{ $document->original_name }}</span>
                            <span class="shrink-0 text-xs text-slate-400">{{ $document->humanSize() }}</span>
                            @if($document->from_presaisine)
                                <span class="shrink-0 rounded bg-slate-100 px-2 py-0.5 text-xs text-slate-500">pré-saisine</span>
                            @endif
                        </span>

                        <a
                            href="{{ route('registre.dossiers.document', [$dossier, $document]) }}"
                            class="shrink-0 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            Télécharger
                        </a>
                    </div>
                @empty
                    <p class="mt-3 text-sm text-slate-500">Aucun document.</p>
                @endforelse

                <form method="POST" action="{{ route('registre.documents.store', $dossier) }}" enctype="multipart/form-data" class="mt-4 flex flex-wrap items-end gap-2 border-t border-slate-100 pt-4">
                    @csrf
                    <div>
                        <label for="kind" class="block text-xs font-semibold text-slate-600">Type</label>
                        <select id="kind" name="kind" class="mt-1 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                            <option value="mandat_signe">Mandat signé</option>
                            <option value="analyse">Analyse</option>
                            <option value="piece_jointe">Pièce jointe</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    <div class="min-w-48 flex-1">
                        <label for="file" class="block text-xs font-semibold text-slate-600">Fichier (PDF, JPG, PNG — 5 Mo)</label>
                        <input type="file" id="file" name="file" required accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
                    </div>

                    <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        Ajouter
                    </button>
                </form>
            </div>
        </div>

        {{-- ============ COLONNE DROITE ============ --}}
        <aside class="space-y-5">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-bold text-slate-900">Demandeur</h2>

                @php
                    $lines = [
                        'Nom' => $dossier->applicant?->fullName(),
                        'Téléphone' => $dossier->applicant?->phone,
                        'E-mail' => $dossier->applicant?->email,
                        'Commune' => $dossier->applicant?->commune,
                        'Étudiant UAM' => $dossier->applicant?->is_uam_student
                            ? ($dossier->applicant->student_card_number ?: 'Oui')
                            : 'Non',
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
                <h2 class="text-sm font-bold text-slate-900">Historique des statuts</h2>

                <ol class="mt-3 space-y-3">
                    @forelse ($dossier->statusHistory as $change)
                        <li class="border-l-2 border-slate-200 pl-3">
                            <p class="text-sm font-medium text-slate-900">{{ $change->toStatus?->label }}</p>
                            <p class="text-xs text-slate-500">
                                {{ $change->created_at?->translatedFormat('j M Y, H:i') }}
                                @if($change->user) &middot; {{ $change->user->name }} @endif
                            </p>
                            @if($change->note)
                                <p class="mt-1 text-xs text-slate-500">{{ $change->note }}</p>
                            @endif
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">Aucun changement enregistré.</li>
                    @endforelse
                </ol>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-bold text-slate-900">Journal de ce dossier</h2>

                <ul class="mt-3 space-y-2.5">
                    @forelse ($log as $entry)
                        <li class="text-xs">
                            <span class="font-medium text-slate-700">{{ $entry->action }}</span>
                            <span class="text-slate-500">
                                &middot; {{ $entry->created_at?->translatedFormat('j M, H:i') }}
                                @if($entry->user) &middot; {{ $entry->user->name }} @endif
                            </span>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">Rien à afficher.</li>
                    @endforelse
                </ul>
            </div>
        </aside>
    </div>

@endsection
