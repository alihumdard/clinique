@extends('layouts.app')

@section('title', 'Nos partenaires - ' . config('app.name'))
@section('meta_description', "La Clinique Juridique FSJP-UAM travaille en etroite collaboration avec des institutions publiques, des organisations internationales, des ONG, des ordres professionnels et des acteurs de la societe civile.")

@section('content')

    <x-page-hero
        image="placeholders/about-hero"
        title="Nos partenaires"
        subtitle="Des institutions et organisations engagées à nos côtés pour une justice plus accessible"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Partenaires'],
        ]"
    />

    {{-- ============ INTRO + STATS PANEL ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-12 lg:px-8 lg:py-16">
        <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-2 lg:gap-10">
            <div>
                <x-eyebrow>Une collaboration pour l'intérêt général</x-eyebrow>
                <h2 class="mt-2 text-2xl font-bold leading-tight text-slate-900 sm:text-3xl">Des partenariats au service de la justice</h2>
                <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">
                    La Clinique Juridique FSJP-UAM travaille en étroite collaboration avec des institutions
                    publiques, des organisations internationales, des ONG, des ordres professionnels et des
                    acteurs de la société civile. Ces partenariats renforcent nos actions, facilitent l'accès
                    au droit et contribuent à la formation pratique de nos étudiants.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-x-5 gap-y-5 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:gap-x-8 sm:gap-y-6 sm:p-6">
                <x-mini-stat icon="users" title="Partenaires" value="15+" subtitle="institutionnels" />
                <x-mini-stat icon="search" title="Organisations" value="5+" subtitle="internationales" />
                <x-mini-stat icon="user-plus" title="ONG et associations" value="10+" subtitle="" />
                <x-mini-stat icon="document" title="Institutions publiques" value="8+" subtitle="et professionnelles" />
            </div>
        </div>
    </section>

    {{-- ============ INSTITUTIONS NATIONALES ============ --}}
    <section class="bg-slate-50 py-10 sm:py-12 lg:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-partner-section
                eyebrow="Nos partenaires institutionnels"
                title="Institutions nationales"
                :partners="[
                    ['name' => 'Ministère de la Justice', 'subtitle' => 'Niger', 'logo' => 'uemf'],
                    ['name' => 'Université Abdou Moumouni', 'subtitle' => 'Niamey', 'logo' => 'ministere-justice'],
                    ['name' => 'Cour d\'Appel de Niamey', 'subtitle' => 'Niger', 'logo' => 'onu-femmes'],
                    ['name' => 'Tribunal de Grande Instance', 'subtitle' => 'Niamey', 'logo' => 'amdh'],
                    ['name' => 'Conseil Supérieur de la Magistrature', 'subtitle' => 'Niger', 'logo' => 'barreau-fes'],
                    ['name' => 'Ordre des Avocats du Niger', 'subtitle' => 'Niamey', 'logo' => 'pndd'],
                ]"
            />
        </div>
    </section>

    {{-- ============ PARTENAIRES INTERNATIONAUX ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-12 lg:px-8 lg:py-16">
        <x-partner-section
            eyebrow="Organisations internationales"
            title="Partenaires internationaux"
            :partners="[
                ['name' => 'PNUD', 'subtitle' => 'Programme des Nations Unies pour le Développement', 'logo' => 'uemf'],
                ['name' => 'UNICEF', 'subtitle' => 'Fonds des Nations Unies pour l\'enfance', 'logo' => 'ministere-justice'],
                ['name' => 'Union Européenne', 'subtitle' => 'Délégation au Niger', 'logo' => 'onu-femmes'],
                ['name' => 'AFD', 'subtitle' => 'Agence Française de Développement', 'logo' => 'amdh'],
                ['name' => 'OIM', 'subtitle' => 'Organisation Internationale pour les Migrations', 'logo' => 'barreau-fes'],
                ['name' => 'CICR', 'subtitle' => 'Comité International de la Croix-Rouge', 'logo' => 'pndd'],
            ]"
        />
    </section>

    {{-- ============ SOCIETE CIVILE ET ONG ============ --}}
    <section class="bg-slate-50 py-10 sm:py-12 lg:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-partner-section
                eyebrow="ONG et associations"
                title="Société civile et ONG"
                :partners="[
                    ['name' => 'ASF', 'subtitle' => 'Avocats Sans Frontières', 'logo' => 'uemf'],
                    ['name' => 'Amnesty International', 'subtitle' => null, 'logo' => 'ministere-justice'],
                    ['name' => 'Human Rights Watch', 'subtitle' => null, 'logo' => 'onu-femmes'],
                    ['name' => 'Search for Common Ground', 'subtitle' => null, 'logo' => 'amdh'],
                    ['name' => 'ALIMA', 'subtitle' => 'The Alliance for International Medical Action', 'logo' => 'barreau-fes'],
                    ['name' => 'Plan International', 'subtitle' => null, 'logo' => 'pndd'],
                ]"
            />
        </div>
    </section>

    {{-- ============ INLINE CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-12 lg:px-8 lg:py-16">
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
