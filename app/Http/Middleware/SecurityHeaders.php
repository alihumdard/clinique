<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sends the baseline security headers required by the Module 4 security
 * checklist. Applied site-wide so a new page cannot be added without them.
 *
 * The CSP is deliberately strict: the performance plan forbids remote
 * scripts and third-party fonts, so no external origin needs allowing.
 * Web TV is the one exception — video players are embedded from the
 * Clinic's external channel, so frame-src lists those hosts explicitly.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only decorate HTML responses; assets and downloads are untouched.
        $contentType = $response->headers->get('Content-Type', '');
        if ($contentType && ! str_contains($contentType, 'text/html')) {
            return $response;
        }

        foreach ($this->headers($request) as $header => $value) {
            // Never clobber a header a controller set deliberately.
            if (! $response->headers->has($header)) {
                $response->headers->set($header, $value);
            }
        }

        return $response;
    }

    /**
     * @return array<string, string>
     */
    protected function headers(Request $request): array
    {
        return [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'X-Permitted-Cross-Domain-Policies' => 'none',

            // No feature on the public site needs these devices.
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()',

            'Content-Security-Policy' => $this->contentSecurityPolicy($request),
        ];
    }

    protected function contentSecurityPolicy(Request $request): string
    {
        // Video hosts for the Web TV embeds (Module 2). Players load only
        // on click, but the frame-src must permit them when they do.
        $frameSrc = "'self' https://www.youtube-nocookie.com https://www.youtube.com https://player.vimeo.com";

        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
            "object-src 'none'",

            // Self-hosted WOFF2 only — no font CDN, so no third-party origin.
            "font-src 'self'",

            // Tailwind ships a compiled stylesheet; 'unsafe-inline' is kept
            // for the inlined critical CSS the performance plan requires.
            "style-src 'self' 'unsafe-inline'",

            "script-src 'self'",

            // data: covers inline SVG placeholders; video thumbnails are
            // served from the video host until a local poster is uploaded.
            "img-src 'self' data: https://i.ytimg.com https://i.vimeocdn.com",

            "connect-src 'self'",
            "media-src 'self'",
            "frame-src {$frameSrc}",
        ];

        // Only meaningful once the site is actually served over TLS. On a
        // plain-HTTP deployment it rewrites every asset URL to https://,
        // where nothing is listening, and the page loads with no CSS at all.
        if ($request->isSecure()) {
            $directives[] = 'upgrade-insecure-requests';
        }

        return implode('; ', $directives);
    }
}
