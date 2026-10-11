<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- The administration area must never be indexed. --}}
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Administration') - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full overflow-x-hidden bg-slate-50">

<div class="flex min-h-full">

    @include('admin.partials.sidebar')

    {{-- lg:pl-64 clears the fixed sidebar on desktop --}}
    <div class="flex min-w-0 flex-1 flex-col lg:pl-64">

        @include('admin.partials.topbar')

        <main id="admin-content" class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
