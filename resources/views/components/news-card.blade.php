@props(['image', 'category', 'categoryColor' => 'primary', 'date', 'title', 'excerpt', 'href' => null])

<article class="overflow-hidden rounded-lg border border-slate-200 bg-white">
    <div class="relative">
        <x-picture :path="$image" :alt="$title" width="700" height="420" class="aspect-[5/3] w-full object-cover" />
        <x-category-badge :color="$categoryColor">{{ $category }}</x-category-badge>
    </div>
    <div class="p-5">
        <p class="flex items-center gap-1.5 text-xs text-slate-400">
            <x-icon name="calendar" class="h-3.5 w-3.5" />
            {{ $date }}
        </p>
        <h3 class="mt-2 text-base font-semibold leading-snug text-slate-900">
            <a href="{{ $href ?? url()->current() }}" class="hover:text-primary-600">{{ $title }}</a>
        </h3>
        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $excerpt }}</p>
        <a href="{{ $href ?? url()->current() }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700">
            Lire la suite
            <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>
</article>
