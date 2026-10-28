@extends(\App\Support\ErrorPage::layout())

@section('title', 'Page introuvable - ' . config('app.name'))
@section('meta_description', "La page que vous recherchez n'existe pas ou a ete deplacee.")

@section('content')

    @php
        $inPlainShell = \App\Support\ErrorPage::layout() === 'layouts.error';
    @endphp

    @if($inPlainShell)
        {{-- Inside the administration or the registre. The public 404 is a
             designed page with the site's navigation on it, which is exactly
             what an administrator must not be handed when a CMS URL does not
             resolve: it ejected them from the area they were working in. --}}
        <x-error-state
            status="404"
            icon="search"
            title="Cette page n'existe pas"
            message="L'adresse demandée ne correspond à aucun écran. Elle a peut-être été supprimée, ou le lien est incomplet."
        >
            @include('errors.partials.actions')
        </x-error-state>
    @else
        @include('errors.partials.public-404')
    @endif

@endsection
