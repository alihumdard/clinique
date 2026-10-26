{{--
    Downloadable resource card (fiche pratique / rapport annuel): cover image
    with a domain badge, title, summary, then a PDF meta row and download
    button pinned to the bottom so cards align across a row.
--}}
@props([
    // `image` is a placeholder path under public/images; `imageUrl` is a
    // ready URL, which is what a stored cover gives. One or the other.
    'image' => null,
    'imageUrl' => null,
    'storedPath' => null,
    'category' => null,
    'categoryColor' => 'primary',
    'title',
    'excerpt' => null,
    'filesize' => null,
    'href' => '#',
    'downloadHref' => null,
    'downloadLabel' => 'Télécharger',
])

<article class="flex h-full flex-col overflow-hidden rounded-lg border border-slate-200 bg-white">
    <div class="relative">
        @if($storedPath !== null)
            <x-stored-image
                :path="$storedPath"
                :alt="$title"
                fallback="placeholders/guide-practical"
                width="600" height="400"
                class="aspect-[3/2] w-full object-cover"
            />
        @elseif($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $title }}" width="600" height="400" loading="lazy" class="aspect-[3/2] w-full object-cover">
        @else
            <x-picture
                :path="$image"
                :alt="$title"
                width="600" height="400"
                class="aspect-[3/2] w-full object-cover"
            />
        @endif
        @if($category)
            <x-category-badge :color="$categoryColor">{{ $category }}</x-category-badge>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        <h3 class="text-base font-semibold leading-snug text-slate-900">
            <a href="{{ $href }}" class="hover:text-primary-600">{{ $title }}</a>
        </h3>

        @if($excerpt)
            <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $excerpt }}</p>
        @endif

        {{-- mt-auto + border keeps the action row flush at the card foot --}}
        <div class="mt-auto flex flex-wrap items-center justify-between gap-3 pt-4">
            @if($filesize)
                <span class="inline-flex items-center gap-1.5 text-xs text-slate-500">
                    <x-icon name="document" class="h-4 w-4 shrink-0 text-rose-500" />
                    PDF ({{ $filesize }})
                </span>
            @endif

            <a
                href="{{ $downloadHref ?? $href }}"
                class="inline-flex items-center gap-1.5 rounded-md border border-primary-200 bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary-700 transition-colors hover:bg-primary-100"
            >
                <x-icon name="download" class="h-4 w-4" />
                {{ $downloadLabel }}
            </a>
        </div>
    </div>
</article>
