<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- The administration area must never be indexed. --}}
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Administration') - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50">

{{--
    The sidebar is position:fixed, so it sits outside the page flow: it
    takes its height from the viewport, not from however long the content
    happens to be. Nesting it in a flex row made it as tall as the page and
    left its scroll area with no real height, which clipped the nav.
--}}
@include('admin.partials.sidebar')

{{-- lg:pl-64 clears the fixed sidebar on desktop --}}
<div class="flex min-h-screen flex-col lg:pl-64">

    @include('admin.partials.topbar')

    <main id="admin-content" class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
        @yield('content')
    </main>
</div>

</body>
</html>
