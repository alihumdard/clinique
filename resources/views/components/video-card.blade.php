{{--
    Web TV capsule card: thumbnail with category badge and duration,
    then title and a date / views meta row.
--}}
@props([
    // `image` is a placeholder path under public/images; `imageUrl` is a
    // ready URL, which is what a stored cover or a channel thumbnail
    // gives. One or the other.
    'image' => null,
    'imageUrl' => null,
    'storedPath' => null,
    'title',
    'category' => null,
    'categoryColor' => 'primary',
    'duration' => null,
    'date' => null,
    'views' => null,
    'href' => '#',
])

<article class="flex h-full flex-col overflow-hidden rounded-lg border border-slate-200 bg-white">
    <a href="{{ $href }}" class="group relative block">
        @if($storedPath !== null)
            <x-stored-image
                :path="$storedPath"
                :alt="$title"
                fallback="placeholders/video-featured"
                width="640" height="360"
                class="aspect-video w-full object-cover"
            />
        @elseif($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $title }}" width="640" height="360" loading="lazy" class="aspect-video w-full object-cover">
        @else
            <x-picture
                :path="$image"
                :alt="$title"
                width="640" height="360"
                class="aspect-video w-full object-cover"
            />
        @endif

        {{-- Play affordance appears on hover/focus; the card is a link, not a player --}}
        <span class="absolute inset-0 flex items-center justify-center bg-slate-900/10 opacity-0 transition-opacity group-hover:opacity-100 group-focus-visible:opacity-100" aria-hidden="true">
            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white/30 ring-2 ring-white/80">
                <svg class="ml-0.5 h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
            </span>
        </span>

        @if($category)
            <x-category-badge :color="$categoryColor">{{ $category }}</x-category-badge>
        @endif

        @if($duration)
            <span class="absolute bottom-3 right-3 rounded bg-slate-900/80 px-2 py-0.5 text-xs font-medium text-white">
                {{ $duration }}
            </span>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-4">
        <h3 class="text-sm font-semibold leading-snug text-slate-900">
            <a href="{{ $href }}" class="hover:text-primary-600">{{ $title }}</a>
        </h3>

        <div class="mt-auto flex flex-wrap items-center gap-x-4 gap-y-1.5 pt-3 text-xs text-slate-500">
            @if($date)
                <span class="inline-flex items-center gap-1.5">
                    <x-icon name="calendar" class="h-3.5 w-3.5 shrink-0" />
                    {{ $date }}
                </span>
            @endif
            @if($views)
                <span class="inline-flex items-center gap-1.5">
                    <x-icon name="eye" class="h-3.5 w-3.5 shrink-0" />
                    {{ $views }} vues
                </span>
            @endif
        </div>
    </div>
</article>
