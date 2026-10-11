<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1d4ed8">
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('meta_description', 'Clinique Juridique de la FSJP - Universite Abdou Moumouni de Niamey. Consultations juridiques gratuites assurees par des etudiants encadres.')">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-white">
    <a href="#contenu-principal" class="skip-link">Aller au contenu principal</a>

    @include('partials.header')

    <main id="contenu-principal" class="flex-1">
        @yield('content')
    </main>

    @include('partials.footer')
</body>
</html>
