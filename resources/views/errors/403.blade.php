@php
    // Reached when an éditeur de contenu follows a link to an
    // administrator-only screen. The message says which it is — a
    // permission, not a missing page — so the person knows to ask for
    // access rather than to look for the screen elsewhere.
    $message = $exception?->getMessage() ?: "Votre compte n'a pas accès à cette section.";
@endphp

@extends(\App\Support\ErrorPage::layout())

@section('title', 'Accès refusé')

@section('content')
    <x-error-state status="403" icon="lock" title="Accès refusé" :message="$message">
        @include('errors.partials.actions')
    </x-error-state>
@endsection
