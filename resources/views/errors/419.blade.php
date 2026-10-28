@php
    $title = 'Votre session a expiré';

    // The sentence the application wrote at the abort() comes first:
    // abort(410, '...') already said the right thing in French, and the
    // framework's default page was throwing it away.
    $message = $exception?->getMessage() ?: "Par sécurité, le formulaire a expiré avant d'être envoyé. Revenez à la page précédente et renvoyez-le.";
@endphp

@extends(\App\Support\ErrorPage::layout())

@section('title', $title)

@section('content')
    <x-error-state status="419" icon="clock" :title="$title" :message="$message">
        @include('errors.partials.actions')
    </x-error-state>
@endsection
