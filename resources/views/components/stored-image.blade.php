{{--
    An image reached through a model: a capsule's cover, a gallery photo, a
    fiche's thumbnail, a partner's logo.

    Pass the raw stored value — the column, not a URL. Where the file has
    width variants (every bundled placeholder does) this emits a srcset
    over them with a WebP first, exactly as x-picture does for the
    hand-placed images. Where it does not — an upload, which has only the
    one file — it falls back to a single src, so nothing breaks.

    `sizes` is how the browser decides before layout. The default assumes a
    card in a three-up grid, which is what most uses here are.
--}}
@props([
    'path',
    'alt' => '',
    'width' => 600,
    'height' => 400,
    'fallback' => 'placeholders/news-1',
    'sizes' => '(min-width: 1280px) 400px, (min-width: 640px) 50vw, 100vw',
    'eager' => false,
])

@php
    $src = \App\Support\Media::url($path, $fallback);
    $srcset = \App\Support\Media::srcset($path);
@endphp

@if($srcset)
    <picture>
        <source srcset="{{ $srcset['webp'] }}" sizes="{{ $sizes }}" type="image/webp">
        <source srcset="{{ $srcset['jpeg'] }}" sizes="{{ $sizes }}" type="image/jpeg">
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            width="{{ $width }}"
            height="{{ $height }}"
            @if($eager) fetchpriority="high" @else loading="lazy" @endif
            {{ $attributes }}
        >
    </picture>
@else
    <img
        src="{{ $src }}"
        alt="{{ $alt }}"
        width="{{ $width }}"
        height="{{ $height }}"
        @if($eager) fetchpriority="high" @else loading="lazy" @endif
        {{ $attributes }}
    >
@endif
