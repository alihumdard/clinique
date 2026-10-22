{{--
    WebP with JPEG fallback, per the performance plan (WebP + repli, dimensions explicites).
    Pass `path` without extension; expects sibling .webp and .jpg files in public/images/.
--}}
@props([
    'path',
    'alt',
    'width',
    'height',
    'eager' => false,
])

<picture class="contents">
    <source srcset="{{ asset('images/' . $path . '.webp') }}" type="image/webp">
    <img
        src="{{ asset('images/' . $path . '.jpg') }}"
        alt="{{ $alt }}"
        width="{{ $width }}"
        height="{{ $height }}"
        @if(!$eager) loading="lazy" @else fetchpriority="high" @endif
        {{ $attributes }}
    >
</picture>
