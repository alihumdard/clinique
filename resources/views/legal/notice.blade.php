@extends('layouts.app')

@section('title', 'Mentions légales - ' . config('app.name'))
@section('meta_description', "Informations legales relatives a l'utilisation du site web de la Clinique Juridique FSJP-UAM : editeur, hebergement, propriete intellectuelle et droit applicable.")

@section('content')

    <x-page-hero
        image="placeholders/about-legal"
        title="Mentions légales"
        subtitle="Informations légales relatives à l'utilisation du site web de la Clinique Juridique FSJP-UAM."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Mentions légales'],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">

            <x-legal-nav current="notice" class="lg:sticky lg:top-24 lg:self-start" />

            <div class="space-y-5 lg:col-span-3">

                <x-legal-section
                    number="1"
                    title="Éditeur du site"
                    lead="Le site web de la Clinique Juridique FSJP-UAM est édité par :"
                >
                    <div class="flex items-start gap-4 rounded-lg bg-primary-50/60 p-5">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white text-primary-600">
                            <x-icon name="building" class="h-6 w-6" />
                        </span>
                        <address class="not-italic">
                            <strong class="block font-semibold text-slate-900">Clinique Juridique</strong>
                            {{ config('clinique.contact.address.line1') }}<br>
                            {{ config('clinique.contact.address.line2') }}<br>
                            {{ config('clinique.contact.address.line3') }}, {{ config('clinique.contact.address.city') }}, {{ config('clinique.contact.address.country') }}
                        </address>
                    </div>
                </x-legal-section>

                <x-legal-section
                    number="2"
                    title="Responsable de la publication"
                    lead="Le responsable de la publication du site est :"
                >
                    <div class="flex items-start gap-4 rounded-lg bg-primary-50/60 p-5">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white text-primary-600">
                            <x-icon name="users" class="h-6 w-6" />
                        </span>
                        <address class="not-italic">
                            <strong class="block font-semibold text-slate-900">Coordonnateur de la Clinique Juridique FSJP-UAM</strong>
                            Faculté des Sciences Juridiques et Politiques<br>
                            Université Abdou Moumouni de Niamey
                        </address>
                    </div>
                </x-legal-section>

                <x-legal-section
                    number="3"
                    title="Hébergement du site"
                    lead="Le site est hébergé par :"
                >
                    {{-- Host details are a placeholder: UAM IT confirms the final
                         infrastructure in Phase 1 before deployment. --}}
                    <div class="flex items-start gap-4 rounded-lg bg-primary-50/60 p-5">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white text-primary-600">
                            <x-icon name="building" class="h-6 w-6" />
                        </span>
                        <address class="not-italic">
                            <strong class="block font-semibold text-slate-900">Hébergeur : à confirmer</strong>
                            Les coordonnées de l'hébergeur seront précisées<br>
                            après validation de l'infrastructure par le service informatique de l'Université.
                        </address>
                    </div>
                </x-legal-section>

                <x-legal-section number="4" title="Propriété intellectuelle" icon="copyright">
                    L'ensemble des contenus présents sur ce site (textes, images, vidéos, logos, documents, etc.)
                    sont la propriété exclusive de la Clinique Juridique FSJP-UAM ou de leurs auteurs respectifs,
                    sauf mention contraire. Toute reproduction, représentation, modification, publication ou
                    adaptation, même partielle, est interdite sans autorisation préalable écrite.
                </x-legal-section>

                <x-legal-section number="5" title="Données personnelles" icon="shield">
                    Les données personnelles collectées via le site (formulaires de contact, pré-saisine, etc.)
                    sont traitées conformément à notre
                    <a href="{{ route('legal.privacy') }}">Politique de confidentialité</a>.
                    Elles sont utilisées uniquement dans le cadre des activités de la Clinique Juridique FSJP-UAM
                    et ne sont pas transmises à des tiers sans votre accord.
                </x-legal-section>

                <x-legal-section number="6" title="Cookies" icon="info">
                    Le site utilise des cookies strictement nécessaires à son fonctionnement, notamment pour
                    la gestion de votre session et la sécurité des formulaires. Aucun cookie publicitaire ni
                    traceur tiers n'est déposé sans votre consentement préalable.
                </x-legal-section>

                <x-legal-section number="7" title="Limitation de responsabilité" icon="help-circle">
                    La Clinique Juridique FSJP-UAM s'efforce d'assurer l'exactitude et la mise à jour des
                    informations diffusées sur ce site. Toutefois, elle ne saurait être tenue responsable des
                    erreurs, omissions ou d'une indisponibilité temporaire du site. Les informations publiées
                    ont une valeur informative et ne constituent pas une consultation juridique.
                </x-legal-section>

                <x-legal-section number="8" title="Droit applicable" icon="scale">
                    Les présentes mentions légales sont soumises au droit nigérien. En cas de litige, les
                    tribunaux compétents de Niamey seront seuls compétents.
                </x-legal-section>

            </div>
        </div>
    </section>

@endsection
