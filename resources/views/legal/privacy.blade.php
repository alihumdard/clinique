@extends('layouts.app')

@section('title', 'Politique de confidentialité - ' . config('app.name'))
@section('meta_description', "La Clinique Juridique FSJP-UAM s'engage a proteger vos donnees personnelles et a respecter votre vie privee : collecte, utilisation, conservation et vos droits.")

@section('content')

    <x-page-hero
        image="placeholders/privacy-security"
        title="Politique de confidentialité"
        subtitle="La Clinique Juridique FSJP-UAM s'engage à protéger vos données personnelles et à respecter votre vie privée."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Politique de confidentialité'],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">

            {{-- ============ SIDEBAR ============ --}}
            <div class="space-y-6 lg:sticky lg:top-24 lg:self-start">
                <x-legal-nav current="privacy" />

                <x-help-card icon="shield" title="Une question ?">
                    Pour toute question relative à notre politique de confidentialité, veuillez nous contacter.
                </x-help-card>

                <blockquote class="rounded-xl bg-slate-50 p-5">
                    <span class="text-3xl leading-none text-slate-300" aria-hidden="true">&ldquo;</span>
                    <p class="mt-1 text-sm italic leading-relaxed text-slate-600">
                        La protection de vos données est une priorité pour nous.
                    </p>
                    <span class="mt-4 block h-0.5 w-10 bg-primary-600" aria-hidden="true"></span>
                </blockquote>
            </div>

            {{-- ============ CONTENT ============ --}}
            <div class="space-y-6 lg:col-span-3">

                <x-info-note-card icon="shield" title="Votre vie privée est importante pour nous">
                    Cette politique de confidentialité explique comment la Clinique Juridique FSJP-UAM collecte,
                    utilise, protège et partage vos données personnelles lorsque vous visitez notre site web,
                    utilisez nos services ou entrez en contact avec nous.
                </x-info-note-card>

                {{-- 1. Data collected --}}
                <section>
                    <div class="flex items-start gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white">1</span>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg font-bold text-slate-900">Quelles données collectons-nous ?</h2>
                            <p class="mt-1 text-sm text-slate-600">Nous pouvons collecter les types de données suivants :</p>
                        </div>
                    </div>

                    @php
                        $dataTypes = [
                            ['icon' => 'users', 'title' => 'Informations personnelles', 'text' => "Nom, prénom, adresse e-mail, numéro de téléphone, profession et établissement d'enseignement (au besoin)."],
                            ['icon' => 'document', 'title' => 'Données de navigation', 'text' => "Adresse IP, type de navigateur, pages visitées, durée de visite, et autres données techniques collectées via des cookies."],
                            ['icon' => 'chat', 'title' => 'Informations de contact', 'text' => "Messages que vous nous envoyez via le formulaire de contact ou la pré-saisine."],
                            ['icon' => 'folder', 'title' => 'Autres informations', 'text' => "Toute information que vous partagez volontairement lors de votre interaction avec notre site ou nos services."],
                        ];
                    @endphp

                    <ul class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:ml-11">
                        @foreach ($dataTypes as $type)
                            <li class="flex items-start gap-3 rounded-lg bg-primary-50/60 p-5">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-primary-600">
                                    <x-icon :name="$type['icon']" class="h-5 w-5" />
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900">{{ $type['title'] }}</p>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $type['text'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>

                {{-- 2. How data is used --}}
                <section>
                    <div class="flex items-start gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white">2</span>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg font-bold text-slate-900">Comment utilisons-nous vos données ?</h2>
                            <p class="mt-1 text-sm text-slate-600">Vos données personnelles sont utilisées pour :</p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2.5 lg:ml-11">
                        <x-check-item variant="filled">Répondre à vos demandes d'information et à vos questions.</x-check-item>
                        <x-check-item variant="filled">Vous fournir nos services et nos accompagnements juridiques.</x-check-item>
                        <x-check-item variant="filled">Améliorer notre site web et nos services.</x-check-item>
                        <x-check-item variant="filled">Vous informer des actualités, événements et opportunités (avec votre consentement).</x-check-item>
                    </div>
                </section>

                {{-- 3. Sharing --}}
                <section>
                    <div class="flex items-start gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white">3</span>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg font-bold text-slate-900">Avec qui partageons-nous vos données ?</h2>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                                Nous ne partageons vos données personnelles qu'avec des tiers de confiance, uniquement
                                lorsque cela est nécessaire pour le bon fonctionnement de nos services (hébergement,
                                outils de gestion, etc.).
                                <strong class="font-semibold text-slate-900">Nous ne vendons ni ne louons jamais vos
                                données à des fins commerciales.</strong>
                            </p>
                        </div>
                    </div>
                </section>

                {{-- 4. Retention --}}
                <section>
                    <div class="flex items-start gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white">4</span>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg font-bold text-slate-900">Combien de temps conservons-nous vos données ?</h2>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                                Vos données sont conservées uniquement pendant la durée nécessaire pour atteindre les
                                finalités pour lesquelles elles ont été collectées, sauf obligation légale contraire.
                            </p>
                        </div>
                    </div>
                </section>

                {{-- 5. Rights --}}
                <section>
                    <div class="flex items-start gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white">5</span>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg font-bold text-slate-900">Vos droits</h2>
                            <p class="mt-1 text-sm text-slate-600">
                                Conformément à la réglementation en vigueur, vous disposez des droits suivants :
                            </p>
                        </div>
                    </div>

                    @php
                        $rights = [
                            ['icon' => 'eye', 'title' => 'Accès', 'text' => 'à vos données'],
                            ['icon' => 'document', 'title' => 'Rectification', 'text' => 'des données'],
                            ['icon' => 'info', 'title' => 'Suppression', 'text' => 'de vos données'],
                            ['icon' => 'shield', 'title' => 'Opposition', 'text' => 'au traitement'],
                            ['icon' => 'download', 'title' => 'Portabilité', 'text' => 'de vos données'],
                        ];
                    @endphp

                    <ul class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:ml-11 lg:grid-cols-5">
                        @foreach ($rights as $right)
                            <li class="rounded-lg bg-primary-50/60 p-4 text-center">
                                <span class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white text-primary-600">
                                    <x-icon :name="$right['icon']" class="h-5 w-5" />
                                </span>
                                <p class="mt-2.5 text-sm font-bold text-slate-900">{{ $right['title'] }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $right['text'] }}</p>
                            </li>
                        @endforeach
                    </ul>

                    <p class="mt-4 text-sm text-slate-600 lg:ml-11">
                        Pour exercer vos droits, vous pouvez nous contacter à l'adresse suivante :
                        <a href="mailto:{{ config('clinique.contact.email') }}" class="font-semibold text-primary-600 hover:text-primary-700">
                            {{ config('clinique.contact.email') }}
                        </a>
                    </p>
                </section>

                {{-- 6. Security --}}
                <section>
                    <div class="flex items-start gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white">6</span>
                        <h2 class="text-lg font-bold text-slate-900">Sécurité</h2>
                    </div>

                    <div class="mt-3 flex items-start gap-4 rounded-lg bg-primary-50/60 p-5 lg:ml-11">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-primary-600">
                            <x-icon name="lock" class="h-5 w-5" />
                        </span>
                        <p class="text-sm leading-relaxed text-slate-600">
                            Nous mettons en œuvre des mesures techniques et organisationnelles appropriées pour
                            protéger vos données contre tout accès non autorisé, toute perte, altération ou divulgation.
                        </p>
                    </div>
                </section>

                {{-- 7. Changes --}}
                <section>
                    <div class="flex items-start gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white">7</span>
                        <h2 class="text-lg font-bold text-slate-900">Modifications</h2>
                    </div>

                    <div class="mt-3 flex items-start gap-4 rounded-lg bg-primary-50/60 p-5 lg:ml-11">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-primary-600">
                            <x-icon name="document" class="h-5 w-5" />
                        </span>
                        <p class="text-sm leading-relaxed text-slate-600">
                            Nous pouvons être amenés à modifier cette politique de confidentialité à tout moment.
                            Toute modification sera publiée sur cette page avec une date de mise à jour.
                        </p>
                    </div>
                </section>

            </div>
        </div>
    </section>

@endsection
