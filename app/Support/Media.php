<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Resolves a stored image path to a URL.
 *
 * Two kinds of path coexist and will keep coexisting until the Clinic's own
 * pictures replace the review content: seeded rows point at the placeholder
 * files under public/images (no extension, a sibling .webp beside the .jpg),
 * while an upload is a real filename on the public disk.
 */
class Media
{
    public static function url(?string $path, string $fallback = 'placeholders/news-1'): string
    {
        if (blank($path)) {
            return asset("images/{$fallback}.jpg");
        }

        // An absolute address (a provider's own thumbnail) is already a URL.
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (self::isPlaceholder($path)) {
            return asset("images/{$path}.jpg");
        }

        return Storage::disk('public')->url($path);
    }

    /** The widths scripts/make-image-variants.php writes beside an original. */
    public const VARIANTS = [400, 800];

    /**
     * A srcset over the stored widths of an image, or null where only one
     * width exists.
     *
     * Images reached through a model — a capsule's cover, a gallery photo,
     * a fiche's thumbnail — are rendered as a plain <img> rather than
     * through x-picture, so until this existed they all served the 1600px
     * original. A gallery tile 400px wide was downloading a hero.
     *
     * @return array{webp: string, jpeg: string}|null
     */
    public static function srcset(?string $path): ?array
    {
        if (blank($path) || ! self::isPlaceholder($path)) {
            // An upload has no variants yet: they would have to be written
            // at upload time, and the existing files predate that.
            return null;
        }

        if (! file_exists(public_path("images/{$path}-800.webp"))) {
            return null;
        }

        $steps = [...self::VARIANTS, null];

        $set = fn (string $extension) => implode(', ', array_map(
            fn (?int $width) => $width === null
                ? asset("images/{$path}.{$extension}").' 1600w'
                : asset("images/{$path}-{$width}.{$extension}")." {$width}w",
            $steps
        ));

        return [
            'webp' => $set('webp'),
            'jpeg' => $set('jpg'),
        ];
    }

    /**
     * "2,1 Mo" / "840 Ko" — the French decimal comma, as the pages show it.
     * A small file in megabytes reads as "0,0 Mo", so the unit follows the
     * size rather than being fixed.
     */
    public static function sizeForHumans(?int $bytes): string
    {
        $bytes = max(0, (int) $bytes);

        if ($bytes < 1048576) {
            return number_format($bytes / 1024, 0, ',', ' ').' Ko';
        }

        return number_format($bytes / 1048576, 1, ',', ' ').' Mo';
    }

    /** A placeholder carries no extension; an upload always does. */
    private static function isPlaceholder(string $path): bool
    {
        return str_contains($path, '/')
            && ! preg_match('/\.(jpe?g|png|webp|avif)$/i', $path);
    }
}
