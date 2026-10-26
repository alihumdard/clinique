{{--
    Horizontal thumbnail strip linking into the full gallery.
    Scrolls sideways on phones rather than shrinking thumbnails to nothing.

    $items: array of ['path' => 'placeholders/x', 'alt' => '...']
--}}
@props([
    // `items` is the static form: ['path' => 'placeholders/x', 'alt' => ''].
    // `photos` is a collection of stored photos, each with url() and alt().
    'items' => [],
    'photos' => null,
    'href' => null,
])

@php
    // One shape for the loop, whichever way the strip was given its photos.
    $tiles = $photos
        ? $photos->map(fn ($photo) => ['storedPath' => $photo->path, 'alt' => $photo->alt()])
        : collect($items)->map(fn (array $item) => ['path' => $item['path'], 'alt' => $item['alt'] ?? '']);
@endphp

<ul class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-2 sm:grid sm:grid-cols-3 sm:overflow-visible sm:pb-0 lg:grid-cols-5">
    @foreach ($tiles as $item)
        <li class="w-64 shrink-0 snap-start sm:w-auto">
            @if($href)
                <a href="{{ $href }}" class="group block overflow-hidden rounded-lg">
            @else
                <div class="overflow-hidden rounded-lg">
            @endif

                @if($item['storedPath'] ?? null)
                    <x-stored-image
                        :path="$item['storedPath']"
                        :alt="$item['alt']"
                        fallback="placeholders/news-caravan"
                        width="400" height="300"
                        sizes="(min-width: 1024px) 20vw, (min-width: 640px) 33vw, 256px"
                        class="aspect-[4/3] w-full object-cover transition-transform duration-300 {{ $href ? 'group-hover:scale-105' : '' }}"
                    />
                @else
                    <x-picture
                        :path="$item['path']"
                        :alt="$item['alt']"
                        width="400" height="300"
                        class="aspect-[4/3] w-full object-cover transition-transform duration-300 {{ $href ? 'group-hover:scale-105' : '' }}"
                    />
                @endif

            @if($href)
                </a>
            @else
                </div>
            @endif
        </li>
    @endforeach
</ul>
