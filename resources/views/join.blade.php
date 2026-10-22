@extends('layouts.app')

@section('title', 'Rejoindre la Clinique - Appels à candidatures - ' . config('app.name'))
@section('meta_description', "Rejoignez une equipe engagee pour la promotion de l'acces au droit : stages, volontariat, consultances et partenariats avec la Clinique Juridique FSJP-UAM.")

@section('content')

    <x-page-hero
        image="placeholders/join-hero"
        image-position="object-right"
        title="Rejoindre la Clinique"
        title-line2="Appels à candidatures"
        subtitle="Rejoignez une équipe engagée pour la promotion de l'accès au droit et la justice au service des populations."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Rejoindre la Clinique', 'url' => route('join')],
            ['label' => 'Appels à candidatures'],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        {{-- ============ FILTERS + SEARCH ============ --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-wrap lg:overflow-visible lg:pb-0">
                <x-filter-pill :active="true">Tous les appels (5)</x-filter-pill>
                <x-filter-pill>Stagiaires (2)</x-filter-pill>
                <x-filter-pill>Volontaires (1)</x-filter-pill>
                <x-filter-pill>Consultants (1)</x-filter-pill>
                <x-filter-pill>Partenaires (1)</x-filter-pill>
            </div>

            <form role="search" class="relative shrink-0 lg:w-72">
                <label for="calls-search" class="sr-only">Rechercher un appel à candidatures</label>
                <input
                    type="search"
                    id="calls-search"
                    name="q"
                    placeholder="Rechercher un appel..."
                    class="w-full rounded-md border border-slate-200 py-2.5 pl-4 pr-10 text-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                >
                <button type="submit" aria-label="Rechercher" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary-600">
                    <x-icon name="search" class="h-4 w-4" />
                </button>
            </form>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-3">

            {{-- ============ MAIN ============ --}}
            <div class="lg:col-span-2">

                <x-section-label>Appels à candidatures en cours (5)</x-section-label>

                @php
                    /*
                     * Open calls. Module 2 replaces this with a query filtered
                     * on the closing date so expired calls drop off the list.
                     */
                    $calls = [
                        [
                            'image' => 'placeholders/join-call-stage',
                            'type' => 'Stage académique',
                            'color' => 'primary',
                            'title' => 'Stage pour étudiants en droit',
                            'excerpt' => "La Clinique Juridique FSJP-UAM lance un appel à candidatures pour le recrutement de stagiaires étudiants en droit pour l'année 2024-2025.",
                            'published' => '15 septembre 2024',
                            'deadline' => '15 octobre 2024',
                        ],
                        [
                            'image' => 'placeholders/news-caravan',
                            'type' => 'Volontariat',
                            'color' => 'green',
                            'title' => 'Volontaires pour les caravanes juridiques',
                            'excerpt' => "Rejoignez notre équipe de volontaires pour l'organisation des caravanes juridiques dans les régions du Niger.",
                            'published' => '10 septembre 2024',
                            'deadline' => '30 septembre 2024',
                        ],
                        [
                            'image' => 'placeholders/join-call-consultance',
                            'type' => 'Consultance',
                            'color' => 'purple',
                            'title' => 'Consultant(e) en communication',
                            'excerpt' => "La Clinique Juridique FSJP-UAM recherche un(e) consultant(e) pour l'élaboration d'une stratégie de communication et de visibilité.",
                            'published' => '5 septembre 2024',
                            'deadline' => '25 septembre 2024',
                        ],
                        [
                            'image' => 'placeholders/news-formation',
                            'type' => 'Appel à partenariat',
                            'color' => 'amber',
                            'title' => 'Partenaires pour les formations juridiques',
                            'excerpt' => "Appel à manifestation d'intérêt pour les partenaires techniques et financiers souhaitant soutenir nos activités de formation.",
                            'published' => '1er septembre 2024',
                            'deadline' => '30 octobre 2024',
                        ],
                        [
                            'image' => 'placeholders/join-call-recherche',
                            'type' => 'Recherche',
                            'color' => 'primary',
                            'title' => 'Assistant(e) de recherche',
                            'excerpt' => "Nous recrutons un(e) assistant(e) de recherche pour appuyer nos études et publications sur l'accès au droit et la justice.",
                            'published' => '20 août 2024',
                            'deadline' => '20 septembre 2024',
                        ],
                    ];
                @endphp

                <div class="mt-5 space-y-5">
                    @foreach ($calls as $call)
                        <x-call-row
                            :image="$call['image']"
                            :type="$call['type']"
                            :type-color="$call['color']"
                            :title="$call['title']"
                            :excerpt="$call['excerpt']"
                            :published="$call['published']"
                            :deadline="$call['deadline']"
                            :href="route('join')"
                        />
                    @endforeach
                </div>

                {{-- ============ PROFILES SOUGHT ============ --}}
                <div class="mt-12">
                    <x-section-label>Profils recherchés</x-section-label>

                    @php
                        $profiles = [
                            ['icon' => 'graduation-cap', 'title' => 'Étudiants en droit', 'text' => 'Stages académiques'],
                            ['icon' => 'users', 'title' => 'Jeunes diplômés', 'text' => 'Volontariat et projets'],
                            ['icon' => 'folder', 'title' => 'Experts et consultants', 'text' => 'Appui technique'],
                            ['icon' => 'globe', 'title' => 'Partenaires', 'text' => 'Institutions et ONG'],
                        ];
                    @endphp

                    <ul class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($profiles as $profile)
                            <li class="flex items-start gap-3 rounded-lg border border-slate-200 bg-white p-4 transition-all duration-200 hover:-translate-y-1 hover:border-primary-200 hover:bg-primary-50/40 hover:shadow-md">
                                <x-icon-badge size="sm" class="shrink-0">
                                    <x-icon :name="$profile['icon']" class="h-5 w-5" />
                                </x-icon-badge>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900">{{ $profile['title'] }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $profile['text'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- ============ FAQ ============ --}}
                <div class="mt-12">
                    <x-section-label>Questions fréquentes</x-section-label>

                    <div class="mt-5 space-y-3">
                        <x-faq-item question="Qui peut postuler aux appels à candidatures ?">
                            Les appels sont ouverts aux étudiants en droit de l'Université Abdou Moumouni, aux jeunes
                            diplômés, aux experts et consultants, ainsi qu'aux institutions et organisations souhaitant
                            nouer un partenariat. Chaque appel précise les critères d'éligibilité qui lui sont propres.
                        </x-faq-item>

                        <x-faq-item question="Quels sont les documents requis ?">
                            Un curriculum vitae à jour et une lettre de motivation sont demandés pour toutes les
                            candidatures. Selon l'appel, une copie des diplômes, une attestation d'inscription ou
                            une note méthodologique peuvent également être exigées.
                        </x-faq-item>

                        <x-faq-item question="Comment se déroule le processus de sélection ?">
                            Les candidatures reçues font l'objet d'une présélection sur dossier. Les candidats retenus
                            sont ensuite conviés à un entretien avec l'équipe de coordination de la Clinique. Les
                            résultats sont communiqués à l'ensemble des candidats.
                        </x-faq-item>

                        <x-faq-item question="Puis-je postuler à plusieurs appels ?">
                            Oui. Vous pouvez candidater à plusieurs appels, à condition de remplir les critères de
                            chacun et de soumettre un dossier distinct pour chaque candidature.
                        </x-faq-item>
                    </div>
                </div>
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">

                <div class="rounded-xl bg-primary-50/60 p-5">
                    <div class="flex items-center gap-2.5">
                        <x-icon-badge size="sm"><x-icon name="users" class="h-5 w-5" /></x-icon-badge>
                        <h2 class="text-base font-bold text-slate-900">Pourquoi nous rejoindre ?</h2>
                    </div>

                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        Contribuez à une justice plus inclusive et développez vos compétences dans un environnement
                        académique et professionnel stimulant.
                    </p>

                    <div class="mt-4 space-y-2.5">
                        <x-check-item variant="filled">Expérience pratique en droit</x-check-item>
                        <x-check-item variant="filled">Engagement social et communautaire</x-check-item>
                        <x-check-item variant="filled">Encadrement par des experts</x-check-item>
                        <x-check-item variant="filled">Participation à des projets concrets</x-check-item>
                        <x-check-item variant="filled">Réseau professionnel</x-check-item>
                    </div>
                </div>

                @php
                    // Defined here rather than inline: an escaped double quote
                    // inside a Blade attribute breaks the parser.
                    $applySteps = [
                        ['title' => "Consulter l'appel", 'text' => 'Lisez attentivement les critères et les documents requis.'],
                        ['title' => 'Préparer votre dossier', 'text' => 'CV, lettre de motivation et documents demandés.'],
                        ['title' => 'Soumettre votre candidature', 'text' => 'Via le formulaire en ligne ou par email selon les indications.'],
                        ['title' => 'Suivre votre candidature', 'text' => 'Vous recevrez une confirmation et serez informé(e) des prochaines étapes.'],
                    ];
                @endphp

                <x-sidebar-card icon="document" title="Comment postuler ?">
                    <x-numbered-steps :steps="$applySteps" />
                </x-sidebar-card>

                <x-help-card icon="mail" title="Une question ?">
                    Notre équipe est à votre disposition pour répondre à vos questions sur les appels à candidatures.
                </x-help-card>

                <div class="rounded-xl bg-primary-50/60 p-5">
                    <div class="flex items-start gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-white text-rose-500">
                            <x-icon name="document" class="h-6 w-6" />
                        </span>
                        <div class="min-w-0">
                            <h3 class="text-base font-bold text-slate-900">Télécharger le guide</h3>
                            <p class="mt-0.5 text-sm text-slate-600">Guide du candidat (PDF, 1,2 Mo)</p>
                        </div>
                    </div>

                    <x-button href="#" variant="outline" :arrow="false" class="mt-4 w-full justify-center">
                        <x-icon name="download" class="h-4 w-4" />
                        Télécharger
                    </x-button>
                </div>

            </aside>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            title="Rejoignez notre réseau"
            button-label="S'inscrire aux alertes"
            :button-href="route('contact')"
        >
            Soyez informé(e) en avant-première des nouveaux appels à candidatures.
        </x-inline-cta>
    </section>

@endsection
