{{--
    Activity / caravane card: image with overlaid category badge, then a meta
    row carrying both date and location (the location pin is what separates
    this from x-news-card, where events have no place).
--}}
@props([
    'image',
    'category',
    'categoryColor' => 'primary',
    'date',
    'location' => null,
    'title',
    'excerpt' => null,
    'href' => '#',
])

<article class="flex h-full flex-col overflow-hidden rounded-lg border border-slate-200 bg-white">
    <div class="relative">
        <x-picture
            :path="$image"
            :alt="$title"
            width="700" height="420"
            class="aspect-[5/3] w-full object-cover"
        />
        <x-category-badge :color="$categoryColor">{{ $category }}</x-category-badge>
    </div>

    <div class="flex flex-1 flex-col p-5">
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-500">
            <span class="inline-flex items-center gap-1.5">
                <x-icon name="calendar" class="h-3.5 w-3.5 shrink-0" />
                {{ $date }}
            </span>
            @if($location)
                <span class="inline-flex items-center gap-1.5">
                    <x-icon name="map-pin" class="h-3.5 w-3.5 shrink-0" />
                    {{ $location }}
                </span>
            @endif
        </div>

        <h3 class="mt-2 text-base font-semibold leading-snug text-slate-900">
            <a href="{{ $href }}" class="hover:text-primary-600">{{ $title }}</a>
        </h3>

        @if($excerpt)
            <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $excerpt }}</p>
        @endif

        {{-- mt-auto pins the link to the bottom so cards in a row align --}}
        <a href="{{ $href }}" class="mt-auto inline-flex items-center gap-1.5 pt-3 text-sm font-semibold text-primary-600 hover:text-primary-700">
            Lire la suite
            <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>
</article>
