@extends('layouts.app')

@section('title', 'Pré-saisine - ' . config('app.name'))

@section('content')

    {{--
        Shown when an orientation answer stops the journey. No answer is
        stored: the plan forbids keeping the replies of a visitor who is
        not routed to the form, so this page is the end of it.
    --}}
    <section class="mx-auto max-w-2xl px-4 py-16 sm:px-6 lg:py-20">

        <x-icon-badge><x-icon name="info" class="h-6 w-6" /></x-icon-badge>

        <h1 class="mt-5 text-2xl font-bold leading-snug text-slate-900 sm:text-3xl">
            {{ $outcome['title'] }}
        </h1>

        <p class="mt-4 text-base leading-relaxed text-slate-600">
            {{ $outcome['body'] }}
        </p>

        @if($outcome['show_phone'] ?? false)
            <div class="mt-6 rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-sm font-semibold text-slate-900">Joindre la Clinique</p>

                <p class="mt-2 flex items-center gap-2 text-sm text-slate-600">
                    <x-icon name="phone" class="h-4 w-4 shrink-0 text-primary-600" />
                    <a href="tel:{{ config('clinique.contact.phone.tel') }}" class="font-medium hover:text-primary-600">
                        {{ config('clinique.contact.phone.display') }}
                    </a>
                </p>

                <p class="mt-2 flex items-center gap-2 text-sm text-slate-600">
                    <x-icon name="clock" class="h-4 w-4 shrink-0 text-primary-600" />
                    @foreach (config('clinique.hours.schedule') as $slot)
                        {{ $slot['days'] }} : {{ $slot['time'] }}@if(! $loop->last) &middot; @endif
                    @endforeach
                </p>
            </div>
        @endif

        @if($provisional)
            <p class="mt-6 text-xs leading-relaxed text-slate-400">
                Le texte de cette orientation est provisoire et sera remplacé par la
                formulation approuvée par la Clinique.
            </p>
        @endif

        <div class="mt-8 flex flex-wrap items-center gap-3">
            <x-button :href="route('contact')" variant="primary">Nous contacter</x-button>
            <x-button :href="route('services')" variant="outline" :arrow="false">Voir nos services</x-button>
            <x-button :href="route('presaisine.index')" variant="text" :arrow="false">Recommencer</x-button>
        </div>

    </section>

@endsection
