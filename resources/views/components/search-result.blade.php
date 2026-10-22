{{--
    Single search hit: thumbnail, content-type pill, title, snippet and a
    date/views meta row, with a chevron affordance on the right.
    `type` selects the pill colour so results are scannable by kind.
--}}
@props([
    'image' => null,
    'type',
    'title',
    'excerpt' => null,
    'date' => null,
    'views' => null,
    'href' => '#',
    'video' => false,
])

@php
    $pills = [
        'Actualité'      => 'bg-primary-50 text-primary-700',
        'Fiche pratique' => 'bg-emerald-50 text-emerald-700',
        'Web TV'         => 'bg-rose-50 text-rose-700',
        'Activité'       => 'bg-violet-50 text-violet-700',
        'Page'           => 'bg-slate-100 text-slate-700',
    ];
@endphp

<article class="group flex items-stretch gap-4 border-b border-slate-100 py-5 last:border-0">
    @if($image)
        <a href="{{ $href }}" class="relative block shrink-0">
            <x-picture
                :path="$image"
                alt=""
                width="320" height="200"
                class="h-full w-32 rounded-lg object-cover sm:w-36"
            />

            @if($video)
                {{-- Marks the hit as a capsule without loading any player --}}
                <span class="absolute inset-0 flex items-center justify-center" aria-hidden="true">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/30 ring-2 ring-white/80">
                        <svg class="ml-0.5 h-4 w-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                    </span>
                </span>
            @endif
        </a>
    @endif

    <div class="min-w-0 flex-1">
        <span class="inline-block rounded px-2 py-0.5 text-xs font-medium {{ $pills[$type] ?? $pills['Page'] }}">
            {{ $type }}
        </span>

        <h3 class="mt-2 text-base font-bold leading-snug text-slate-900">
            <a href="{{ $href }}" class="hover:text-primary-600">{{ $title }}</a>
        </h3>

        @if($excerpt)
            <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $excerpt }}</p>
        @endif

        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-500">
            @if($date)
                <span class="inline-flex items-center gap-1.5">
                    <x-icon name="calendar" class="h-3.5 w-3.5 shrink-0" />
                    {{ $date }}
                </span>
            @endif
            @if($views)
                <span class="inline-flex items-center gap-1.5">
                    <x-icon name="eye" class="h-3.5 w-3.5 shrink-0" />
                    {{ $views }}
                </span>
            @endif
        </div>
    </div>

    <a
        href="{{ $href }}"
        tabindex="-1"
        aria-hidden="true"
        class="hidden h-9 w-9 shrink-0 items-center justify-center self-center rounded-full bg-slate-50 text-slate-400 transition-colors group-hover:bg-primary-50 group-hover:text-primary-600 sm:flex"
    >
        <x-icon name="arrow-right" class="h-4 w-4" />
    </a>
</article>
