{{--
    WebP with a JPEG fallback, per the performance plan (WebP + repli,
    dimensions explicites), and a srcset so a card does not download a
    hero.

    Pass `path` without an extension. The build ships three widths of every
    placeholder — 400, 800 and the 1600 original — and scripts/
    make-image-variants.php writes the two narrow ones. `sizes` tells the
    browser how wide the image will actually be, which is the only way it
    can pick correctly before layout; the default assumes a card in a
    three-up grid, which is what most uses here are.
--}}
@props([
    'path',
    'alt',
    'width',
    'height',
    'eager' => false,
    'sizes' => '(min-width: 1280px) 400px, (min-width: 640px) 50vw, 100vw',
    // A hero spans the viewport, so its widest step is the one that counts.
    'hero' => false,
])

@php
    $base = 'images/'.$path;

    // The variants exist only for the bundled placeholders; anything else
    // falls back to the single file it has always had.
    $hasVariants = file_exists(public_path($base.'-800.webp'));

    $sizesAttribute = $hero
        ? '100vw'
        : $sizes;

    $webp = $hasVariants
        ? asset($base.'-400.webp').' 400w, '.asset($base.'-800.webp').' 800w, '.asset($base.'.webp').' 1600w'
        : null;

    $jpeg = $hasVariants
        ? asset($base.'-400.jpg').' 400w, '.asset($base.'-800.jpg').' 800w, '.asset($base.'.jpg').' 1600w'
        : null;
@endphp

<picture>
    @if($webp)
        <source srcset="{{ $webp }}" sizes="{{ $sizesAttribute }}" type="image/webp">
        <source srcset="{{ $jpeg }}" sizes="{{ $sizesAttribute }}" type="image/jpeg">
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
