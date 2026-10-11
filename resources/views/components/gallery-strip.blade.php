{{--
    Horizontal thumbnail strip linking into the full gallery.
    Scrolls sideways on phones rather than shrinking thumbnails to nothing.

    $items: array of ['path' => 'placeholders/x', 'alt' => '...']
--}}
@props([
    'items' => [],
    'href' => null,
])

<ul class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-2 sm:grid sm:grid-cols-3 sm:overflow-visible sm:pb-0 lg:grid-cols-5">
    @foreach ($items as $item)
        <li class="w-64 shrink-0 snap-start sm:w-auto">
            @if($href)
                <a href="{{ $href }}" class="group block overflow-hidden rounded-lg">
            @else
                <div class="overflow-hidden rounded-lg">
            @endif

                <x-picture
                    :path="$item['path']"
                    :alt="$item['alt'] ?? ''"
                    width="400" height="300"
                    class="aspect-[4/3] w-full object-cover transition-transform duration-300 {{ $href ? 'group-hover:scale-105' : '' }}"
                />

            @if($href)
                </a>
            @else
                </div>
            @endif
        </li>
    @endforeach
</ul>
