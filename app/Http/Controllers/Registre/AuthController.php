<?php

namespace App\Http\Controllers\Registre;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Sign-in to the Registre numérique.
 *
 * Same account-security rules as the CMS — attempt throttling, a new
 * session id on sign-in, a full invalidation on sign-out — but against
 * the registre guard, so signing in here grants nothing on the public CMS
 * and signing in there grants nothing here.
 */
class AuthController extends Controller
{
    protected const MAX_ATTEMPTS = 5;

    protected const DECAY_SECONDS = 300;

    public function show(): View
    {
        return view('registre.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ], [], [
            'email' => 'adresse e-mail',
            'password' => 'mot de passe',
        ]);

        $this->ensureIsNotRateLimited($request);

        // is_active is part of the credentials, not a check afterwards: a
        // disabled account must fail the same way a wrong password does,
        // and never reach a signed-in state at all.
        $attempted = Auth::guard('registre')->attempt(
            $credentials + ['is_active' => true],
            $request->boolean('remember')
        );

        if (! $attempted) {
            RateLimiter::hit($this->throttleKey($request), self::DECAY_SECONDS);

            // One generic message: naming which field was wrong would let
            // an attacker enumerate the Clinic's internal accounts.
            throw ValidationException::withMessages([
                'email' => 'Ces identifiants ne correspondent à aucun compte.',
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        $request->session()->regenerate();

        // Recorded on the account rather than only in the session: the
        // Clinic needs to see an account that is no longer used.
        Auth::guard('registre')->user()->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route('registre.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('registre')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('registre.login');
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), self::MAX_ATTEMPTS)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => sprintf(
                'Trop de tentatives de connexion. Veuillez réessayer dans %d minute(s).',
                ceil($seconds / 60)
            ),
        ]);
    }

    /** Separate prefix from the CMS: the two throttles are not shared. */
    protected function throttleKey(Request $request): string
    {
        return 'registre|'.mb_strtolower((string) $request->input('email')).'|'.$request->ip();
    }
}
