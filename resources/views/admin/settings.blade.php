@extends('layouts.admin')

@section('title', 'Paramètres du site')

@section('content')

    <x-admin.page-header
        title="Paramètres du site"
        lead="Gérez les informations générales, l'apparence et les configurations de votre site."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Paramètres du site'],
        ]"
    />

    <x-admin.flash />

    {{--
        The fields below are the CMS-editable half of config/clinique.php.
        Every value shown is what the site is currently using: the stored
        setting where the Clinic has filled one in, and the file's default
        where they have not. Emptying a field restores the default rather
        than blanking the site.
    --}}
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @method('PUT')

        {{-- ============ SETTINGS CARDS ============ --}}
        <div class="mt-5 grid grid-cols-1 items-start gap-5 lg:grid-cols-2 xl:grid-cols-3">

            {{-- General information --}}
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-start gap-3">
                    <x-icon-badge size="sm" class="shrink-0"><x-icon name="building" class="h-5 w-5" /></x-icon-badge>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-slate-900">Informations générales</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Informations principales de la Clinique Juridique FSJP-UAM.</p>
                    </div>
                </div>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="site-name" class="block text-sm font-medium text-slate-700">
                            Nom du site
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <input
                            type="text"
                            id="site-name"
                            name="site_name"
                            required
                            value="{{ old('site_name', config('clinique.name')) }}"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="site-subtitle" class="block text-sm font-medium text-slate-700">Sous-titre</label>
                        <input
                            type="text"
                            id="site-subtitle"
                            name="site_faculty"
                            value="{{ old('site_faculty', config('clinique.faculty')) }}"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="site-description" class="block text-sm font-medium text-slate-700">
                            Description courte
                            <span class="text-rose-600" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatoire)</span>
                        </label>
                        <textarea
                            id="site-description"
                            name="site_description"
                            rows="3"
                            required
                            class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >{{ old('site_description', config('clinique.description')) }}</textarea>
                    </div>

                    <div>
                        <label for="site-university" class="block text-sm font-medium text-slate-700">Université</label>
                        <input
                            type="text"
                            id="site-university"
                            name="site_university"
                            value="{{ old('site_university', config('clinique.university')) }}"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    {{-- The site is French-only by plan, so there is no
                         language to choose; a select with one option is not
                         a choice, it is a label. --}}
                    <p class="text-xs text-slate-500">
                        Langue du site : français.
                    </p>
                </div>
            </section>

            {{-- Logo and visual identity --}}
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-start gap-3">
                    <x-icon-badge size="sm" class="shrink-0"><x-icon name="image" class="h-5 w-5" /></x-icon-badge>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-slate-900">Logo et identité visuelle</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Logo affiché dans l'en-tête et favicon de l'onglet.</p>
                    </div>
                </div>

                @php
                    $assets = [
                        ['field' => 'logo',    'key' => 'site.logo',    'label' => 'Logo principal', 'box' => 'h-24 w-24'],
                        ['field' => 'favicon', 'key' => 'site.favicon', 'label' => 'Favicon',        'box' => 'h-24 w-24'],
                    ];
                @endphp

                <div class="mt-5 space-y-5">
                    @foreach ($assets as $asset)
                        <div>
                            <span class="block text-sm font-medium text-slate-700">{{ $asset['label'] }}</span>

                            <div class="mt-1.5 flex flex-wrap items-center gap-4">
                                <img
                                    src="{{ \App\Support\SiteSettings::imageUrl($asset['key']) }}"
                                    alt=""
                                    class="{{ $asset['box'] }} shrink-0 rounded-lg border border-slate-200 bg-white object-contain p-1"
                                >

                                <div class="min-w-0">
                                    <label
                                        for="asset-{{ $asset['field'] }}"
                                        class="inline-block cursor-pointer rounded-lg bg-primary-50 px-4 py-2 text-sm font-semibold text-primary-700 transition-colors hover:bg-primary-100"
                                    >
                                        Changer
                                        <input
                                            type="file"
                                            id="asset-{{ $asset['field'] }}"
                                            name="{{ $asset['field'] }}"
                                            accept="image/png,image/jpeg,image/webp"
                                            class="sr-only"
                                        >
                                    </label>

                                    @if (! blank($values[$asset['key']] ?? null))
                                        <label class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                            <input
                                                type="checkbox"
                                                name="remove[]"
                                                value="{{ $asset['field'] }}"
                                                class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500"
                                            >
                                            Revenir à l'image par défaut
                                        </label>
                                    @endif

                                    <p class="mt-1.5 text-xs text-slate-400">PNG, JPG ou WebP - Max 1MB</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Contact details --}}
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-start gap-3">
                    <x-icon-badge size="sm" class="shrink-0"><x-icon name="phone" class="h-5 w-5" /></x-icon-badge>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-slate-900">Coordonnées de contact</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Informations de contact affichées sur le site.</p>
                    </div>
                </div>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="contact-address" class="block text-sm font-medium text-slate-700">Adresse</label>
                        <div class="relative mt-1.5">
                            <span class="pointer-events-none absolute left-3.5 top-3 text-slate-400">
                                <x-icon name="map-pin" class="h-4 w-4" />
                            </span>
                            <input
                                type="text"
                                id="contact-address"
                                name="contact_address_line1"
                                value="{{ old('contact_address_line1', config('clinique.contact.address.line1')) }}"
                                placeholder="Faculté des Sciences Juridiques et Politiques"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                        </div>
                    </div>

                    @php
                        $contactFields = [
                            ['id' => 'contact-line2',   'name' => 'contact_address_line2',   'label' => 'Adresse (2e ligne)', 'type' => 'text',  'icon' => 'map-pin', 'value' => config('clinique.contact.address.line2')],
                            ['id' => 'contact-city',    'name' => 'contact_address_city',    'label' => 'Ville',              'type' => 'text',  'icon' => 'map-pin', 'value' => config('clinique.contact.address.city')],
                            ['id' => 'contact-country', 'name' => 'contact_address_country', 'label' => 'Pays',               'type' => 'text',  'icon' => 'globe',   'value' => config('clinique.contact.address.country')],
                            ['id' => 'contact-phone',   'name' => 'contact_phone',           'label' => 'Téléphone affiché',  'type' => 'tel',   'icon' => 'phone',   'value' => config('clinique.contact.phone.display')],
                            ['id' => 'contact-tel',     'name' => 'contact_phone_href',      'label' => 'Téléphone (format international, pour les liens)', 'type' => 'tel', 'icon' => 'phone', 'value' => config('clinique.contact.phone.tel')],
                            ['id' => 'contact-email',   'name' => 'contact_email',           'label' => 'Email',              'type' => 'email', 'icon' => 'mail',    'value' => config('clinique.contact.email')],
                            ['id' => 'hours-days',      'name' => 'hours_days',              'label' => 'Jours d\'ouverture', 'type' => 'text',  'icon' => 'calendar','value' => config('clinique.hours.days')],
                            ['id' => 'hours-time',      'name' => 'hours_time',              'label' => 'Horaires',           'type' => 'text',  'icon' => 'clock',   'value' => config('clinique.hours.time')],
                            ['id' => 'hours-note',      'name' => 'hours_note',              'label' => 'Note sur les horaires', 'type' => 'text', 'icon' => 'info',  'value' => config('clinique.hours.note')],
                        ];
                    @endphp

                    @foreach ($contactFields as $field)
                        <div>
                            <label for="{{ $field['id'] }}" class="block text-sm font-medium text-slate-700">{{ $field['label'] }}</label>
                            <div class="relative mt-1.5">
                                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                    <x-icon :name="$field['icon']" class="h-4 w-4" />
                                </span>
                                <input
                                    type="{{ $field['type'] }}"
                                    id="{{ $field['id'] }}"
                                    name="{{ $field['name'] }}"
                                    value="{{ old($field['name'], $field['value']) }}"
                                    class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                >
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Social networks --}}
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-start gap-3">
                    <x-icon-badge size="sm" class="shrink-0"><x-icon name="share" class="h-5 w-5" /></x-icon-badge>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-slate-900">Réseaux sociaux</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Liens vers les réseaux sociaux.</p>
                    </div>
                </div>

                @php
                    $networks = [
                        ['key' => 'facebook',  'label' => 'Facebook',  'colour' => 'bg-[#1877F2]', 'path' => 'M22 12a10 10 0 10-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.1 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0022 12z', 'placeholder' => 'https://www.facebook.com/...'],
                        ['key' => 'x',         'label' => 'X',         'colour' => 'bg-slate-900',  'path' => 'M18.9 2H22l-7.4 8.4L23 22h-6.8l-5.3-6.9L4.8 22H1.7l7.9-9L1 2h7l4.8 6.3L18.9 2zm-1.2 18h1.9L7.3 3.9H5.3L17.7 20z', 'placeholder' => 'https://twitter.com/...'],
                        ['key' => 'linkedin',  'label' => 'LinkedIn',  'colour' => 'bg-[#0A66C2]', 'path' => 'M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.03-1.85-3.03-1.85 0-2.14 1.45-2.14 2.94v5.66H9.34V9h3.41v1.56h.05c.47-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 110-4.13 2.06 2.06 0 010 4.13zM7.12 20.45H3.56V9h3.56v11.45z', 'placeholder' => 'https://www.linkedin.com/company/...'],
                        ['key' => 'youtube',   'label' => 'YouTube',   'colour' => 'bg-[#FF0000]', 'path' => 'M23 12s0-3.5-.45-5.17a2.98 2.98 0 00-2.1-2.1C18.78 4.3 12 4.3 12 4.3s-6.78 0-8.45.43a2.98 2.98 0 00-2.1 2.1C1 8.5 1 12 1 12s0 3.5.45 5.17a2.98 2.98 0 002.1 2.1C5.22 19.7 12 19.7 12 19.7s6.78 0 8.45-.43a2.98 2.98 0 002.1-2.1C23 15.5 23 12 23 12zM9.75 15.02V8.98L15.5 12l-5.75 3.02z', 'placeholder' => 'https://www.youtube.com/@...'],
                        ['key' => 'instagram', 'label' => 'Instagram', 'colour' => 'bg-[#E4405F]', 'path' => 'M12 2c2.72 0 3.06.01 4.12.06 1.06.05 1.79.22 2.43.47.66.26 1.21.6 1.76 1.15.5.5.9 1.1 1.15 1.76.25.64.42 1.37.47 2.43C21.99 8.94 22 9.28 22 12s-.01 3.06-.06 4.12c-.05 1.06-.22 1.79-.47 2.43a4.9 4.9 0 01-1.15 1.76 4.9 4.9 0 01-1.76 1.15c-.64.25-1.37.42-2.43.47C15.06 21.99 14.72 22 12 22s-3.06-.01-4.12-.06c-1.06-.05-1.79-.22-2.43-.47a4.9 4.9 0 01-1.76-1.15 4.9 4.9 0 01-1.15-1.76c-.25-.64-.42-1.37-.47-2.43C2.01 15.06 2 14.72 2 12s.01-3.06.06-4.12c.05-1.06.22-1.79.47-2.43.26-.66.6-1.21 1.15-1.76a4.9 4.9 0 011.76-1.15c.64-.25 1.37-.42 2.43-.47C8.94 2.01 9.28 2 12 2zm0 5a5 5 0 100 10 5 5 0 000-10zm0 8.2a3.2 3.2 0 110-6.4 3.2 3.2 0 010 6.4zm5.2-8.4a1.2 1.2 0 100-2.4 1.2 1.2 0 000 2.4z', 'placeholder' => 'https://www.instagram.com/...'],
                    ];
                @endphp

                <div class="mt-5 space-y-3">
                    @foreach ($networks as $network)
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $network['colour'] }} text-white">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="{{ $network['path'] }}" />
                                </svg>
                            </span>

                            <label for="social-{{ $network['key'] }}" class="sr-only">Lien {{ $network['label'] }}</label>
                            <input
                                type="url"
                                id="social-{{ $network['key'] }}"
                                name="social_{{ $network['key'] }}"
                                value="{{ old('social_'.$network['key'], config('clinique.social.'.$network['key'])) }}"
                                placeholder="{{ $network['placeholder'] }}"
                                class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                        </div>
                    @endforeach
                </div>
            </section>

            {{--
                Bandeau d'état. This card replaces the design's colour and
                font pickers: the palette is fixed by the design system and
                is compiled into the stylesheet, so a colour chosen here
                could not take effect — whereas the bandeau is a plan
                requirement that had no screen of its own.
            --}}
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-start gap-3">
                    <x-icon-badge size="sm" class="shrink-0"><x-icon name="bell" class="h-5 w-5" /></x-icon-badge>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-slate-900">Bandeau d'information</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Message affiché en haut de toutes les pages du site.</p>
                    </div>
                </div>

                <div class="mt-5 space-y-4">
                    <x-admin.toggle
                        name="banner_enabled"
                        label="Afficher le bandeau"
                        :checked="(bool) old('banner_enabled', $values['banner.enabled'] ?? false)"
                    />

                    <div>
                        <label for="banner-message" class="block text-sm font-medium text-slate-700">Message</label>
                        <textarea
                            id="banner-message"
                            name="banner_message"
                            rows="3"
                            maxlength="300"
                            aria-describedby="banner-message-hint"
                            placeholder="Ex. La Clinique sera fermée du 24 au 28 décembre."
                            class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >{{ old('banner_message', $values['banner.message'] ?? '') }}</textarea>
                        <p id="banner-message-hint" class="mt-1.5 text-xs text-slate-500">
                            Fermeture exceptionnelle, changement d'horaires, information urgente.
                        </p>
                    </div>

                    <div>
                        <label for="banner-tone" class="block text-sm font-medium text-slate-700">Type de message</label>
                        <select
                            id="banner-tone"
                            name="banner_tone"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            @php
                                $tones = [
                                    'info' => 'Information (bleu)',
                                    'warning' => 'Avertissement (ambre)',
                                    'closure' => 'Fermeture (rouge)',
                                ];
                            @endphp
                            @foreach ($tones as $value => $label)
                                <option value="{{ $value }}" @selected(old('banner_tone', $values['banner.tone'] ?? 'info') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- The strip on the home page. It is separate from the
                         notice above: that one is a message the Clinic
                         writes, this one is whether the permanence is
                         receiving today. It used to be written into the
                         template as "ouvertes", every day of the year. --}}
                    <div class="sm:col-span-2">
                        <label for="service-state" class="block text-sm font-medium text-slate-700">
                            État des permanences
                        </label>
                        <select
                            id="service-state"
                            name="service_state"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            @php
                                $serviceStates = [
                                    'open' => 'Permanences ouvertes',
                                    'closed' => 'Permanences fermées',
                                    'notice' => 'Information importante',
                                ];
                            @endphp
                            @foreach ($serviceStates as $value => $label)
                                <option value="{{ $value }}" @selected(old('service_state', $values['service.state'] ?? 'open') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1.5 text-xs text-slate-500">
                            Affiché en haut de la page d'accueil, avec les horaires.
                        </p>
                    </div>
                </div>
            </section>

            {{-- SEO --}}
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-start gap-3">
                    <x-icon-badge size="sm" class="shrink-0"><x-icon name="search" class="h-5 w-5" /></x-icon-badge>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-slate-900">SEO et métadonnées</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Paramètres pour le référencement.</p>
                    </div>
                </div>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="seo-title" class="block text-sm font-medium text-slate-700">Titre SEO</label>
                        <input
                            type="text"
                            id="seo-title"
                            name="seo_title"
                            maxlength="70"
                            value="{{ old('seo_title', config('clinique.seo.title')) }}"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="seo-description" class="block text-sm font-medium text-slate-700">Description SEO</label>
                        <textarea
                            id="seo-description"
                            name="seo_description"
                            rows="3"
                            maxlength="300"
                            class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >{{ old('seo_description', config('clinique.seo.description')) }}</textarea>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-slate-700">Image par défaut pour le partage</span>

                        <div class="mt-1.5 flex flex-wrap items-center gap-4">
                            <img
                                src="{{ \App\Support\SiteSettings::imageUrl('seo.image') }}"
                                alt=""
                                class="h-16 w-24 shrink-0 rounded-lg border border-slate-200 bg-white object-contain p-1"
                            >

                            <div class="min-w-0">
                                <label
                                    for="seo-image"
                                    class="inline-block cursor-pointer rounded-lg bg-primary-50 px-4 py-2 text-sm font-semibold text-primary-700 transition-colors hover:bg-primary-100"
                                >
                                    Choisir une image
                                    <input
                                        type="file"
                                        id="seo-image"
                                        name="seo_image"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="sr-only"
                                    >
                                </label>

                                @if (! blank($values['seo.image'] ?? null))
                                    <label class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                        <input
                                            type="checkbox"
                                            name="remove[]"
                                            value="seo_image"
                                            class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500"
                                        >
                                        Revenir à l'image par défaut
                                    </label>
                                @endif

                                <p class="mt-1.5 text-xs text-slate-400">
                                    Utilisée quand un lien du site est partagé. JPG, PNG - Max 1MB
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- ============ ACTIONS ============ --}}
        <div class="mt-5 flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-5 sm:flex-row sm:items-center sm:justify-between">

            {{-- Emptying a field already restores its default, so there is
                 no separate reset to get wrong. --}}
            <p class="text-xs leading-relaxed text-slate-500 sm:max-w-sm">
                Un champ laissé vide reprend sa valeur par défaut.
            </p>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <a
                    href="{{ route('home') }}"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-50"
                >
                    <x-icon name="eye" class="h-4 w-4" />
                    Aperçu du site
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-primary-700"
                >
                    <x-icon name="save" class="h-4 w-4" />
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>

@endsection
