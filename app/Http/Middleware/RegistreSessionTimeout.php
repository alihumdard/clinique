<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ends a Registre session after thirty idle minutes.
 *
 * The Module 4 checklist states it for the Registre specifically —
 * "terminer automatiquement les sessions du registre après 30 minutes
 * d'inactivité" — because Clinic workstations are shared and what is open
 * here is confidential case material, not a web page.
 *
 * Its own session key, so idling on the CMS does not keep a Registre
 * session alive, nor the other way round.
 */
class RegistreSessionTimeout
{
    protected const TIMEOUT_MINUTES = 30;

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('registre')->check()) {
            return $next($request);
        }

        $lastActivity = $request->session()->get('registre.last_activity');
        $timeout = self::TIMEOUT_MINUTES * 60;

        if ($lastActivity && (time() - $lastActivity) > $timeout) {
            Auth::guard('registre')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('registre.login')
                ->withErrors(['email' => "Votre session a expiré après 30 minutes d'inactivité. Veuillez vous reconnecter."]);
        }

        $request->session()->put('registre.last_activity', time());

        return $next($request);
    }
}
