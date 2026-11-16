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
        image="placeholders/annual-report-hero"
        imagePosition="object-right-top"
        :title="__('Rapports annuels d\'activité')"
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

                    <div class="mt-5 space-y-5">
                        @forelse ($reports as $report)
                            <x-report-row
                                :stored-path="$report->cover"
                                :title="$report->title"
                                :excerpt="$report->description"
                                :date="$report->published_at?->translatedFormat('F Y')"
                                :filesize="$report->fileSizeForHumans()"
                                :href="route('documents.report', $report)"
                            />
                        @empty
                            <p class="text-sm text-slate-500">
                                Aucun rapport n'est disponible pour le moment.
                            </p>
                        @endforelse
                    </div>

                    <x-pagination :paginator="$reports" />
                </div>

                {{-- Evolution stats removed: the Clinique launched in 2025 --}}
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">

                <x-sidebar-card icon="folder" title="Autres documents" :tint="true">
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
            :title="__('Contribuez à nos actions')"
            :button-label="__('Faire une pré-saisine')"
            :button-href="route('presaisine.index')"
        >
            Votre soutien nous permet de renforcer nos activités et d'atteindre plus de personnes.
        </x-inline-cta>
    </section>

@endsection
