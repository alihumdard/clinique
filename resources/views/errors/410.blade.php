@php
    $title = "Ce document n'est plus disponible";

    // The sentence the application wrote at the abort() comes first:
    // abort(410, '...') already said the right thing in French, and the
    // framework's default page was throwing it away.
    $message = $exception?->getMessage() ?: "Le fichier a été retiré du serveur de réception. Ce n'est pas une panne : il a été déplacé là où il doit être conservé.";
@endphp

@extends(\App\Support\ErrorPage::layout())

@section('title', $title)

@section('content')
    <x-error-state status="410" icon="info" :title="$title" :message="$message">
        @include('errors.partials.actions')
    </x-error-state>
@endsection
