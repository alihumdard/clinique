<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ends an administration session after a period of inactivity.
 *
 * Required by the security plan: "Deconnexion automatique apres trente
 * minutes d'inactivite sur le registre, les postes de la Clinique etant
 * partages." Clinic workstations are shared, so an unattended session
 * must not stay open.
 */
class AdminSessionTimeout
{
    /** Idle minutes before the session is ended. */
    protected const TIMEOUT_MINUTES = 30;

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $lastActivity = $request->session()->get('admin.last_activity');
        $timeout = self::TIMEOUT_MINUTES * 60;

        if ($lastActivity && (time() - $lastActivity) > $timeout) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->withErrors(['email' => 'Votre session a expiré après 30 minutes d\'inactivité. Veuillez vous reconnecter.']);
        }

        $request->session()->put('admin.last_activity', time());

        return $next($request);
    }
}
