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

    /*
     * The paginator builds its own links, which keeps the active filters
     * (?statut=, ?categorie=, ?q=) that a hand-built ?page= would drop.
     */
    $link = fn (int $page) => $paginator
        ? $paginator->url($page)
        : url()->current() . '?page=' . $page;

    $arrow = 'flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-500 transition-colors hover:bg-slate-50';
    // An arrow that cannot go anywhere is drawn as spent rather than
    // removed, so the row of controls does not change width page to page.
    $spent = 'pointer-events-none opacity-40';

    /*
     * A window of page numbers, not all of them.
     *
     * This printed every page: @for ($i = 1; $i <= $pages; $i++). Eleven
     * rapports are four pages and that looked fine, but the newsletter
     * runs to dozens — the numbers then wrapped onto three or four lines
     * and pushed the footer over the content below it. Two pages either
     * side of the current one, the first and the last always reachable,
     * and an ellipsis where the run breaks.
     */
    $window = 2;
    $numbers = collect(range(1, $pages))
        ->filter(fn (int $i) => $i === 1
            || $i === $pages
            || abs($i - $current) <= $window)
        ->values();

    $perPage = (int) request('par_page', $paginator?->perPage() ?? 10);
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 border-t border-slate-100 p-5 lg:flex-row lg:items-center lg:justify-between']) }}>

    <p class="text-sm text-slate-500">
        Affichage de {{ $from }} à {{ $to }} sur {{ $total }} {{ $unit }}
    </p>

    <div class="flex flex-wrap items-center gap-3">
        {{--
            A real form, not a decorative select.

            This was three options bound to nothing: choosing "50 par page"
            left the page on ten, which is the kind of control the rest of
            this administration deliberately does not draw. It now submits
            as a GET, carrying whatever else is filtered in hidden fields
            so that changing the page size does not clear a search or a
            statut — and dropping page, because row 41 of the old paging is
            not row 41 of the new one.
        --}}
        <form method="GET" action="{{ url()->current() }}" class="flex items-center">
            @foreach (request()->except(['par_page', 'page']) as $key => $value)
                @if(! is_array($value))
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach

            <label for="per-page" class="sr-only">Nombre de lignes par page</label>
            <select
                id="per-page"
                name="par_page"
                onchange="this.form.requestSubmit()"
                class="rounded-lg border border-slate-200 py-2 pl-3 pr-8 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
                @foreach ([10, 25, 50] as $size)
                    <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }} par page</option>
                @endforeach
            </select>

            {{-- Without JavaScript the select still needs a way to submit. --}}
            <noscript>
                <button type="submit" class="ml-2 rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-600">Appliquer</button>
            </noscript>
        </form>

        <nav aria-label="Pagination" class="flex items-center gap-1.5">
            {{-- On the first page these two lead back to it. Drawn spent
                 and made unclickable, so the row keeps its width while
                 saying plainly that there is nothing before this. --}}
            <a href="{{ $link(1) }}" aria-label="Première page" @if($current <= 1) aria-disabled="true" tabindex="-1" @endif class="{{ $arrow }} {{ $current <= 1 ? $spent : '' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" /></svg>
            </a>

            <a href="{{ $link(max(1, $current - 1)) }}" aria-label="Page précédente" @if($current <= 1) aria-disabled="true" tabindex="-1" @endif class="{{ $arrow }} {{ $current <= 1 ? $spent : '' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
            </a>

            @php $previous = 0; @endphp
            @foreach ($numbers as $i)
                @if ($i - $previous > 1)
                    {{-- The run broke: say so rather than letting 3 sit
                         against 47 as though they were neighbours. --}}
                    <span class="px-1 text-sm text-slate-400" aria-hidden="true">…</span>
                @endif

                <a
                    href="{{ $link($i) }}"
                    @if($i === $current) aria-current="page" @endif
                    class="flex h-9 w-9 items-center justify-center rounded-md text-sm font-medium {{ $i === $current ? 'bg-primary-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}"
                >
                    {{ $i }}
                </a>

                @php $previous = $i; @endphp
            @endforeach

            <a href="{{ $link(min($pages, $current + 1)) }}" aria-label="Page suivante" @if($current >= $pages) aria-disabled="true" tabindex="-1" @endif class="{{ $arrow }} {{ $current >= $pages ? $spent : '' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
            </a>

            <a href="{{ $link($pages) }}" aria-label="Dernière page" @if($current >= $pages) aria-disabled="true" tabindex="-1" @endif class="{{ $arrow }} {{ $current >= $pages ? $spent : '' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 4.5l7.5 7.5-7.5 7.5m6-15l7.5 7.5-7.5 7.5" /></svg>
            </a>
        </nav>
    </div>
</div>
