<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the language of the response from the visitor's choice.
 *
 * The site is written in French and French is what it falls back to:
 * Laravel's JSON translations return the key itself when no English
 * entry exists, so a string that has not been translated yet is served
 * in French rather than as a missing-key placeholder. That is what makes
 * translating the pages something that can be done a page at a time.
 *
 * The choice is a plain cookie rather than a session entry: it should
 * survive a session expiring, and there is nothing private about it.
 */
class SetLocale
{
    public const COOKIE = 'langue';

    /** @var list<string> */
    public const SUPPORTED = ['fr', 'en'];

    public const DEFAULT = 'fr';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) $request->cookie(self::COOKIE);

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = self::DEFAULT;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
