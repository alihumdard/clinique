@extends('layouts.app')

@section('title', 'Nos partenaires - ' . config('app.name'))
@section('meta_description', "La Clinique Juridique FSJP-UAM travaille en etroite collaboration avec des institutions publiques, des organisations internationales, des ONG, des ordres professionnels et des acteurs de la societe civile.")

@section('content')

    <x-page-hero
        image="placeholders/about-hero"
        imagePosition="object-right-top"
        :title="__('Nos partenaires')"
        subtitle="Institutions publiques, organisations internationales et acteurs de la société civile — ensemble pour l'accès au droit au Niger."
        :breadcrumb="[
            ['label' => __('Accueil'), 'url' => route('home')],
            ['label' => __('Partenaires')],
        ]"
    />

    {{-- ============ INTRO + STATS PANEL ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-12 lg:px-8 lg:py-16">
        <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-2 lg:gap-10">
            <div>
                <x-eyebrow>Nos partenaires</x-eyebrow>
                <h2 class="mt-2 text-2xl font-bold leading-tight text-slate-900 sm:text-3xl">Des partenariats au service de l'accès au droit</h2>
                <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">
                    La Clinique Juridique FSJP-UAM bénéficie du soutien de l'Université Abdou Moumouni,
                    du Haut-Commissariat des Nations Unies pour les réfugiés (HCR), de l'Organisation
                    internationale de droit du développement (IDLO) et d'Affaires mondiales Canada. Ces
                    partenariats renforcent nos activités, facilitent l'accès au droit et contribuent à
                    la formation pratique de nos étudiants cliniciens.
                </p>
            </div>

            {{-- The figures are the rubriques themselves, so the panel can
                 never drift from the lists below it. --}}
            @php
                $statIcons = [
                    'institutions-nationales' => 'document',
                    'partenaires-internationaux' => 'search',
                    'societe-civile-et-ong' => 'user-plus',
                ];
            @endphp

            <div class="grid grid-cols-2 gap-x-5 gap-y-5 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:gap-x-8 sm:gap-y-6 sm:p-6">
                <x-mini-stat icon="users" title="Partenaires" :value="$total" subtitle="au total" />

                @foreach ($sections as $section)
                    <x-mini-stat
                        :icon="$statIcons[$section['category']->slug] ?? 'users'"
                        :title="$section['category']->name"
                        :value="$section['partners']->count()"
                        subtitle=""
                    />
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ PARTNER SECTIONS ============ --}}
    {{-- Alternating background, so each rubrique reads as its own band
         however many the Clinic ends up with. --}}
    @forelse ($sections as $section)
        <section class="{{ $loop->even ? '' : 'bg-slate-50' }} py-10 sm:py-12 lg:py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-partner-section
                    eyebrow="Nos partenaires"
                    :title="$section['category']->name"
                    :partners="$section['partners']"
                />
            </div>
        </section>
    @empty
        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <p class="text-sm text-slate-500">
                La liste de nos partenaires sera publiée prochainement.
            </p>
        </section>
    @endforelse

    @if ($unsorted->isNotEmpty())
        <section class="{{ $sections->count() % 2 === 0 ? '' : 'bg-slate-50' }} py-10 sm:py-12 lg:py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-partner-section
                    eyebrow="Nos partenaires"
                    :title="__('Autres partenaires')"
                    :partners="$unsorted"
                />
            </div>
        </section>
    @endif

    {{-- ============ INLINE CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-12 lg:px-8 lg:py-16">
        <x-inline-cta
            icon="users"
            :title="__('Vous souhaitez collaborer avec nous ?')"
            :button-label="__('Nous contacter')"
            :button-href="route('contact')"
        >
            La Clinique est ouverte à toute collaboration visant à renforcer l'accès au droit et la
            promotion de la justice au Niger. Contactez-nous pour en discuter.
        </x-inline-cta>
    </section>

@endsection
