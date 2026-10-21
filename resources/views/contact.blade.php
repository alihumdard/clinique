@extends('layouts.app')

@section('title', 'Contactez-nous - ' . config('app.name'))
@section('meta_description', "Une question, une demande d'information ou besoin d'accompagnement ? Contactez la Clinique Juridique FSJP-UAM a Niamey.")

@section('content')

    <x-page-hero
        image="placeholders/contact-hero"
        title="Contactez-nous"
        subtitle="Une question, une demande d'information ou besoin d'accompagnement ? Notre équipe est à votre écoute."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Contact'],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        {{-- ============ CONTACT REASONS ============ --}}
        @php
            $reasons = [
                ['icon' => 'headset', 'title' => 'Informations générales', 'text' => 'Des renseignements sur nos services et nos activités.'],
                ['icon' => 'document', 'title' => "Demande d'assistance", 'text' => "Un besoin d'orientation juridique ou de pré-saisine."],
                ['icon' => 'handshake', 'title' => 'Partenariats', 'text' => 'Des collaborations pour soutenir nos actions.'],
                ['icon' => 'users', 'title' => 'Recrutement', 'text' => "Des opportunités de stage, volontariat ou emploi."],
            ];
        @endphp

        <ul class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($reasons as $reason)
                <li class="group rounded-xl bg-primary-50/60 p-6 text-center transition-transform duration-300 hover:scale-105">
                    <x-icon-badge class="mx-auto !bg-white transition-transform duration-300 group-hover:scale-110">
                        <x-icon :name="$reason['icon']" class="h-6 w-6" />
                    </x-icon-badge>
                    <h2 class="mt-4 text-base font-bold text-slate-900">{{ $reason['title'] }}</h2>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $reason['text'] }}</p>
                </li>
            @endforeach
        </ul>

        {{-- ============ DETAILS + FORM ============ --}}
        <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-5">

            {{-- Coordinates: every value comes from config/clinique.php --}}
            <div class="rounded-xl border border-primary-100 bg-primary-50/60 p-6 lg:col-span-2">
                <x-section-label>Nos coordonnées</x-section-label>

                <ul class="mt-6 space-y-6">
                    <li class="flex items-start gap-4">
                        <x-icon-badge size="sm" class="shrink-0"><x-icon name="map-pin" class="h-5 w-5" /></x-icon-badge>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900">Adresse</p>
                            <address class="mt-1 text-sm not-italic leading-relaxed text-slate-600">
                                {{ config('clinique.contact.address.line1') }}<br>
                                {{ config('clinique.contact.address.line2') }}<br>
                                {{ config('clinique.contact.address.line3') }}, {{ config('clinique.contact.address.city') }}, {{ config('clinique.contact.address.country') }}
                            </address>
                        </div>
                    </li>

                    <li class="flex items-start gap-4">
                        <x-icon-badge size="sm" class="shrink-0"><x-icon name="phone" class="h-5 w-5" /></x-icon-badge>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900">Téléphone</p>
                            <p class="mt-1 text-sm text-slate-600">
                                <a href="tel:{{ config('clinique.contact.phone.tel') }}" class="hover:text-primary-600">
                                    {{ config('clinique.contact.phone.display') }}
                                </a>
                            </p>
                        </div>
                    </li>

                    <li class="flex items-start gap-4">
                        <x-icon-badge size="sm" class="shrink-0"><x-icon name="mail" class="h-5 w-5" /></x-icon-badge>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900">Email</p>
                            <p class="mt-1 break-words text-sm text-slate-600">
                                <a href="mailto:{{ config('clinique.contact.email') }}" class="hover:text-primary-600">
                                    {{ config('clinique.contact.email') }}
                                </a>
                            </p>
                        </div>
                    </li>

                    <li class="flex items-start gap-4">
                        <x-icon-badge size="sm" class="shrink-0"><x-icon name="clock" class="h-5 w-5" /></x-icon-badge>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900">Horaires d'ouverture</p>
                            <div class="mt-1 text-sm leading-relaxed text-slate-600">
                                @foreach (config('clinique.hours.schedule') as $slot)
                                    <p>{{ $slot['days'] }} : {{ $slot['time'] }}</p>
                                @endforeach
                                <p class="text-slate-500">({{ config('clinique.hours.closed') }})</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            {{-- Message form --}}
            <div class="rounded-xl border border-slate-200 p-6 lg:col-span-3">
                <x-section-label>Envoyez-nous un message</x-section-label>

                {{--
                    Presentation only: Module 2 adds the POST route, server-side
                    validation, anti-spam and mail delivery. Client-side `required`
                    attributes are a convenience, never the security boundary.
                --}}
                <form class="mt-6 space-y-5" method="post" action="#" novalidate>
                    @csrf

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-form-field
                            name="nom"
                            label="Nom complet"
                            placeholder="Votre nom complet"
                            :required="true"
                            autocomplete="name"
                        />

                        <x-form-field
                            name="email"
                            type="email"
                            label="Adresse email"
                            placeholder="votre@email.com"
                            :required="true"
                            autocomplete="email"
                        />

                        @php
                            // Defined here rather than inline: an escaped double
                            // quote inside a Blade attribute breaks the parser.
                            $subjectOptions = [
                                'information' => 'Informations générales',
                                'assistance' => "Demande d'assistance",
                                'partenariat' => 'Partenariats',
                                'recrutement' => 'Recrutement',
                                'autre' => 'Autre',
                            ];
                        @endphp

                        <x-form-field
                            name="sujet"
                            type="select"
                            label="Sujet"
                            placeholder="Sélectionnez un sujet"
                            :required="true"
                            :options="$subjectOptions"
                        />

                        <x-form-field
                            name="telephone"
                            type="tel"
                            label="Téléphone"
                            placeholder="+227 ..."
                            autocomplete="tel"
                        />
                    </div>

                    <x-form-field
                        name="message"
                        type="textarea"
                        label="Votre message"
                        placeholder="Décrivez votre demande ou votre question..."
                        :required="true"
                        :rows="6"
                    />

                    {{-- Consent is separate from any terms acceptance, per the
                         data-protection requirements in the project plan. --}}
                    <div class="flex items-start gap-2.5">
                        <input
                            type="checkbox"
                            id="field-consentement"
                            name="consentement"
                            required
                            class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                        >
                        <label for="field-consentement" class="text-sm leading-snug text-slate-600">
                            J'accepte que mes informations soient utilisées pour traiter ma demande.
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                    </div>

                    <x-button variant="primary" :arrow="false" class="w-full justify-center">
                        <x-icon name="paper-plane" class="h-4 w-4" />
                        Envoyer le message
                    </x-button>
                </form>
            </div>
        </div>

        {{-- ============ LOCATION ============ --}}
        <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-5">

            <div class="lg:col-span-3">
                <x-section-label>Notre localisation</x-section-label>

                <div class="relative mt-5 overflow-hidden rounded-xl border border-slate-200">
                    <iframe
                        src="https://www.google.com/maps?q={{ urlencode('Faculté des Sciences Juridiques et Politiques, Université Abdou Moumouni, Niamey, Niger') }}&output=embed"
                        class="aspect-[21/10] w-full border-0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Plan de localisation de la Clinique Juridique sur le campus de l'Université Abdou Moumouni"
                    ></iframe>

                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 bg-white p-4">
                        <p class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                            <x-icon name="map-pin" class="h-5 w-5 shrink-0 text-rose-500" />
                            Clinique Juridique FSJP - UAM
                        </p>

                        <a
                            href="https://www.google.com/maps/search/?api=1&query={{ urlencode('Faculté des Sciences Juridiques et Politiques, Université Abdou Moumouni, Niamey, Niger') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 rounded-md bg-primary-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-primary-700"
                        >
                            Voir sur Google Maps
                            <x-icon name="arrow-right" class="h-4 w-4" />
                        </a>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-primary-50/60 p-6 lg:col-span-2">
                @php
                    $access = [
                        ['icon' => 'map-pin', 'title' => 'Accès et transports', 'text' => "La Clinique est située dans l'enceinte de la FSJP - UAM, facilement accessible depuis le centre-ville de Niamey."],
                        ['icon' => 'users', 'title' => 'Transport en commun', 'text' => "Lignes de bus universitaires et urbaines desservant l'UAM."],
                        ['icon' => 'building', 'title' => 'En voiture', 'text' => "Parking disponible à l'entrée de la faculté."],
                        ['icon' => 'info', 'title' => 'Repère', 'text' => "À proximité de la Bibliothèque universitaire et de l'amphithéâtre central."],
                    ];
                @endphp

                <ul class="space-y-5">
                    @foreach ($access as $item)
                        <li class="flex items-start gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-primary-600">
                                <x-icon :name="$item['icon']" class="h-5 w-5" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-900">{{ $item['title'] }}</p>
                                <p class="mt-0.5 text-sm leading-relaxed text-slate-600">{{ $item['text'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- ============ FAQ + SOCIAL ============ --}}
        <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-5">

            <div class="lg:col-span-3">
                <x-section-label>Questions fréquentes</x-section-label>

                <div class="mt-5 space-y-3">
                    <x-faq-item question="Comment puis-je bénéficier d'une assistance juridique ?">
                        Vous pouvez vous présenter directement à nos permanences aux heures d'ouverture, ou remplir
                        le formulaire de pré-saisine en ligne. Après étude de votre demande, un étudiant clinicien
                        encadré prendra contact avec vous pour organiser la consultation.
                    </x-faq-item>

                    <x-faq-item question="Quels sont vos horaires de consultation ?">
                        Les permanences se tiennent du lundi au vendredi de 08h00 à 16h00, et le samedi de 08h00
                        à 12h00. La Clinique est fermée le dimanche et les jours fériés.
                    </x-faq-item>

                    <x-faq-item question="Puis-je vous contacter en dehors des heures d'ouverture ?">
                        Oui. Vous pouvez nous écrire à tout moment par email ou via le formulaire de contact de
                        cette page. Votre demande sera traitée dès la réouverture de la Clinique.
                    </x-faq-item>
                </div>
            </div>

            <div class="lg:col-span-2">
                <x-section-label>Autres moyens de contact</x-section-label>

                @php
                    $channels = [
                        ['key' => 'facebook', 'label' => 'Facebook', 'text' => 'Suivez nos actualités et événements', 'color' => 'text-[#1877F2]', 'path' => 'M22 12a10 10 0 10-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.1 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0022 12z'],
                        ['key' => 'youtube', 'label' => 'YouTube', 'text' => 'Regardez nos vidéos sur notre Web TV', 'color' => 'text-[#FF0000]', 'path' => 'M23 12s0-3.5-.45-5.17a2.98 2.98 0 00-2.1-2.1C18.78 4.3 12 4.3 12 4.3s-6.78 0-8.45.43a2.98 2.98 0 00-2.1 2.1C1 8.5 1 12 1 12s0 3.5.45 5.17a2.98 2.98 0 002.1 2.1C5.22 19.7 12 19.7 12 19.7s6.78 0 8.45-.43a2.98 2.98 0 002.1-2.1C23 15.5 23 12 23 12zM9.75 15.02V8.98L15.5 12l-5.75 3.02z'],
                        ['key' => 'linkedin', 'label' => 'LinkedIn', 'text' => 'Échangez avec notre communauté', 'color' => 'text-[#0A66C2]', 'path' => 'M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.03-1.85-3.03-1.85 0-2.14 1.45-2.14 2.94v5.66H9.34V9h3.41v1.56h.05c.47-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 110-4.13 2.06 2.06 0 010 4.13zM7.12 20.45H3.56V9h3.56v11.45z'],
                    ];
                @endphp

                <ul class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach ($channels as $channel)
                        @php $url = config('clinique.social.' . $channel['key']); @endphp
                        <li>
                            <a
                                href="{{ $url ?? '#' }}"
                                @if($url) target="_blank" rel="noopener noreferrer" @endif
                                class="flex h-full flex-col items-center rounded-xl border border-primary-100 bg-primary-50/60 p-5 text-center transition-colors hover:border-primary-200 hover:bg-primary-50"
                            >
                                <svg class="h-8 w-8 {{ $channel['color'] }}" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="{{ $channel['path'] }}" />
                                </svg>
                                <span class="mt-3 text-sm font-bold text-slate-900">{{ $channel['label'] }}</span>
                                <span class="mt-1 text-xs leading-snug text-slate-600">{{ $channel['text'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            title="Besoin d'une assistance rapide ?"
            button-label="Faire une pré-saisine"
            :button-href="route('presaisine.index')"
        >
            Vous pouvez également faire une pré-saisine en ligne pour être orienté plus rapidement.
        </x-inline-cta>
    </section>

@endsection
