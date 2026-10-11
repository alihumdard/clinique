@props(['image', 'title', 'date', 'href' => '#'])

<a href="{{ $href }}" class="flex items-start gap-3 group">
    <x-picture :path="$image" :alt="$title" width="80" height="80" class="h-16 w-16 shrink-0 rounded-md object-cover" />
    <div class="min-w-0">
        <p class="text-sm font-medium leading-snug text-slate-800 group-hover:text-primary-600">{{ $title }}</p>
        <p class="mt-1 flex items-center gap-1 text-xs text-slate-400">
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
            {{ $date }}
        </p>
    </div>
</a>
