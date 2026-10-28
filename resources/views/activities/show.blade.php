@extends('layouts.app')

@section('title', $activity->title . ' - ' . config('app.name'))
@section('meta_description', str(strip_tags($activity->excerpt ?: $activity->body ?: ''))->limit(150))

@section('content')

    {{-- ============ ACTIVITY HERO ============ --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0">
            <x-stored-image
                :path="$activity->image"
                alt=""
                width="1600" height="500"
                sizes="100vw"
                :eager="true"
                class="h-full w-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-navy-950/90 via-navy-900/70 to-navy-900/20"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <nav aria-label="Fil d'Ariane" class="mb-4 flex flex-wrap items-center gap-2 text-sm text-slate-300">
                <a href="{{ route('home') }}" class="hover:text-white">Accueil</a>
                <span aria-hidden="true">&rsaquo;</span>
                <a href="{{ route('activities.index') }}" class="hover:text-white">{{ __('Activités et caravanes juridiques') }}</a>
                <span aria-hidden="true">&rsaquo;</span>
                <span class="text-white">{{ $activity->title }}</span>
            </nav>

            @if($activity->category)
                <x-category-badge position="inline" :color="$activity->category->tone ?? 'primary'" class="mb-4">
                    {{ $activity->category->name }}
                </x-category-badge>
            @endif

            <h1 class="max-w-2xl text-3xl font-bold leading-tight text-white sm:text-4xl">
                {{ $activity->title }}
            </h1>

            <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-1.5 text-sm text-slate-200">
                @if($activity->held_on)
                    <span class="inline-flex items-center gap-1.5">
                        <x-icon name="calendar" class="h-4 w-4 shrink-0" />
                        {{ $activity->held_on->translatedFormat('j F Y') }}
                    </span>
                @endif
                @if($activity->location)
                    <span class="inline-flex items-center gap-1.5">
                        <x-icon name="map-pin" class="h-4 w-4 shrink-0" />
                        {{ $activity->location }}
                    </span>
                @endif
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-start gap-10 lg:grid-cols-3">

            {{-- ============ ACTIVITY BODY ============ --}}
            <div class="lg:col-span-2">
                @if(filled($activity->excerpt))
                    <p class="text-base font-medium leading-relaxed text-slate-700">{{ $activity->excerpt }}</p>
                @endif

                @if(filled($activity->body))
                    <div class="mt-6 whitespace-pre-line text-base leading-relaxed text-slate-700">{{ $activity->body }}</div>
                @endif

                @if($activity->photos->isNotEmpty())
                    <div class="mt-10 space-y-5">
                        <x-section-label>Photos de l'activité</x-section-label>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach ($activity->photos as $photo)
                                <a href="{{ $photo->url() }}" target="_blank" rel="noopener noreferrer" class="block overflow-hidden rounded-lg">
                                    <img src="{{ $photo->url() }}" alt="{{ $photo->caption ?: $activity->title }}" loading="lazy" class="aspect-square w-full object-cover transition-transform duration-300 hover:scale-105">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($related->isNotEmpty())
                    <div class="mt-12 space-y-5">
                        <x-section-label>Autres activités de cette rubrique</x-section-label>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            @foreach ($related as $item)
                                <x-activity-card
                                    :stored-path="$item->image"
                                    :category="$item->category?->name"
                                    category-color="primary"
                                    :date="$item->held_on?->translatedFormat('j F Y')"
                                    :location="$item->location"
                                    :title="$item->title"
                                    :excerpt="$item->excerpt"
                                    :href="route('activities.show', $item)"
                                />
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6">
                <x-sidebar-card icon="clock" title="Activités récentes" color="amber" :tint="true">
                    <div class="space-y-4">
                        @forelse ($recent as $item)
                            <x-mini-article-row
                                :stored-path="$item->image"
                                :title="$item->title"
                                :date="$item->held_on?->translatedFormat('j F Y')"
                                :href="route('activities.show', $item)"
                            />
                        @empty
                            <p class="text-sm text-slate-500">Aucune autre activité pour le moment.</p>
                        @endforelse
                    </div>
                </x-sidebar-card>
            </aside>
        </div>
    </section>

    {{-- ============ CTA (dark) ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            :title="__('Vous souhaitez organiser une activité dans votre localité ?')"
            :button-label="__('Nous contacter')"
            :button-href="route('contact')"
        >
            Contactez-nous pour planifier une caravane juridique ou une session de sensibilisation.
        </x-inline-cta>
    </section>

@endsection
