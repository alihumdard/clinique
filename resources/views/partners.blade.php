@extends('layouts.app')

@section('title', 'Nos partenaires - ' . config('app.name'))
@section('meta_description', "La Clinique Juridique FSJP-UAM travaille en etroite collaboration avec des institutions publiques, des organisations internationales, des ONG, des ordres professionnels et des acteurs de la societe civile.")

@section('content')

    <x-page-hero
        image="placeholders/hero-building"
        title="Nos partenaires"
        subtitle="Des institutions et organisations engagées à nos côtés pour une justice plus accessible"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Partenaires'],
        ]"
    />

    {{-- ============ INTRO + STATS PANEL ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2">
            <div>
                <x-eyebrow>Une collaboration pour l'intérêt général</x-eyebrow>
                <h2 class="mt-2 text-3xl font-bold leading-tight text-slate-900">Des partenariats au service de la justice</h2>
                <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">
                    La Clinique Juridique FSJP-UAM travaille en étroite collaboration avec des institutions
                    publiques, des organisations internationales, des ONG, des ordres professionnels et des
                    acteurs de la société civile. Ces partenariats renforcent nos actions, facilitent l'accès
                    au droit et contribuent à la formation pratique de nos étudiants.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-x-8 gap-y-6 rounded-xl border border-slate-200 bg-slate-50 p-6">
                <x-mini-stat icon="users" title="Partenaires" value="15+" subtitle="institutionnels" />
                <x-mini-stat icon="search" title="Organisations" value="5+" subtitle="internationales" />
                <x-mini-stat icon="user-plus" title="ONG et associations" value="10+" subtitle="" />
                <x-mini-stat icon="document" title="Institutions publiques" value="8+" subtitle="et professionnelles" />
            </div>
        </div>
    </section>

    {{-- ============ INSTITUTIONS NATIONALES ============ --}}
    <section class="bg-slate-50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-partner-section
                eyebrow="Nos partenaires institutionnels"
                title="Institutions nationales"
                :partners="[
                    ['name' => 'Ministère de la Justice', 'subtitle' => 'Niger'],
                    ['name' => 'Université Abdou Moumouni', 'subtitle' => 'Niamey'],
                    ['name' => 'Cour d\'Appel de Niamey', 'subtitle' => 'Niger'],
                    ['name' => 'Tribunal de Grande Instance', 'subtitle' => 'Niamey'],
                    ['name' => 'Conseil Supérieur de la Magistrature', 'subtitle' => 'Niger'],
                    ['name' => 'Ordre des Avocats du Niger', 'subtitle' => 'Niamey'],
                ]"
            />
        </div>
    </section>

    {{-- ============ PARTENAIRES INTERNATIONAUX ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <x-partner-section
            eyebrow="Organisations internationales"
            title="Partenaires internationaux"
            :partners="[
                ['name' => 'PNUD', 'subtitle' => 'Programme des Nations Unies pour le Développement'],
                ['name' => 'UNICEF', 'subtitle' => 'Fonds des Nations Unies pour l\'enfance'],
                ['name' => 'Union Européenne', 'subtitle' => 'Délégation au Niger'],
                ['name' => 'AFD', 'subtitle' => 'Agence Française de Développement'],
                ['name' => 'OIM', 'subtitle' => 'Organisation Internationale pour les Migrations'],
                ['name' => 'CICR', 'subtitle' => 'Comité International de la Croix-Rouge'],
            ]"
        />
    </section>

    {{-- ============ SOCIETE CIVILE ET ONG ============ --}}
    <section class="bg-slate-50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-partner-section
                eyebrow="ONG et associations"
                title="Société civile et ONG"
                :partners="[
                    ['name' => 'ASF', 'subtitle' => 'Avocats Sans Frontières'],
                    ['name' => 'Amnesty International', 'subtitle' => null],
                    ['name' => 'Human Rights Watch', 'subtitle' => null],
                    ['name' => 'Search for Common Ground', 'subtitle' => null],
                    ['name' => 'ALIMA', 'subtitle' => 'The Alliance for International Medical Action'],
                    ['name' => 'Plan International', 'subtitle' => null],
                ]"
            />
        </div>
    </section>

    {{-- ============ INLINE CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            icon="users"
            title="Vous souhaitez devenir partenaire ?"
            button-label="Nous contacter"
            button-href="{{ route('contact') }}"
        >
            Nous sommes ouverts à toute collaboration visant à renforcer l'accès au droit, la formation des
            étudiants et la promotion de la justice au Niger.
        </x-inline-cta>
    </section>

@endsection
