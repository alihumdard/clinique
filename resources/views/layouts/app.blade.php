<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1d4ed8">

    @php
        // The favicon, the sharing image and the two defaults below are
        // editable from Paramètres du site; each falls back to the bundled
        // file or to config/clinique.php.
        $favicon = \App\Support\SiteSettings::imageUrl('site.favicon');
        $shareImage = \App\Support\SiteSettings::imageUrl('seo.image');
        $metaTitle = trim($__env->yieldContent('title', config('clinique.seo.title')));
        $metaDescription = trim($__env->yieldContent('meta_description', config('clinique.seo.description')));
    @endphp

    <link rel="icon" href="{{ $favicon }}">
    <link rel="apple-touch-icon" href="{{ $favicon }}">

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph and Twitter, so a shared link carries a title, a summary
         and an image rather than a bare URL. The share buttons on the
         actualités and fiches pages rely on these. --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('clinique.name') }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta property="og:locale" content="fr_FR">
    <meta name="twitter:card" content="summary_large_image">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-white">
    <a href="#contenu-principal" class="skip-link">Aller au contenu principal</a>

    {{-- The bandeau d'état sits above the header, so it is the first thing
         read on the page and is never scrolled past by a sticky header. --}}
    <x-status-banner />

    @include('partials.header')

    <main id="contenu-principal" class="flex-1">
        @yield('content')
    </main>

    @include('partials.footer')
    @stack('scripts')
</body>
</html>
