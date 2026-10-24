@props([
    // `image` is a placeholder path under public/images; `imageUrl` is a
    // ready URL, which is what a stored upload gives. One or the other.
    'image' => null,
    'imageUrl' => null,
    'category' => null,
    'categoryColor' => 'primary',
    'date' => null,
    'title',
    'excerpt' => null,
    'href' => null,
])

<article class="flex h-full flex-col overflow-hidden rounded-lg border border-slate-200 bg-white">
    <div class="relative">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="" width="700" height="300" loading="lazy" class="aspect-[7/3] w-full object-cover">
        @else
            <x-picture :path="$image" :alt="$title" width="700" height="300" class="aspect-[7/3] w-full object-cover" />
        @endif
        <x-category-badge :color="$categoryColor">{{ $category }}</x-category-badge>
    </div>
    <div class="flex flex-1 flex-col p-4">
        <p class="flex items-center gap-1.5 text-xs text-slate-400">
            <x-icon name="calendar" class="h-3.5 w-3.5" />
            {{ $date }}
        </p>
        <h3 class="mt-2 text-base font-semibold leading-snug text-slate-900">
            <a href="{{ $href ?? url()->current() }}" class="hover:text-primary-600">{{ $title }}</a>
        </h3>
        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $excerpt }}</p>
        <a href="{{ $href ?? url()->current() }}" class="mt-auto inline-flex items-center gap-1.5 pt-3 text-sm font-semibold text-primary-600 hover:text-primary-700">
            Lire la suite
            <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>
</article>
