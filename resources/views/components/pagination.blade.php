{{-- Static visual pagination for the design phase; wired to real query results in the backend phase. --}}
@props(['current' => 1, 'total' => 5])

<nav aria-label="Pagination" class="mt-10 flex items-center justify-center gap-2">
    <a href="#" aria-label="Page précédente" class="flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-500 hover:bg-slate-50">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
    </a>

    @for ($i = 1; $i <= $total; $i++)
        <a
            href="#"
            @if($i === $current) aria-current="page" @endif
            class="flex h-9 w-9 items-center justify-center rounded-md text-sm font-medium {{ $i === $current ? 'bg-primary-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}"
        >
            {{ $i }}
        </a>
    @endfor

    <span class="px-1 text-slate-400">&hellip;</span>

    <a href="#" aria-label="Page suivante" class="flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-500 hover:bg-slate-50">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
    </a>
</nav>
