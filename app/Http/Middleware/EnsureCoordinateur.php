<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps a membre to the two screens they submit on (actualités and
 * activités). Everything else in the CMS is coordinateur work or above.
 */
class EnsureCoordinateur
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->managesContent(), 403, "Votre compte n'a pas accès à cette section.");

        return $next($request);
    }
}
