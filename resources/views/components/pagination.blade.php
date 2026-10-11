{{-- Static visual pagination for the design phase; wired to real query results in the backend phase. --}}
@props([
    'current' => 1,
    'total' => 5,
    'edges' => false,
])

@php
    $arrow = 'flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-500 hover:bg-slate-50';
@endphp

<nav aria-label="Pagination" {{ $attributes->merge(['class' => 'mt-10 flex items-center justify-center gap-2']) }}>
    @if($edges)
        <a href="{{ url()->current() }}?page=1" aria-label="Première page" class="{{ $arrow }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" /></svg>
        </a>
    @endif

    <a href="{{ url()->current() }}?page={{ max(1, $current - 1) }}" aria-label="Page précédente" class="{{ $arrow }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $edges ? 'M15.75 19.5L8.25 12l7.5-7.5' : 'M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18' }}" /></svg>
    </a>

    @for ($i = 1; $i <= $total; $i++)
        <a
            href="{{ url()->current() }}?page={{ $i }}"
            @if($i === $current) aria-current="page" @endif
            class="flex h-9 w-9 items-center justify-center rounded-md text-sm font-medium {{ $i === $current ? 'bg-primary-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}"
        >
            {{ $i }}
        </a>
    @endfor

    @unless($edges)
        <span class="px-1 text-slate-400">&hellip;</span>
    @endunless

    <a href="{{ url()->current() }}?page={{ min($total, $current + 1) }}" aria-label="Page suivante" class="{{ $arrow }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $edges ? 'M8.25 4.5l7.5 7.5-7.5 7.5' : 'M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3' }}" /></svg>
    </a>

    @if($edges)
        <a href="{{ url()->current() }}?page={{ $total }}" aria-label="Dernière page" class="{{ $arrow }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 4.5l7.5 7.5-7.5 7.5m6-15l7.5 7.5-7.5 7.5" /></svg>
        </a>
    @endif
</nav>
