@extends('layouts.app')

@section('title', "Conditions d'utilisation - " . config('app.name'))
@section('meta_description', "Les presentes conditions regissent l'utilisation du site web de la Clinique Juridique FSJP-UAM : acces, utilisation du contenu, comportement des utilisateurs et droit applicable.")

@section('content')

@php
    // Defined here: an escaped double quote inside a Blade attribute breaks the parser.
    $breadcrumb = [
        ['label' => 'Accueil', 'url' => route('home')],
        ['label' => "Conditions d'utilisation"],
    ];
@endphp

    <x-page-hero
        image="placeholders/about-legal"
        title="Conditions d'utilisation"
        subtitle="Les présentes conditions régissent l'utilisation du site web de la Clinique Juridique FSJP-UAM."
        :breadcrumb="$breadcrumb"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">

            <x-legal-nav current="terms" class="lg:sticky lg:top-24 lg:self-start" />

            <div class="space-y-5 lg:col-span-3">

                <x-legal-section number="1" title="Objet" icon="document">
                    Les présentes conditions d'utilisation ont pour objet de définir les modalités et conditions
                    d'accès et d'utilisation du site web de la Clinique Juridique FSJP-UAM (ci-après « le Site »).
                    En accédant au Site, vous acceptez sans réserve les présentes conditions.
                </x-legal-section>

                <x-legal-section number="2" title="Accès au site" icon="globe">
                    Le Site est accessible gratuitement à tout utilisateur disposant d'une connexion Internet.
                    La Clinique Juridique FSJP-UAM s'efforce d'assurer un accès continu au Site, mais ne saurait
                    être tenue responsable en cas d'interruption, de suspension ou d'indisponibilité temporaire
                    pour des raisons techniques ou de maintenance.
                </x-legal-section>

                <x-legal-section number="3" title="Utilisation du contenu" icon="document">
                    Les contenus publiés sur le Site (textes, images, vidéos, documents, logos, etc.) sont fournis
                    à titre informatif et pédagogique. Ils ne constituent pas des conseils juridiques personnalisés
                    et ne remplacent pas une consultation directe avec la Clinique.
                    Toute utilisation, reproduction ou diffusion du contenu à des fins commerciales est interdite
                    sans autorisation préalable.
                </x-legal-section>

                <x-legal-section
                    number="4"
                    title="Comportement des utilisateurs"
                    icon="users"
                >
                    <p>En utilisant le Site, vous vous engagez à :</p>

                    <div class="mt-3 space-y-2">
                        <x-check-item variant="filled">Ne pas publier de contenus illégaux, diffamatoires ou contraires aux bonnes mœurs ;</x-check-item>
                        <x-check-item variant="filled">Ne pas tenter d'altérer le bon fonctionnement du Site ;</x-check-item>
                        <x-check-item variant="filled">Respecter les droits de propriété intellectuelle et la confidentialité des informations ;</x-check-item>
                        <x-check-item variant="filled">Utiliser les formulaires de contact et de pré-saisine de manière loyale et sincère.</x-check-item>
                    </div>
                </x-legal-section>

                <x-legal-section number="5" title="Liens vers des sites tiers" icon="globe">
                    Le Site peut contenir des liens vers des sites web de partenaires ou d'institutions tierces.
                    La Clinique Juridique FSJP-UAM n'exerce aucun contrôle sur ces sites et décline toute
                    responsabilité quant à leurs contenus ou pratiques.
                </x-legal-section>

                <x-legal-section number="6" title="Protection des données personnelles" icon="shield">
                    L'utilisation du Site implique la collecte et le traitement de certaines données personnelles
                    conformément à notre <a href="{{ route('legal.privacy') }}">Politique de confidentialité</a>.
                    En utilisant le Site, vous acceptez ces pratiques.
                </x-legal-section>

                <x-legal-section number="7" title="Modification des conditions" icon="info">
                    La Clinique Juridique FSJP-UAM se réserve le droit de modifier à tout moment les présentes
                    conditions d'utilisation. Les utilisateurs seront informés de toute mise à jour par la
                    publication de la nouvelle version sur cette page.
                </x-legal-section>

                <x-legal-section number="8" title="Droit applicable" icon="scale">
                    Les présentes conditions d'utilisation sont soumises au droit nigérien. En cas de litige
                    relatif à l'utilisation du Site, les tribunaux compétents de Niamey seront seuls compétents.
                </x-legal-section>

            </div>
        </div>
    </section>

@endsection
