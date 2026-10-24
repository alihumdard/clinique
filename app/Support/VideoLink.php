<?php

namespace App\Support;

/**
 * Turns a pasted video address into the provider and id the player needs.
 *
 * The plan asks that an editor publish a capsule "par simple collage du
 * lien, sans aucune manipulation technique", so the shape of the URL is
 * our problem, not theirs: watch links, share links, embed links and
 * Shorts all have to resolve to the same id.
 */
class VideoLink
{
    /**
     * @return array{provider: string, provider_id: string}|null
     *         null when the address is not a provider we can embed.
     */
    public static function parse(?string $url): ?array
    {
        if (blank($url)) {
            return null;
        }

        $url = trim($url);
        $host = str_replace('www.', '', strtolower((string) parse_url($url, PHP_URL_HOST)));

        return match (true) {
            str_contains($host, 'youtu') => self::youtube($url),
            str_contains($host, 'vimeo') => self::vimeo($url),
            default => null,
        };
    }

    /**
     * youtu.be/ID, /watch?v=ID, /embed/ID, /shorts/ID, /live/ID.
     * An id is 11 characters of the URL-safe alphabet.
     */
    private static function youtube(string $url): ?array
    {
        $patterns = [
            '#youtu\.be/([\w-]{11})#i',
            '#[?&]v=([\w-]{11})#i',
            '#/(?:embed|shorts|live|v)/([\w-]{11})#i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $m)) {
                return ['provider' => 'youtube', 'provider_id' => $m[1]];
            }
        }

        return null;
    }

    /** vimeo.com/123456789, player.vimeo.com/video/123456789. */
    private static function vimeo(string $url): ?array
    {
        if (preg_match('#vimeo\.com/(?:video/)?(\d{6,})#i', $url, $m)) {
            return ['provider' => 'vimeo', 'provider_id' => $m[1]];
        }

        return null;
    }

    /** The thumbnail the provider already hosts, used when none is uploaded. */
    public static function thumbnail(?string $provider, ?string $providerId): ?string
    {
        if (blank($providerId)) {
            return null;
        }

        // Vimeo needs an API call for its thumbnail, so only YouTube's
        // predictable address is used; a Vimeo capsule falls back to the
        // placeholder until a cover is uploaded.
        return $provider === 'youtube'
            ? "https://i.ytimg.com/vi/{$providerId}/hqdefault.jpg"
            : null;
    }
}
