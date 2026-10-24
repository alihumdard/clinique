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

    /** A placeholder carries no extension; an upload always does. */
    private static function isPlaceholder(string $path): bool
    {
        return str_contains($path, '/')
            && ! preg_match('/\.(jpe?g|png|webp|avif)$/i', $path);
    }
}
