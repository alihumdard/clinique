<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps the IT administrator out of real dossiers: on a registre holding
 * real data, the administrateur technique manages accounts and reads the
 * journal, and nothing that carries a person's matter.
 */
class EnsureRegistreDataAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('registre');

        if ($user && ! $user->mayReadDossiers()) {
            // The dashboard is where sign-in lands, so it sends on rather
            // than refusing.
            if ($request->routeIs('registre.dashboard')) {
                return redirect()->route('registre.accounts');
            }

            abort(403, "Le service informatique n'a pas accès aux dossiers réels.");
        }

        return $next($request);
    }
}
