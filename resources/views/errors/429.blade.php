@php
    $title = 'Trop de tentatives';

    // The sentence the application wrote at the abort() comes first:
    // abort(410, '...') already said the right thing in French, and the
    // framework's default page was throwing it away.
    $message = $exception?->getMessage() ?: 'Merci de patienter quelques minutes avant de réessayer.';
@endphp

@extends(\App\Support\ErrorPage::layout())

@section('title', $title)

@section('content')
    <x-error-state status="429" icon="shield" :title="$title" :message="$message">
        @include('errors.partials.actions')
    </x-error-state>
@endsection
