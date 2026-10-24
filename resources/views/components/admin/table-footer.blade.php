{{--
    Footer strip under a management table: result count, per-page control
    and pagination with first/last jumps.
--}}
@props([
    // Pass a paginator and the numbers come from it. The loose props stay
    // for the screens still showing example rows.
    'paginator' => null,
    'from' => 1,
    'to' => 10,
    'total' => 0,
    'unit' => 'éléments',
    'current' => 1,
    'pages' => 1,
])

@php
    if ($paginator) {
        $total = $paginator->total();
        $from = $paginator->firstItem() ?? 0;
        $to = $paginator->lastItem() ?? 0;
        $current = $paginator->currentPage();
        $pages = $paginator->lastPage();
    }
@endphp

@php
    $arrow = 'flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-500 transition-colors hover:bg-slate-50';
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 border-t border-slate-100 p-5 lg:flex-row lg:items-center lg:justify-between']) }}>

    <p class="text-sm text-slate-500">
        Affichage de {{ $from }} à {{ $to }} sur {{ $total }} {{ $unit }}
    </p>

    <div class="flex flex-wrap items-center gap-3">
        <div>
            <label for="per-page" class="sr-only">Nombre de lignes par page</label>
            <select
                id="per-page"
                class="rounded-lg border border-slate-200 py-2 pl-3 pr-8 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
                <option>10 par page</option>
                <option>25 par page</option>
                <option>50 par page</option>
            </select>
        </div>

        <nav aria-label="Pagination" class="flex items-center gap-1.5">
            <a href="{{ url()->current() }}?page=1" aria-label="Première page" class="{{ $arrow }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" /></svg>
            </a>

            <a href="{{ url()->current() }}?page={{ max(1, $current - 1) }}" aria-label="Page précédente" class="{{ $arrow }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
            </a>

            @for ($i = 1; $i <= $pages; $i++)
                <a
                    href="{{ url()->current() }}?page={{ $i }}"
                    @if($i === $current) aria-current="page" @endif
                    class="flex h-9 w-9 items-center justify-center rounded-md text-sm font-medium {{ $i === $current ? 'bg-primary-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}"
                >
                    {{ $i }}
                </a>
            @endfor

            <a href="{{ url()->current() }}?page={{ min($pages, $current + 1) }}" aria-label="Page suivante" class="{{ $arrow }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
            </a>

            <a href="{{ url()->current() }}?page={{ $pages }}" aria-label="Dernière page" class="{{ $arrow }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 4.5l7.5 7.5-7.5 7.5m6-15l7.5 7.5-7.5 7.5" /></svg>
            </a>
        </nav>
    </div>
</div>
