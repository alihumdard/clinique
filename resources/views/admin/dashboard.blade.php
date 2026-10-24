@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')

    {{-- ============ WELCOME ============ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold text-navy-900 sm:text-3xl">
                Bienvenue, {{ auth()->user()?->name ?? 'Administrateur' }}
                <span aria-hidden="true">&#128075;</span>
            </h1>
            <p class="mt-1.5 text-sm text-slate-600">
                Voici un aperçu des activités de la Clinique Juridique FSJP-UAM.
            </p>
        </div>

        <div class="flex shrink-0 items-center gap-3 rounded-xl border border-slate-200 bg-white px-5 py-3">
            <x-icon-badge size="sm"><x-icon name="calendar" class="h-5 w-5" /></x-icon-badge>
            <div class="leading-tight">
                <p class="text-sm font-semibold text-slate-900">{{ ucfirst(now()->translatedFormat('l j F Y')) }}</p>
                <p class="text-xs text-slate-500">{{ now()->format('H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- ============ KPIs ============ --}}
    {{-- Figures are illustrative until Module 2 supplies real counts. --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card icon="document"    color="blue"   :value="$kpis['articles']"   label="Actualités publiées" />
        <x-admin.stat-card icon="play-circle" color="green"  :value="$kpis['videos']"     label="Capsules Web TV" />
        <x-admin.stat-card icon="chat"        color="amber"  :value="$kpis['messages']"   label="Messages à traiter" note="formulaire de contact" />
        <x-admin.stat-card icon="calendar"    color="purple" :value="$kpis['activities']" label="Activités publiées" />
    </div>

    {{-- ============ CHARTS ============ --}}
    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-3">

        <section class="min-w-0 rounded-xl border border-slate-200 bg-white p-5 xl:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="chart-bar" class="h-5 w-5 text-primary-600" />
                    Statistiques des publications
                </h2>

                <p class="text-xs text-slate-500">6 derniers mois</p>
            </div>

            <div class="mt-5">
                <x-admin.line-chart
                    :labels="$months['labels']"
                    :series="$months['series']"
                />
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                <x-icon name="chart-pie" class="h-5 w-5 text-primary-600" />
                Répartition du contenu
            </h2>

            <div class="mt-5">
                @if(count($breakdown))
                    <x-admin.donut-chart total-label="Éléments" :items="$breakdown" />
                @else
                    <p class="py-8 text-center text-sm text-slate-500">
                        Aucun contenu publié pour le moment.
                    </p>
                @endif
            </div>
        </section>
    </div>

    {{-- ============ RECENT LISTS ============ --}}
    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-3">

        <section class="min-w-0 rounded-xl border border-slate-200 bg-white xl:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-5">
                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="document" class="h-5 w-5 text-primary-600" />
                    Dernières actualités
                </h2>

                <a href="{{ route('admin.news') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700">
                    Voir toutes
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            <p class="flex items-center gap-1.5 px-5 pt-1 text-xs text-slate-500 lg:hidden">
                <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0" />
                Faites glisser le tableau pour voir toutes les colonnes
            </p>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[42rem] text-left">
                    <thead>
                        <tr class="whitespace-nowrap border-b border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th scope="col" class="px-5 py-3">#</th>
                            <th scope="col" class="px-5 py-3">Titre</th>
                            <th scope="col" class="px-5 py-3">Catégorie</th>
                            <th scope="col" class="px-5 py-3">Statut</th>
                            <th scope="col" class="px-5 py-3">Date</th>
                            <th scope="col" class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latest as $article)
                            <tr class="align-middle hover:bg-slate-50">
                                <td class="px-5 py-3.5 align-middle text-sm text-slate-500">{{ $loop->iteration }}</td>

                                <td class="px-5 py-3.5 align-middle">
                                    <span class="block max-w-[20rem] truncate text-sm font-medium text-slate-900">
                                        {{ $article->title }}
                                    </span>
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    @if($article->category)
                                        <x-admin.badge :tone="$article->category->tone">{{ $article->category->name }}</x-admin.badge>
                                    @else
                                        <span class="text-sm text-slate-400">&mdash;</span>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    <x-admin.badge :tone="$article->statusTone()">{{ $article->statusLabel() }}</x-admin.badge>
                                </td>

                                <td class="whitespace-nowrap px-5 py-3.5 align-middle text-sm text-slate-500">
                                    {{ ($article->published_at ?? $article->created_at)->translatedFormat('j F Y') }}
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    <x-admin.row-actions
                                        :label="$article->title"
                                        :duplicate="false"
                                        :view-href="$article->isPublic() ? route('news.show', $article) : null"
                                        :delete-action="route('admin.news.destroy', $article)"
                                    />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center">
                                    <p class="text-sm text-slate-500">Aucune actualité enregistrée.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-5">
                <h2 class="flex items-center gap-2.5 text-base font-bold text-slate-900">
                    <x-icon name="chat" class="h-5 w-5 text-primary-600" />
                    Derniers messages
                </h2>

                <a href="{{ route('admin.messages') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700">
                    Voir tous
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            {{--
                The design had "Dernières candidatures" here. Nothing records
                an application: a call tells the applicant how to apply and
                they do so by e-mail or in person, so there is no table
                behind that panel. The contact inbox is the real stream of
                incoming requests and takes the slot.
            --}}
            <ul class="divide-y divide-slate-100">
                @forelse ($recentMessages as $message)
                    <li class="flex items-center gap-3 p-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600" aria-hidden="true">
                            {{ \Illuminate\Support\Str::of($message->name)->explode(' ')->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') }}
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $message->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $message->email }}</p>
                        </div>

                        <div class="shrink-0 text-right">
                            <p class="text-xs text-slate-500">{{ $message->created_at->translatedFormat('j M Y') }}</p>
                            @if($message->status === \App\Models\ContactMessage::STATUS_NEW)
                                <span class="mt-1 inline-block rounded bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">Nouveau</span>
                            @else
                                <span class="mt-1 inline-block rounded bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Traité</span>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="p-8 text-center">
                        <p class="text-sm text-slate-500">Aucun message reçu pour le moment.</p>
                    </li>
                @endforelse
            </ul>

            <div class="border-t border-slate-100 p-4">
                <p class="flex items-center gap-2 text-sm text-slate-600">
                    <x-icon name="mail" class="h-4 w-4 shrink-0 text-slate-400" />
                    {{ number_format($subscribers, 0, ',', ' ') }}
                    {{ $subscribers === 1 ? 'abonné' : 'abonnés' }} à la newsletter
                </p>
            </div>
        </section>
    </div>

@endsection
