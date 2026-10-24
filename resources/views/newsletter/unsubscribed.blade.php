@extends('layouts.app')

@section('title', 'Désinscription de la newsletter - ' . config('app.name'))
@section('meta_description', "Votre desinscription de la newsletter de la Clinique Juridique FSJP-UAM a ete prise en compte.")

@section('content')

    <section class="mx-auto max-w-2xl px-4 py-20 text-center sm:px-6 lg:px-8">
        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
            <x-icon name="check-circle" class="h-7 w-7" />
        </span>

        <h1 class="mt-6 text-2xl font-bold text-slate-900 sm:text-3xl">
            Votre désinscription a été prise en compte
        </h1>

        <p class="mt-4 text-base leading-relaxed text-slate-600">
            Vous ne recevrez plus la newsletter de la Clinique Juridique FSJP-UAM.
            Vous pouvez vous réabonner à tout moment depuis la page Actualités.
        </p>

        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <x-button :href="route('home')" variant="primary">Retour à l'accueil</x-button>
            <x-button :href="route('news.index')" variant="outline">Voir les actualités</x-button>
        </div>
    </section>

@endsection
