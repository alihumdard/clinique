@extends('layouts.app')

@section('title', 'Pré-saisine envoyée - ' . config('app.name'))

@section('content')

    <section class="mx-auto max-w-2xl px-4 py-16 sm:px-6 lg:py-20">

        <x-icon-badge class="!bg-emerald-50 !text-emerald-600">
            <x-icon name="check-circle" class="h-6 w-6" />
        </x-icon-badge>

        <h1 class="mt-5 text-2xl font-bold leading-snug text-slate-900 sm:text-3xl">
            Votre demande a bien été envoyée
        </h1>

        <p class="mt-4 text-base leading-relaxed text-slate-600">
            Conservez le numéro ci-dessous : il identifie votre demande dans nos échanges.
        </p>

        {{-- The reference, large and selectable: it is what the applicant
             is asked to quote, so it must be easy to read and to copy. --}}
        <p class="mt-6 select-all rounded-xl border-2 border-dashed border-primary-200 bg-primary-50/60 px-6 py-5 text-center text-2xl font-bold tracking-wider text-primary-800">
            {{ $reference }}
        </p>

        <div class="mt-8 space-y-3">
            <x-section-label>La suite</x-section-label>
            <p class="text-base leading-relaxed text-slate-600">
                La Clinique examine votre demande et vous informe de sa recevabilité. Si vous
                avez indiqué une adresse e-mail, un accusé de réception vous y est adressé.
                Pour toute question, contactez-nous en indiquant votre numéro de référence.
            </p>
        </div>

        <div class="mt-8 flex flex-wrap items-center gap-3">
            <x-button :href="route('home')" variant="primary">{{ __("Retour à l'accueil") }}</x-button>
            <x-button :href="route('contact')" variant="outline" :arrow="false">{{ __('Nous contacter') }}</x-button>
        </div>

    </section>

@endsection
