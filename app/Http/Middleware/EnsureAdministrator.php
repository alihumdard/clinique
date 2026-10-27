<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the screens an éditeur de contenu has no business on: the site
 * settings, the référentiels, the personal data the public leaves
 * (contact messages, newsletter), and permanent deletion.
 *
 * It answers 403 rather than redirecting: a redirect to the dashboard
 * would read as "that screen does not exist" instead of "your account
 * may not open it", and an editor who was sent a link deserves the
 * difference. The sidebar hides the same screens, so reaching this is a
 * typed URL or a stale link.
 */
class EnsureAdministrator
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isAdmin(), 403, "Votre compte n'a pas accès à cette section.");

        return $next($request);
    }
}
