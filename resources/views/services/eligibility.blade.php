@extends('layouts.app')

@section('title', "Conditions d'accès et gratuité - " . config('app.name'))
@section('meta_description', "La Clinique Juridique FSJP-UAM offre une assistance juridique gratuite a la population, dans le respect des principes d'egalite, de dignite et de justice.")

@section('content')

    <x-page-hero
        image="placeholders/eligibility-hero"
        title="Conditions d'accès et gratuité"
        subtitle="Une justice accessible à tous, sans discrimination"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Nos services', 'url' => route('services')],
            ['label' => 'Conditions d\'accès et gratuité'],
        ]"
    />

    {{-- ============ ENGAGEMENT + QUOTE ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2.5">
                    <span class="h-0.5 w-6 shrink-0 bg-amber-400" aria-hidden="true"></span>
                    <x-eyebrow>Notre engagement</x-eyebrow>
                </div>
                <h2 class="mt-2 text-2xl font-bold leading-tight text-slate-900 sm:text-3xl">
                    Une assistance juridique gratuite et ouverte à tous
                </h2>
                <p class="mt-4 max-w-2xl text-base leading-relaxed text-slate-600">
                    La Clinique Juridique FSJP-UAM offre une assistance juridique gratuite à la population,
                    dans le cadre de sa mission de service public et de formation pratique des étudiants.
                    Nos services sont accessibles à toute personne, sans discrimination, dans le respect des
                    principes d'égalité, de dignité et de justice.
                </p>
            </div>

            <x-quote-card attribution="Notre mission au service de la population" :tint="true">
                « Rendre le droit plus accessible à toutes et à tous, en particulier aux personnes les plus vulnérables. »
            </x-quote-card>
        </div>
    </section>

    {{-- ============ CRITERES D'ACCES (8 cards) ============ --}}
    <section class="bg-slate-50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-eyebrow>Qui peut bénéficier de nos services ?</x-eyebrow>
            <h2 class="mt-2 text-2xl font-bold text-slate-900">Critères d'accès</h2>
            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-600">
                Nos services sont ouverts à toute personne qui souhaite obtenir une information, un conseil
                ou un accompagnement juridique dans les domaines couverts par la Clinique.
            </p>

            <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <x-service-card compact :link="false" icon="users" title="Tout citoyen" color="primary">
                    Toute personne, sans condition de nationalité, de sexe, de religion ou d'origine sociale.
                </x-service-card>
                <x-service-card compact :link="false" icon="graduation-cap" title="Étudiants" color="green">
                    Les étudiants de l'Université et d'autres établissements d'enseignement.
                </x-service-card>
                <x-service-card compact :link="false" icon="users" title="Personnes vulnérables" color="purple">
                    Les personnes en situation de vulnérabilité (femmes, enfants, personnes handicapées, personnes déplacées, etc.).
                </x-service-card>
                <x-service-card compact :link="false" icon="document" title="Groupes communautaires" color="amber">
                    Les associations, organisations de la société civile et groupes communautaires.
                </x-service-card>
                <x-service-card compact :link="false" icon="building" title="Justiciables à faible revenu" color="red">
                    Les personnes qui n'ont pas les moyens de recourir à une assistance payante.
                </x-service-card>
                <x-service-card compact :link="false" icon="handshake" title="Personnes en situation de crise" color="teal">
                    Les personnes affectées par des situations d'urgence (déplacement, catastrophes, conflits, etc.).
                </x-service-card>
                <x-service-card compact :link="false" icon="book" title="Autres publics" color="purple">
                    Toute personne ayant besoin d'une orientation juridique dans les domaines couverts par la Clinique.
                </x-service-card>
                <x-service-card compact :link="false" icon="globe" title="Sans discrimination" color="green">
                    Nos services sont accessibles à tous, sans aucune forme de discrimination.
                </x-service-card>
            </div>
        </div>
    </section>

    {{-- ============ LA GRATUITE (split) ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:items-center">
            <x-picture
                path="placeholders/about-legal"
                alt="Balance de la justice et marteau de juge"
                width="700" height="500"
                class="aspect-[4/3] w-full rounded-xl object-cover"
            />

            <div>
                <x-eyebrow>La gratuité</x-eyebrow>
                <h2 class="mt-2 text-2xl font-bold leading-tight text-slate-900">Des services entièrement gratuits</h2>
                <p class="mt-3 max-w-lg text-sm leading-relaxed text-slate-600">
                    L'assistance juridique fournie par la Clinique Juridique FSJP-UAM est gratuite. Aucun frais
                    n'est demandé pour :
                </p>

                <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <x-compact-checklist-item icon="chat">Information juridique</x-compact-checklist-item>
                    <x-compact-checklist-item icon="document">Orientation vers les services compétents</x-compact-checklist-item>
                    <x-compact-checklist-item icon="users">Entretiens et conseils</x-compact-checklist-item>
                    <x-compact-checklist-item icon="handshake">Accompagnement dans les démarches</x-compact-checklist-item>
                    <x-compact-checklist-item icon="search">Analyse de dossiers</x-compact-checklist-item>
                    <x-compact-checklist-item icon="gavel">Soutien dans la recherche de solutions</x-compact-checklist-item>
                </div>

                <div class="mt-6 flex items-start gap-3 rounded-lg bg-primary-50 p-4">
                    <x-icon name="info" class="mt-0.5 h-5 w-5 shrink-0 text-primary-600" />
                    <p class="text-sm leading-relaxed text-slate-700">
                        La gratuité de nos services s'inscrit dans notre mission de service public et de
                        promotion de l'accès au droit pour tous.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ ATTENTION PARTICULIERE (amber banner) ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="amber"
            icon="heart"
            title="Une attention particulière aux personnes vulnérables"
            button-label="Faire une pré-saisine"
            :button-href="route('presaisine.index')"
        >
            La Clinique accorde une attention spéciale aux femmes, aux enfants, aux personnes handicapées,
            aux personnes déplacées et à toute personne en situation de vulnérabilité.
        </x-inline-cta>
    </section>

    {{-- ============ FAQ ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <x-eyebrow>Questions fréquentes</x-eyebrow>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Questions sur l'accès et la gratuité</h2>
            </div>
            <x-button href="{{ route('services') }}#faq" variant="text" class="self-start shrink-0 sm:self-auto">Voir toutes les questions</x-button>
        </div>

        <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
            <x-faq-item question="Les services de la Clinique sont-ils vraiment gratuits ?">
                Oui, l'ensemble de nos services est entièrement gratuit, sans aucune exception.
            </x-faq-item>
            <x-faq-item question="Quels documents sont nécessaires ?">
                Une pièce d'identité et tout document relatif à votre situation (contrats, courriers, décisions administratives, etc.).
            </x-faq-item>
            <x-faq-item question="Qui peut bénéficier des services de la Clinique ?">
                Toute personne résidant au Niger, sans condition de nationalité, de revenu ou de statut social.
            </x-faq-item>
            <x-faq-item question="Les services sont-ils disponibles pour les personnes vulnérables ?">
                Oui, une attention particulière est accordée aux femmes, enfants, personnes handicapées et personnes déplacées.
            </x-faq-item>
            <x-faq-item question="Faut-il être étudiant pour accéder aux services ?">
                Non, nos services sont ouverts à toute personne, étudiante ou non.
            </x-faq-item>
            <x-faq-item question="Puis-je être accompagné dans mes démarches ?">
                Oui, nos étudiants encadrés peuvent vous accompagner tout au long de votre démarche juridique.
            </x-faq-item>
            <x-faq-item question="Y a-t-il des frais de dossier ou d'administration ?">
                Non, aucun frais n'est demandé, quel que soit le type de service sollicité.
            </x-faq-item>
            <x-faq-item question="Que faire si mon cas ne relève pas de la Clinique ?">
                Nous vous orientons vers un service partenaire ou une structure compétente adaptée à votre situation.
            </x-faq-item>
        </div>
    </section>

    {{-- ============ CTA (dark) ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            title="Besoin d'une assistance juridique ?"
            button-label="Faire une pré-saisine"
            :button-href="route('presaisine.index')"
        >
            Remplissez notre formulaire de pré-saisine pour être rapidement orienté vers le bon service.
        </x-inline-cta>
    </section>

@endsection
