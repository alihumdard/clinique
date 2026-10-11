@extends('layouts.app')

@section('title', "Rapports annuels d'activité - " . config('app.name'))
@section('meta_description', "Decouvrez les rapports annuels d'activite de la Clinique Juridique FSJP-UAM : realisations, chiffres cles et impact de nos actions pour une justice plus accessible au Niger.")

@section('content')

@php
    // Defined here rather than inline: an escaped double quote inside a
    // Blade attribute breaks the parser.
    $breadcrumb = [
        ['label' => 'Accueil', 'url' => route('home')],
        ['label' => 'Ressources', 'url' => route('resources')],
        ['label' => "Rapports annuels d'activité"],
    ];
@endphp

    <x-page-hero
        image="placeholders/about-legal"
        title="Rapports annuels d'activité"
        subtitle="Découvrez nos réalisations, nos chiffres clés et l'impact de nos actions pour une justice plus accessible au Niger."
        :breadcrumb="$breadcrumb"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">

            {{-- ============ MAIN ============ --}}
            <div class="lg:col-span-2">

                <x-info-note-card icon="chart-bar" title="Transparence et impact">
                    Nos rapports annuels d'activité présentent un bilan complet de nos actions, de nos résultats
                    et de notre contribution à la promotion de l'accès au droit au Niger. Ils témoignent de notre
                    engagement pour une justice plus inclusive et plus proche des populations.
                </x-info-note-card>

                <div class="mt-10">
                    <x-section-label>Rapports disponibles</x-section-label>

                    @php
                        /*
                         * Published reports, newest first. Module 2 replaces
                         * this with a query; `href` becomes the stored PDF.
                         */
                        $reports = [
                            [
                                'cover' => 'placeholders/about-legal',
                                'title' => "Rapport annuel d'activité 2024",
                                'excerpt' => "Un bilan complet de nos activités, des chiffres clés, des témoignages et des perspectives pour renforcer l'accès au droit.",
                                'date' => 'Janvier 2025',
                                'size' => '4,2 Mo',
                                'views' => '1 250',
                            ],
                            [
                                'cover' => 'placeholders/article-highlight-1',
                                'title' => "Rapport annuel d'activité 2023",
                                'excerpt' => "Nos réalisations, nos partenariats et l'impact de nos actions sur les communautés locales au Niger.",
                                'date' => 'Janvier 2024',
                                'size' => '3,8 Mo',
                                'views' => '980',
                            ],
                            [
                                'cover' => 'placeholders/article-highlight-2',
                                'title' => "Rapport annuel d'activité 2022",
                                'excerpt' => "Une année d'engagement pour l'accès au droit et la protection des personnes vulnérables.",
                                'date' => 'Janvier 2023',
                                'size' => '3,5 Mo',
                                'views' => '760',
                            ],
                            [
                                'cover' => 'placeholders/article-highlight-3',
                                'title' => "Rapport annuel d'activité 2021",
                                'excerpt' => "Retour sur nos principales actions, nos formations et nos caravanes juridiques.",
                                'date' => 'Janvier 2022',
                                'size' => '3,1 Mo',
                                'views' => '620',
                            ],
                        ];
                    @endphp

                    <div class="mt-5 space-y-5">
                        @foreach ($reports as $report)
                            <x-report-row
                                :cover="$report['cover']"
                                :title="$report['title']"
                                :excerpt="$report['excerpt']"
                                :date="$report['date']"
                                :filesize="$report['size']"
                                :views="$report['views']"
                            />
                        @endforeach
                    </div>
                </div>

                {{-- ============ EVOLUTION ============ --}}
                <div class="mt-10 rounded-xl bg-primary-50/60 p-6">
                    <div class="flex items-center gap-2.5">
                        <x-icon-badge size="sm"><x-icon name="chart-bar" class="h-5 w-5" /></x-icon-badge>
                        <h2 class="text-base font-bold text-slate-900">Évolution de nos activités</h2>
                    </div>

                    @php
                        $evolution = [
                            ['value' => '620', 'year' => '2021'],
                            ['value' => '980', 'year' => '2022'],
                            ['value' => '1 850', 'year' => '2023'],
                            ['value' => '2 450', 'year' => '2024'],
                        ];
                    @endphp

                    <dl class="mt-5 grid grid-cols-2 gap-5 sm:grid-cols-4">
                        @foreach ($evolution as $step)
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white text-primary-600">
                                    <x-icon name="chart-bar" class="h-5 w-5" />
                                </span>
                                <div>
                                    <dd class="text-xl font-bold leading-tight text-slate-900">{{ $step['value'] }}</dd>
                                    <dt class="text-xs text-slate-600">Bénéficiaires ({{ $step['year'] }})</dt>
                                </div>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">

                <div class="rounded-xl bg-primary-50/60 p-5">
                    <div class="flex items-center gap-2.5">
                        <x-icon-badge size="sm"><x-icon name="chart-bar" class="h-5 w-5" /></x-icon-badge>
                        <h2 class="text-base font-bold text-slate-900">Chiffres clés (2024)</h2>
                    </div>

                    <x-figure-list
                        class="mt-4"
                        :items="[
                            ['icon' => 'users', 'value' => '2 450', 'label' => 'Personnes conseillées', 'color' => 'blue'],
                            ['icon' => 'map-pin', 'value' => '15', 'label' => 'Caravanes juridiques', 'color' => 'green'],
                            ['icon' => 'graduation-cap', 'value' => '50+', 'label' => 'Étudiants impliqués', 'color' => 'amber'],
                            ['icon' => 'document', 'value' => '120', 'label' => 'Sessions de sensibilisation', 'color' => 'purple'],
                            ['icon' => 'handshake', 'value' => '10', 'label' => 'Partenariats institutionnels', 'color' => 'red'],
                        ]"
                    />
                </div>

                <x-sidebar-card icon="folder" title="Autres documents">
                    @php
                        $documents = [
                            ['label' => 'Rapports thématiques', 'icon' => 'document', 'color' => 'blue'],
                            ['label' => 'Études et recherches', 'icon' => 'book', 'color' => 'amber'],
                            ['label' => 'Guides et manuels', 'icon' => 'document', 'color' => 'red'],
                            ['label' => 'Statistiques', 'icon' => 'chart-bar', 'color' => 'purple'],
                            ['label' => 'Publications', 'icon' => 'folder', 'color' => 'amber'],
                        ];

                        $swatches = [
                            'blue' => 'bg-primary-50 text-primary-600',
                            'amber' => 'bg-amber-50 text-amber-600',
                            'red' => 'bg-rose-50 text-rose-600',
                            'purple' => 'bg-violet-50 text-violet-600',
                        ];
                    @endphp

                    <ul class="space-y-1">
                        @foreach ($documents as $document)
                            <li>
                                <a href="{{ route('resources') }}" class="group flex items-center gap-2.5 rounded-md px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded {{ $swatches[$document['color']] }}">
                                        <x-icon :name="$document['icon']" class="h-3.5 w-3.5" />
                                    </span>
                                    <span class="min-w-0 flex-1 truncate group-hover:text-primary-600">{{ $document['label'] }}</span>
                                    <x-icon name="arrow-right" class="h-4 w-4 shrink-0 text-slate-400 group-hover:text-primary-600" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-sidebar-card>

                <x-help-card title="Besoin d'un rapport ou d'une information ?">
                    Notre équipe est à votre disposition pour toute demande d'information ou de document complémentaire.
                </x-help-card>

            </aside>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            title="Contribuez à nos actions"
            button-label="Faire une pré-saisine"
            :button-href="route('presaisine.index')"
        >
            Votre soutien nous permet de renforcer nos activités et d'atteindre plus de personnes.
        </x-inline-cta>
    </section>

@endsection
