{{-- Pass a paginator and the links come from it; the loose props stay for
     the pages still showing example rows. --}}
@props([
    'paginator' => null,
    'current' => 1,
    'total' => 5,
    'edges' => false,
])

@php
    if ($paginator) {
        $current = $paginator->currentPage();
        $total = $paginator->lastPage();
    }

    /*
     * The paginator builds its own links, which keeps the active filters
     * (?categorie=, ?annee=, ?q=) that a hand-built ?page= would drop.
     */
    $link = fn (int $page) => $paginator
        ? $paginator->url($page)
        : url()->current() . '?page=' . $page;

    $arrow = 'flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-500 hover:bg-slate-50';
@endphp

{{-- A single page of results needs no pagination at all. --}}
@if(! $paginator || $total > 1)
    <nav aria-label="Pagination" {{ $attributes->merge(['class' => 'mt-10 flex items-center justify-center gap-2']) }}>
        @if($edges)
            <a href="{{ $link(1) }}" aria-label="Première page" class="{{ $arrow }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" /></svg>
            </a>
        @endif

        <a href="{{ $link(max(1, $current - 1)) }}" aria-label="Page précédente" class="{{ $arrow }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $edges ? 'M15.75 19.5L8.25 12l7.5-7.5' : 'M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18' }}" /></svg>
        </a>

        @for ($i = 1; $i <= $total; $i++)
            <a
                href="{{ $link($i) }}"
                @if($i === $current) aria-current="page" @endif
                class="flex h-9 w-9 items-center justify-center rounded-md text-sm font-medium {{ $i === $current ? 'bg-primary-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}"
            >
                {{ $i }}
            </a>
        @endfor

        {{-- The ellipsis is part of the static design; a real paginator
             already lists every page it has. --}}
        @unless($edges || $paginator)
            <span class="px-1 text-slate-400">&hellip;</span>
        @endunless

        <a href="{{ $link(min($total, $current + 1)) }}" aria-label="Page suivante" class="{{ $arrow }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $edges ? 'M8.25 4.5l7.5 7.5-7.5 7.5' : 'M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3' }}" /></svg>
        </a>

        @if($edges)
            <a href="{{ $link($total) }}" aria-label="Dernière page" class="{{ $arrow }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 4.5l7.5 7.5-7.5 7.5m6-15l7.5 7.5-7.5 7.5" /></svg>
            </a>
        @endif
    </nav>
@endif
