<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Confidential area: never indexed, and no referrer leaks out. --}}
    <meta name="robots" content="noindex, nofollow">
    <meta name="referrer" content="no-referrer">
    <title>@yield('title', 'Registre numérique') - {{ config('app.name') }}</title>

    <link rel="icon" href="{{ \App\Support\SiteSettings::imageUrl('site.favicon') }}">
    <link rel="apple-touch-icon" href="{{ \App\Support\SiteSettings::imageUrl('site.favicon') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{--
    admin-shell, deliberately: the registre is the same product as the
    administration and carries the same shell, the same spacing and the
    same components. It was built with a palette of its own, and that
    was the wrong call — beside the rest of the site it read as a
    different, less finished piece of software. What marks it as the
    confidential area is now said in words, in the sidebar and the
    topbar, rather than by looking unlike everything else.
--}}
<body class="admin-shell min-h-screen bg-slate-50">

@include('registre.partials.sidebar')

{{-- lg:pl-64 clears the fixed sidebar on desktop --}}
<div class="flex min-h-screen flex-col lg:pl-64">

    @include('registre.partials.topbar')

    <main id="admin-content" class="min-w-0 flex-1 px-4 py-6 sm:px-6 lg:px-8">

        @if(session('registre_message'))
            <p role="status" class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
                {{ session('registre_message') }}
            </p>
        @endif

        @if($errors->any())
            <div role="alert" class="mb-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <x-icon name="x-circle" class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />
                <ul class="min-w-0 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>

{{-- The registre uses the administration's components deliberately, and
     its confirmations are the same question in the same place. --}}
@include('admin.partials.confirm-dialog')

</body>
</html>
