@extends('layouts.app')

@section('title', 'Pré-saisine — vérification - ' . config('app.name'))

@section('content')

    <x-page-hero
        image="placeholders/legal-scales-gavel"
        title="Vérifiez votre demande"
        subtitle="Étape 3 sur 3 — relisez avant d'envoyer."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Pré-saisine', 'url' => route('presaisine.index')],
            ['label' => 'Vérification'],
        ]"
    />

    <section class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">

        @error('envoi')
            <div role="alert" class="mb-8 rounded-lg border border-rose-200 bg-rose-50 p-5 text-sm text-rose-800">
                {{ $message }}
            </div>
        @enderror

        <p class="text-base leading-relaxed text-slate-600">
            Rien n'est encore enregistré. Relisez votre demande, puis envoyez-la : vous
            recevrez alors un numéro de référence à conserver.
        </p>

        @php
            $lines = [
                'Nom' => trim(($draft['first_name'] ?? '') . ' ' . ($draft['last_name'] ?? '')),
                'Téléphone' => $draft['phone'] ?? null,
                'Adresse e-mail' => $draft['email'] ?? null,
                'Commune' => $draft['commune'] ?? null,
                'Étudiant(e) UAM' => ($draft['is_uam_student'] ?? false) ? 'Oui — carte n° ' . ($draft['student_card_number'] ?? '—') : 'Non',
                'Domaine juridique' => $draft['legal_domain'] ?? null,
                'Objet' => $draft['subject'] ?? null,
            ];
        @endphp

        <div class="mt-8 overflow-hidden rounded-xl border border-slate-200 bg-white">
            <dl class="divide-y divide-slate-100">
                @foreach ($lines as $label => $value)
                    @continue(blank($value))
                    <div class="flex flex-col gap-1 px-5 py-3.5 sm:flex-row sm:items-baseline sm:justify-between sm:gap-6">
                        <dt class="shrink-0 text-sm text-slate-500">{{ $label }}</dt>
                        <dd class="text-sm font-medium text-slate-900 sm:text-right">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        @if(filled($draft['narrative'] ?? null))
            <div class="mt-6 space-y-2">
                <h2 class="text-sm font-bold text-slate-900">Exposé de la situation</h2>
                <p class="whitespace-pre-line rounded-xl border border-slate-200 bg-white p-5 text-sm leading-relaxed text-slate-700">{{ $draft['narrative'] }}</p>
            </div>
        @endif

        @php $questions = array_filter([$draft['question_1'] ?? null, $draft['question_2'] ?? null, $draft['question_3'] ?? null]); @endphp

        @if(count($questions))
            <div class="mt-6 space-y-2">
                <h2 class="text-sm font-bold text-slate-900">Vos questions</h2>
                <ol class="list-inside list-decimal space-y-1.5 rounded-xl border border-slate-200 bg-white p-5 text-sm leading-relaxed text-slate-700">
                    @foreach ($questions as $question)
                        <li>{{ $question }}</li>
                    @endforeach
                </ol>
            </div>
        @endif

        @if(count($files))
            <div class="mt-6 space-y-2">
                <h2 class="text-sm font-bold text-slate-900">Pièces jointes</h2>
                <ul class="space-y-2 rounded-xl border border-slate-200 bg-white p-5">
                    @foreach ($files as $file)
                        <li class="flex items-center gap-2 text-sm text-slate-700">
                            <x-icon name="document" class="h-4 w-4 shrink-0 text-slate-400" />
                            <span class="truncate">{{ $file['original_name'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-8 flex flex-wrap items-center gap-3">
            <form method="POST" action="{{ route('presaisine.submit') }}">
                @csrf
                <x-button variant="primary" type="submit">Envoyer ma demande</x-button>
            </form>

            <x-button :href="route('presaisine.form')" variant="outline" :arrow="false">Corriger ma demande</x-button>
        </div>

    </section>

@endsection
