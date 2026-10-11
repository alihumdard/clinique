@props(['image', 'category', 'categoryColor' => 'primary', 'date', 'title', 'excerpt', 'href' => '#'])

@php
    $colors = [
        'primary' => 'bg-primary-600',
        'amber' => 'bg-amber-500',
        'green' => 'bg-emerald-600',
        'purple' => 'bg-violet-600',
        'red' => 'bg-rose-600',
    ];
@endphp

<article class="overflow-hidden rounded-lg border border-slate-200 bg-white">
    <div class="relative">
        <x-picture :path="$image" :alt="$title" width="700" height="420" class="aspect-[5/3] w-full object-cover" />
        <span class="absolute left-3 top-3 rounded-md {{ $colors[$categoryColor] ?? $colors['primary'] }} px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-white">
            {{ $category }}
        </span>
    </div>
    <div class="p-5">
        <p class="flex items-center gap-1.5 text-xs text-slate-400">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
            {{ $date }}
        </p>
        <h3 class="mt-2 text-base font-semibold leading-snug text-slate-900">
            <a href="{{ $href }}" class="hover:text-primary-600">{{ $title }}</a>
        </h3>
        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $excerpt }}</p>
        <a href="{{ $href }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700">
            Lire la suite
            <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>
</article>
