{{--
    WebP with a JPEG fallback, per the performance plan (WebP + repli,
    dimensions explicites), and a srcset so a card does not download a
    hero.

    Pass `path` without an extension. The width variants beside each
    placeholder are written by scripts/make-image-variants.php, and which
    of them exist is Media::srcset's business — this component and
    x-stored-image share that one implementation, because they had the same
    bug independently: both assumed every step existed, and an image
    narrower than a step has no file for it.

    `sizes` tells the browser how wide the image will actually be, which is
    the only way it can choose before layout. The default assumes a card in
    a three-up grid, which is what most uses here are; a hero passes
    hero: true and gets the viewport.
--}}
@props([
    'path',
    'alt',
    'width',
    'height',
    'eager' => false,
    'sizes' => '(min-width: 1280px) 400px, (min-width: 640px) 50vw, 100vw',
    'hero' => false,
])

@php
    /*
     * A blank path asked the browser for /images/.webp — a 404 on every
     * page that showed a row without a picture. A caller that has nothing
     * to show gets the placeholder, which is what every other path here
     * already did.
     */
    $path = filled($path) ? $path : 'placeholders/news-1';

    $base = 'images/'.$path;
    $srcset = \App\Support\Media::srcset($path);
    $sizesAttribute = $hero ? '100vw' : $sizes;
@endphp

<picture>
    @if($srcset)
        <source srcset="{{ $srcset['webp'] }}" sizes="{{ $sizesAttribute }}" type="image/webp">
        <source srcset="{{ $srcset['jpeg'] }}" sizes="{{ $sizesAttribute }}" type="image/jpeg">
    @else
        <source srcset="{{ asset($base.'.webp') }}" type="image/webp">
    @endif

    <img
        src="{{ asset($base.'.jpg') }}"
        alt="{{ $alt }}"
        width="{{ $width }}"
        height="{{ $height }}"
        @if(!$eager) loading="lazy" @else fetchpriority="high" @endif
        {{ $attributes }}
    >
</picture>
