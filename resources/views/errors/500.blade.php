@php
    $title = 'Une erreur est survenue';

    // The sentence the application wrote at the abort() comes first:
    // abort(410, '...') already said the right thing in French, and the
    // framework's default page was throwing it away.
    $message = $exception?->getMessage() ?: 'Le problème a été consigné dans le journal du serveur. Merci de réessayer dans un instant.';
@endphp

@extends(\App\Support\ErrorPage::layout())

@section('title', $title)

@section('content')
    <x-error-state status="500" icon="help-circle" :title="$title" :message="$message">
        @include('errors.partials.actions')
    </x-error-state>
@endsection
