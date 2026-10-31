{{--
    The shell for an error inside the administration or the registre.

    Deliberately standalone: it renders the same whether or not anyone is
    signed in, because that is exactly what an error page cannot know. It
    carries no navigation — an error is not the moment to offer the public
    site's menu to someone who was working in the CMS — only the way back
    to where they came from.
--}}
<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Erreur') - {{ config('app.name') }}</title>
    {{-- The bundled file, not the one in Paramètres: a 500 is raised when
         the database is the thing that failed, and this page must not ask
         it for a setting on the way to saying so. --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-full bg-slate-50">
    <main class="mx-auto flex min-h-dvh max-w-xl flex-col items-center justify-center px-4 py-12 text-center sm:px-6">
        <img
            src="{{ asset('images/brand/logo.png') }}"
            alt="Clinique Juridique FSJP - UAM"
            class="h-14 w-14 rounded-full object-cover"
            width="56"
            height="56"
        >
        @yield('content')
    </main>
</body>
</html>
