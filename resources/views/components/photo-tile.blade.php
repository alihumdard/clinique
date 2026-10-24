{{--
    Gallery photo tile with a zoom affordance in the bottom-right corner.

    Rendered as a link rather than a JS lightbox: the performance plan keeps
    the base bundle tiny and requires pages to stay usable with scripts
    disabled. Module 2 can progressively enhance this into a lightbox.
--}}
@props([
    // `path` is a placeholder path under public/images; `url` is a ready
    // URL, which is what a stored photo gives. One or the other.
    'path' => null,
    'url' => null,
    'alt' => '',
    'href' => null,
])

<a
    href="{{ $href ?? $url ?? asset('images/' . $path . '.jpg') }}"
    class="group relative block overflow-hidden rounded-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
>
    @if($url)
        <img
            src="{{ $url }}"
            alt="{{ $alt }}"
            width="600" height="400"
            loading="lazy"
            class="aspect-[3/2] w-full object-cover transition-transform duration-300 group-hover:scale-105"
        >
    @else
        <x-picture
            :path="$path"
            :alt="$alt"
            width="600" height="400"
            class="aspect-[3/2] w-full object-cover transition-transform duration-300 group-hover:scale-105"
        />
    @endif

    {{-- Dark wash on hover keeps the white zoom glyph legible on any photo. --}}
    <span class="absolute inset-0 bg-slate-900/0 transition-colors duration-300 group-hover:bg-slate-900/20" aria-hidden="true"></span>

    <span
        class="absolute bottom-3 right-3 flex h-8 w-8 items-center justify-center rounded-md bg-white/90 text-slate-700 opacity-0 transition-opacity duration-300 group-hover:opacity-100 group-focus-visible:opacity-100"
        aria-hidden="true"
    >
        <x-icon name="zoom-in" class="h-4 w-4" />
    </span>
</a>
