@php
    $title = 'Maintenance en cours';

    // The sentence the application wrote at the abort() comes first:
    // abort(410, '...') already said the right thing in French, and the
    // framework's default page was throwing it away.
    $message = $exception?->getMessage() ?: 'La plateforme est momentanément indisponible. Merci de réessayer dans quelques minutes.';
@endphp

@extends(\App\Support\ErrorPage::layout())

@section('title', $title)

@section('content')
    <x-error-state status="503" icon="refresh" :title="$title" :message="$message">
        @include('errors.partials.actions')
    </x-error-state>
@endsection
