@props(['question'])

<details class="group rounded-lg border border-slate-200 bg-white open:shadow-sm">
    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-4 text-sm font-semibold text-slate-900 marker:content-none">
        {{ $question }}
        <svg class="h-5 w-5 shrink-0 text-slate-400 transition-transform group-open:rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
    </summary>
    <div class="border-t border-slate-100 px-4 pb-4 pt-3 text-sm leading-relaxed text-slate-600">
        {{ $slot }}
    </div>
</details>
