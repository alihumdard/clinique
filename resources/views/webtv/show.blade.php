@extends('layouts.app')

@section('title', $video->title . ' - Web TV - ' . config('app.name'))
@section('meta_description', $video->summary ?? "Une capsule video de la Clinique Juridique FSJP-UAM.")

@section('content')

    <x-page-hero
        image="placeholders/webtv-show-hero"
        image-position="object-right"
        :title="$video->title"
        :subtitle="$video->summary"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Web TV', 'url' => route('webtv.index')],
            ['label' => $video->title],
        ]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">

            {{-- ============ MAIN ============ --}}
            <div class="lg:col-span-2">

                <x-video-player
                    :poster-url="$video->coverUrl()"
                    :video-id="$video->provider_id"
                    :provider="$video->provider"
                    :duration="$video->durationLabel()"
                    :title="$video->title"
                />

                <div class="mt-5">
                    @if($video->category)
                        <x-category-badge color="primary" position="inline">{{ $video->category->name }}</x-category-badge>
                    @endif

                    <h2 class="mt-3 text-xl font-bold leading-snug text-slate-900 sm:text-2xl">
                        {{ $video->title }}
                    </h2>

                    {{-- Meta strip: publication data left, sharing right --}}
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-x-5 gap-y-2 border-b border-slate-100 pb-5 text-sm text-slate-500">
                        <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                            @if($video->published_at)
                                <span class="inline-flex items-center gap-1.5">
                                    <x-icon name="calendar" class="h-4 w-4 shrink-0" />
                                    {{ $video->published_at->translatedFormat('j F Y') }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1.5">
                                <x-icon name="eye" class="h-4 w-4 shrink-0" />
                                {{ number_format($video->views, 0, ',', ' ') }} vues
                            </span>
                            @if($video->durationLabel())
                                <span class="inline-flex items-center gap-1.5">
                                    <x-icon name="clock" class="h-4 w-4 shrink-0" />
                                    {{ $video->durationLabel() }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ============ TABBED DETAIL ============ --}}
                @php
                    /*
                     * Only what the capsule actually carries. The plan asks
                     * for a written transcript "lorsqu'elle existe", so that
                     * tab appears for a capsule that has one and not
                     * otherwise.
                     */
                    $capsuleTabs = ['description' => 'Description'];

                    if ($video->hasTranscript()) {
                        $capsuleTabs['transcription'] = 'Transcription';
                    }
                @endphp

                <x-tabs class="mt-6" name="capsule" :tabs="$capsuleTabs">
                    <x-slot:description>
                        <div class="rounded-lg bg-primary-50/40 p-5">
                            <p class="whitespace-pre-line text-sm leading-relaxed text-slate-600">
                                {{ $video->summary ?? "La description de cette capsule sera publiée prochainement." }}
                            </p>
                        </div>
                    </x-slot:description>

                    @if($video->hasTranscript())
                        <x-slot:transcription>
                            <div class="rounded-lg bg-primary-50/40 p-5">
                                <p class="text-sm leading-relaxed text-slate-600">
                                    Transcription écrite de la capsule, pour la lecture hors ligne et
                                    pour les personnes sourdes ou malentendantes.
                                </p>

                                <div class="mt-5 whitespace-pre-line border-t border-primary-100 pt-5 text-sm leading-relaxed text-slate-700">
                                    {{ $video->transcript }}
                                </div>
                            </div>
                        </x-slot:transcription>
                    @endif
                </x-tabs>

                {{-- ============ SHARE ============ --}}
                <div class="mt-8 rounded-lg border border-primary-100 bg-primary-50/60 p-5">
                    <x-share-buttons :title="$video->title" />
                </div>

                {{-- ============ MORE VIDEOS ============ --}}
                @if($more->isNotEmpty())
                    <div class="mt-12">
                        <x-section-heading :link="route('webtv.index')" link-label="Voir toutes les vidéos">
                            Plus de vidéos
                        </x-section-heading>

                        <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                            @foreach ($more as $other)
                                <x-video-card
                                    :image-url="$other->coverUrl()"
                                    :duration="$other->durationLabel()"
                                    :title="$other->title"
                                    :date="$other->published_at?->translatedFormat('j F Y')"
                                    :views="number_format($other->views, 0, ',', ' ')"
                                    :href="route('webtv.show', $other)"
                                />
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">

                @if($video->category && $playlist->count() > 1)
                    <x-sidebar-card icon="folder" title="Playlist : {{ $video->category->name }}" :tint="true">
                        <ul class="space-y-2">
                            @foreach ($playlist as $item)
                                @php $isCurrent = $item->id === $video->id; @endphp
                                <li>
                                    <a
                                        href="{{ route('webtv.show', $item) }}"
                                        @if($isCurrent) aria-current="true" @endif
                                        class="group flex items-start gap-3 rounded-md p-2 {{ $isCurrent ? 'bg-primary-50' : 'hover:bg-slate-50' }}"
                                    >
                                        <span class="relative shrink-0">
                                            <img
                                                src="{{ $item->coverUrl() }}"
                                                alt=""
                                                width="160" height="90"
                                                loading="lazy"
                                                class="h-12 w-20 rounded object-cover"
                                            >
                                            @if($item->durationLabel())
                                                <span class="absolute bottom-0.5 right-0.5 rounded bg-slate-900/80 px-1 text-[10px] font-medium text-white">
                                                    {{ $item->durationLabel() }}
                                                </span>
                                            @endif
                                        </span>

                                        <span class="min-w-0">
                                            <span class="block text-sm font-medium leading-snug text-slate-800 group-hover:text-primary-600">{{ $item->title }}</span>
                                            <span class="mt-0.5 block text-xs text-slate-500">
                                                {{ number_format($item->views, 0, ',', ' ') }} vues
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('webtv.index', ['categorie' => $video->category->slug]) }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700">
                            Voir la playlist
                            <x-icon name="arrow-right" class="h-4 w-4" />
                        </a>
                    </x-sidebar-card>
                @endif

                <x-sidebar-card icon="youtube" title="Notre chaîne YouTube" :tint="true">
                    <p class="text-sm leading-relaxed text-slate-600">
                        Suivez toutes nos vidéos sur notre chaîne officielle.
                    </p>
                    <x-button :href="config('clinique.social.youtube') ?: '#'" variant="outline" class="mt-4 w-full justify-center">
                        Voir la chaîne
                    </x-button>
                </x-sidebar-card>

                <x-help-card icon="chat" title="Vous avez une vidéo à proposer ?">
                    Partagez vos initiatives, témoignages ou événements pour une diffusion sur notre Web TV.
                </x-help-card>

            </aside>
        </div>
    </section>

@endsection
