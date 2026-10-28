<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Administration sign-in.
 *
 * Implements the Module 4 account-security requirements: attempt
 * throttling with temporary lockout, session fixation protection on
 * login, and full session invalidation on logout.
 */
class AuthController extends Controller
{
    /** Failed attempts allowed per e-mail + IP before lockout. */
    protected const MAX_ATTEMPTS = 5;

    /** Lockout window in seconds once the limit is reached. */
    protected const DECAY_SECONDS = 300;

    public function show(): View
    {
        return view('admin.auth.login');
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

        /*
         * is_active is part of the credentials, not a check afterwards: a
         * disabled account must fail exactly as a wrong password does and
         * never reach a signed-in state at all.
         */
        if (! Auth::attempt($credentials + ['is_active' => true], $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request), self::DECAY_SECONDS);

            // One generic message: naming which field was wrong would let an
            // attacker enumerate valid administrator addresses.
            throw ValidationException::withMessages([
                'email' => 'Ces identifiants ne correspondent à aucun compte.',
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        // New session id on privilege change, against session fixation.
        $request->session()->regenerate();

        // Recorded on the account: an account nobody uses is the first
        // thing to look at when closing one.
        Auth::user()->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
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

    /** Throttle per e-mail and IP so one account cannot lock out others. */
    protected function throttleKey(Request $request): string
    {
        return mb_strtolower((string) $request->input('email')).'|'.$request->ip();
    }
}
