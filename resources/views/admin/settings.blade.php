@extends('layouts.admin')

@section('title', 'Paramètres du site')
@section('search_placeholder', 'Rechercher un paramètre, une information...')

@section('content')

    <x-admin.page-header
        title="Paramètres du site"
        lead="Gérez les informations générales, l'apparence et les configurations de votre site."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Paramètres du site'],
        ]"
    />

    {{--
        Presentation only: Module 2 wires these fields to CMS-backed site
        settings. The values shown come from config/clinique.php, which is
        the current single source of truth for the public site.
    --}}
    <form method="POST" action="#" class="mt-6">
        @csrf

        {{-- ============ SECTION TABS ============ --}}
        @php
            $tabs = [
                ['icon' => 'info',      'label' => 'Informations générales', 'active' => true],
                ['icon' => 'palette',   'label' => 'Apparence'],
                ['icon' => 'search',    'label' => 'SEO'],
                ['icon' => 'share',     'label' => 'Réseaux sociaux'],
                ['icon' => 'mail',      'label' => 'Contact'],
                ['icon' => 'mail',      'label' => 'Email'],
                ['icon' => 'window',    'label' => 'Pied de page'],
                ['icon' => 'sparkles',  'label' => 'Autres paramètres'],
            ];
        @endphp

        <div class="rounded-xl border border-slate-200 bg-white px-5">
            <div class="flex gap-6 overflow-x-auto">
                @foreach ($tabs as $tab)
                    @php $active = $tab['active'] ?? false; @endphp

                    <a
                        href="{{ url()->current() }}"
                        @if($active) aria-current="page" @endif
                        class="flex shrink-0 items-center gap-2 border-b-2 py-4 text-sm transition-colors {{ $active ? 'border-primary-600 font-semibold text-primary-600' : 'border-transparent font-medium text-slate-500 hover:text-primary-600' }}"
                    >
                        <x-icon :name="$tab['icon']" class="h-4 w-4 shrink-0" />
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ============ SETTINGS CARDS ============ --}}
        <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2 xl:grid-cols-3">

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
                            name="nom_site"
                            required
                            value="{{ config('clinique.name') }}"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="site-subtitle" class="block text-sm font-medium text-slate-700">Sous-titre</label>
                        <input
                            type="text"
                            id="site-subtitle"
                            name="sous_titre"
                            value="{{ config('clinique.faculty') }}"
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
                            name="description"
                            rows="3"
                            required
                            class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >La Clinique Juridique de la FSJP-UAM contribue à l'accès à la justice et à la promotion des droits humains à travers la formation, la sensibilisation et l'assistance juridique.</textarea>
                    </div>

                    <div>
                        <label for="site-language" class="block text-sm font-medium text-slate-700">Langue principale</label>
                        <select
                            id="site-language"
                            name="langue"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                            <option>Français</option>
                        </select>
                    </div>
                </div>
            </section>

            {{-- Logo and visual identity --}}
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-start gap-3">
                    <x-icon-badge size="sm" class="shrink-0"><x-icon name="image" class="h-5 w-5" /></x-icon-badge>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-slate-900">Logo et identité visuelle</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Gérez le logo, le favicon et l'identité visuelle.</p>
                    </div>
                </div>

                @php
                    $assets = [
                        ['key' => 'logo',    'label' => 'Logo principal', 'required' => true,  'hint' => 'PNG, SVG - Taille max : 2MB', 'accept' => 'image/png,image/svg+xml', 'box' => 'h-24 w-40'],
                        ['key' => 'favicon', 'label' => 'Favicon',        'required' => false, 'hint' => 'ICO, PNG - Taille max : 1MB', 'accept' => 'image/png,image/x-icon',  'box' => 'h-24 w-24'],
                    ];
                @endphp

                <div class="mt-5 space-y-5">
                    @foreach ($assets as $asset)
                        <div>
                            <span class="block text-sm font-medium text-slate-700">
                                {{ $asset['label'] }}
                                @if($asset['required'])
                                    <span class="text-rose-600" aria-hidden="true">*</span>
                                    <span class="sr-only">(obligatoire)</span>
                                @endif
                            </span>

                            <div class="mt-1.5 flex flex-wrap items-center gap-4">
                                <span class="flex {{ $asset['box'] }} shrink-0 items-center justify-center rounded-lg border-2 border-dashed border-slate-200 bg-slate-50 text-slate-400">
                                    <x-icon name="scale" class="h-8 w-8" />
                                </span>

                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <label
                                            for="asset-{{ $asset['key'] }}"
                                            class="cursor-pointer rounded-lg bg-primary-50 px-4 py-2 text-sm font-semibold text-primary-700 transition-colors hover:bg-primary-100"
                                        >
                                            Changer
                                            <input
                                                type="file"
                                                id="asset-{{ $asset['key'] }}"
                                                name="{{ $asset['key'] }}"
                                                accept="{{ $asset['accept'] }}"
                                                class="sr-only"
                                            >
                                        </label>

                                        <button type="button" class="rounded-lg p-2 text-rose-600 transition-colors hover:bg-rose-50">
                                            <x-icon name="trash" class="h-4 w-4" />
                                            <span class="sr-only">Supprimer le {{ mb_strtolower($asset['label']) }}</span>
                                        </button>
                                    </div>

                                    <p class="mt-1.5 text-xs text-slate-400">{{ $asset['hint'] }}</p>
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
                            <textarea
                                id="contact-address"
                                name="adresse"
                                rows="2"
                                class="w-full resize-y rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >{{ config('clinique.contact.address.line2') }}, {{ config('clinique.contact.address.line1') }}, {{ config('clinique.contact.address.city') }}, {{ config('clinique.contact.address.country') }}</textarea>
                        </div>
                    </div>

                    @php
                        $contactFields = [
                            ['id' => 'contact-phone', 'name' => 'telephone', 'label' => 'Téléphone', 'type' => 'tel',   'icon' => 'phone', 'value' => config('clinique.contact.phone.display')],
                            ['id' => 'contact-email', 'name' => 'email',     'label' => 'Email',     'type' => 'email', 'icon' => 'mail',  'value' => config('clinique.contact.email')],
                            ['id' => 'contact-hours', 'name' => 'horaires',  'label' => 'Horaires',  'type' => 'text',  'icon' => 'clock', 'value' => config('clinique.hours.days').' : '.config('clinique.hours.time')],
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
                                    value="{{ $field['value'] }}"
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
                                name="social[{{ $network['key'] }}]"
                                value="{{ config('clinique.social.'.$network['key']) }}"
                                placeholder="{{ $network['placeholder'] }}"
                                class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Appearance --}}
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-start gap-3">
                    <x-icon-badge size="sm" class="shrink-0"><x-icon name="palette" class="h-5 w-5" /></x-icon-badge>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-slate-900">Apparence du site</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Couleurs principales et paramètres d'affichage.</p>
                    </div>
                </div>

                @php
                    $colours = [
                        ['id' => 'colour-primary',   'name' => 'couleur_principale', 'label' => 'Couleur principale', 'value' => '#2563EB'],
                        ['id' => 'colour-secondary', 'name' => 'couleur_secondaire', 'label' => 'Couleur secondaire', 'value' => '#0F2847'],
                    ];
                @endphp

                <div class="mt-5 space-y-4">
                    @foreach ($colours as $colour)
                        <div>
                            <label for="{{ $colour['id'] }}" class="block text-sm font-medium text-slate-700">{{ $colour['label'] }}</label>

                            <div class="mt-1.5 flex items-center gap-3">
                                {{-- Native colour picker: no script, and it is the
                                     same control screen readers already announce. --}}
                                <input
                                    type="color"
                                    id="{{ $colour['id'] }}"
                                    name="{{ $colour['name'] }}"
                                    value="{{ $colour['value'] }}"
                                    class="h-10 w-12 shrink-0 cursor-pointer rounded-lg border border-slate-200 bg-white p-1"
                                >

                                <label for="{{ $colour['id'] }}-hex" class="sr-only">{{ $colour['label'] }} (code hexadécimal)</label>
                                <input
                                    type="text"
                                    id="{{ $colour['id'] }}-hex"
                                    name="{{ $colour['name'] }}_hex"
                                    value="{{ $colour['value'] }}"
                                    pattern="#[0-9A-Fa-f]{6}"
                                    class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3.5 py-2.5 font-mono text-sm uppercase text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                >
                            </div>
                        </div>
                    @endforeach

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="site-font" class="block text-sm font-medium text-slate-700">Police principale</label>
                            <select
                                id="site-font"
                                name="police"
                                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                                <option>Inter</option>
                                <option>Police système</option>
                            </select>
                        </div>

                        <div>
                            <label for="site-theme" class="block text-sm font-medium text-slate-700">Mode d'affichage</label>
                            <div class="relative mt-1.5">
                                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                    <x-icon name="sun" class="h-4 w-4" />
                                </span>
                                <select
                                    id="site-theme"
                                    name="theme"
                                    class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-9 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                >
                                    <option>Clair</option>
                                    <option>Sombre</option>
                                    <option>Automatique</option>
                                </select>
                            </div>
                        </div>
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
                            name="seo_titre"
                            value="Clinique Juridique FSJP - UAM | Accès à la justice"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="seo-description" class="block text-sm font-medium text-slate-700">Description SEO</label>
                        <textarea
                            id="seo-description"
                            name="seo_description"
                            rows="3"
                            maxlength="160"
                            class="mt-1.5 w-full resize-y rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        >La Clinique Juridique de la FSJP-UAM contribue à l'accès à la justice, la formation et la promotion des droits humains au Niger.</textarea>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-slate-700">Image par défaut pour le partage</span>

                        <div class="mt-1.5 flex flex-wrap items-center gap-4">
                            <span class="flex h-16 w-24 shrink-0 items-center justify-center rounded-lg border-2 border-dashed border-slate-200 bg-slate-50 text-slate-400">
                                <x-icon name="image" class="h-6 w-6" />
                            </span>

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
                                        accept="image/jpeg,image/png"
                                        class="sr-only"
                                    >
                                </label>
                                <p class="mt-1.5 text-xs text-slate-400">JPG, PNG - Taille max : 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- ============ ACTIONS ============ --}}
        <div class="mt-5 flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-5 sm:flex-row sm:items-center sm:justify-between">

            {{-- Reset is a submit, not a link: it changes stored settings
                 and must carry the CSRF token. --}}
            <button
                type="submit"
                name="action"
                value="reset"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-50"
            >
                <x-icon name="refresh" class="h-4 w-4" />
                Réinitialiser les paramètres
            </button>

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
