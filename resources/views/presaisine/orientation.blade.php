@extends('layouts.app')

@section('title', 'Pré-saisine - ' . config('app.name'))
@section('meta_description', "Repondez a quatre questions pour savoir si la Clinique Juridique FSJP-UAM peut prendre votre demande en charge.")

@section('content')

    <x-page-hero
        image="placeholders/legal-scales-gavel"
        :title="__('Pré-saisine')"
        subtitle="Quatre questions pour savoir si nous pouvons prendre votre demande en charge."
        :breadcrumb="[
            ['label' => __('Accueil'), 'url' => route('home')],
            ['label' => __('Pré-saisine')],
        ]"
    />

    <section class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">

        @if($provisional)
            {{-- Said to the visitor, not only to the Clinic: the wording
                 below is ours until the Clinic approves its own. --}}
            <x-notice-banner icon="info" lead="Formulation provisoire." class="mb-8">
                Les questions ci-dessous servent à vous orienter ; leur formulation
                définitive est en cours de validation par la Clinique.
            </x-notice-banner>
        @endif

        <div class="space-y-3">
            <x-section-label>Avant de déposer une demande</x-section-label>
            <p class="text-base leading-relaxed text-slate-600">
                La pré-saisine permet à la Clinique de savoir, avant tout rendez-vous, si votre
                situation relève de ses domaines d'intervention. Vos réponses à ces quatre
                questions ne sont pas conservées : elles servent uniquement à vous orienter.
            </p>
        </div>

        <form method="POST" action="{{ route('presaisine.orient') }}" class="mt-8 space-y-6">
            @csrf

            @foreach ($questions as $i => $question)
                <fieldset class="rounded-xl border border-slate-200 bg-white p-5">
                    <legend class="px-1 text-sm font-bold text-primary-600">
                        Question {{ $i + 1 }} / {{ count($questions) }}
                    </legend>

                    <p class="text-base font-semibold leading-snug text-slate-900">
                        {{ $question['label'] }}
                    </p>

                    @if($question['help'] ?? null)
                        <p class="mt-1.5 text-sm leading-relaxed text-slate-500">{{ $question['help'] }}</p>
                    @endif

                    <div class="mt-4 flex flex-wrap gap-3">
                        @foreach (['oui' => 'Oui', 'non' => 'Non'] as $value => $label)
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 hover:border-primary-400 has-checked:border-primary-600 has-checked:bg-primary-50">
                                <input
                                    type="radio"
                                    name="reponses[{{ $question['key'] }}]"
                                    value="{{ $value }}"
                                    @checked(old('reponses.' . $question['key']) === $value)
                                    required
                                    class="h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-500"
                                >
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>

                    @error('reponses.' . $question['key'])
                        <p role="alert" class="mt-3 text-sm text-rose-700">Merci de répondre à cette question.</p>
                    @enderror
                </fieldset>
            @endforeach

            <div class="flex flex-wrap items-center gap-3">
                <x-button variant="primary" type="submit">Continuer</x-button>
                <x-button :href="route('contact')" variant="outline" :arrow="false">{{ __('Nous contacter') }}</x-button>
            </div>
        </form>

    </section>

@endsection
