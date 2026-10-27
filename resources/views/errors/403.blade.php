@extends('layouts.app')

@section('title', 'Accès refusé - ' . config('app.name'))

@section('content')

    {{--
        Reached when an éditeur de contenu follows a link to an
        administrator-only screen. It says which it is — a permission, not
        a missing page — so the person knows to ask for access rather than
        to look for the screen elsewhere. Module 4 refines this state.
    --}}
    <section class="mx-auto flex max-w-2xl flex-col items-center px-4 py-20 text-center sm:px-6 lg:py-24">
        <x-icon-badge><x-icon name="lock" class="h-6 w-6" /></x-icon-badge>

        <h1 class="mt-6 text-2xl font-bold text-slate-900 sm:text-3xl">Accès refusé</h1>

        <p class="mt-3 text-base leading-relaxed text-slate-600">
            {{ $exception?->getMessage() ?: "Votre compte n'a pas accès à cette section." }}
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            @auth
                <x-button :href="route('admin.dashboard')" variant="primary">Retour au tableau de bord</x-button>
            @endauth

            <x-button :href="route('home')" variant="outline" :arrow="false">Aller au site public</x-button>
        </div>
    </section>

@endsection
