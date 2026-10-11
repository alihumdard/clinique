@extends('layouts.admin')

@section('title', 'Connexion')

@section('content')
    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-sm rounded-lg bg-white p-8 shadow">
            <h1 class="text-xl font-bold text-gray-900">Administration</h1>
            <p class="mt-1 text-sm text-gray-600">Clinique Juridique FSJP</p>

            {{-- Full auth logic (validation, rate limiting, roles) implemented in Module 2/3 --}}
            <form class="mt-6 space-y-4" method="POST" action="#">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Adresse e-mail</label>
                    <input type="email" id="email" name="email" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-600 focus:ring-primary-600 sm:text-sm">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <input type="password" id="password" name="password" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-600 focus:ring-primary-600 sm:text-sm">
                </div>
                <button type="submit"
                    class="w-full rounded-md bg-primary-700 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-800">
                    Se connecter
                </button>
            </form>
        </div>
    </div>
@endsection
