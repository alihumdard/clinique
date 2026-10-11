@extends('layouts.app')

@section('title', 'Page introuvable - ' . config('app.name'))

@section('content')
    <section class="mx-auto flex max-w-2xl flex-col items-center px-4 py-24 text-center sm:px-6 lg:px-8">
        <h1 class="text-6xl font-bold text-primary-700">404</h1>
        <p class="mt-4 text-lg font-semibold text-gray-900">Page introuvable</p>
        <p class="mt-2 text-sm text-gray-600">
            La page que vous recherchez n'existe pas ou a ete deplacee.
        </p>
        <a href="{{ route('home') }}" class="mt-8 inline-flex items-center rounded-md bg-primary-700 px-6 py-3 text-sm font-semibold text-white hover:bg-primary-800">
            Retour a l'accueil
        </a>
    </section>
@endsection
