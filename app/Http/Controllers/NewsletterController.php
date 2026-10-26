<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        /*
         * The form is open to the public, so it is rate limited per address
         * as well as validated: without this, one script could fill the
         * table and send the Clinic's address reputation with it.
         */
        $key = 'newsletter:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Trop de tentatives. Merci de réessayer dans quelques minutes.']);
        }

        RateLimiter::hit($key, 600);

        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
            'name' => ['nullable', 'string', 'max:120'],
            // A field a person never sees and a script usually fills in.
            'website' => ['prohibited'],
        ], [
            'email.required' => 'Merci de saisir votre adresse e-mail.',
            'email.email' => "Cette adresse e-mail n'est pas valide.",
        ]);

        $subscriber = NewsletterSubscriber::firstOrNew(['email' => $data['email']]);

        /*
         * Someone re-subscribing after leaving is a new consent, so the
         * unsubscribe is cleared. The row is reused rather than duplicated,
         * which is also why the email column is unique.
         */
        $subscriber->fill([
            'name' => $data['name'] ?? $subscriber->name,
            'unsubscribed_at' => null,
            'confirmed_at' => $subscriber->confirmed_at ?? now(),
        ])->save();

        return back()->with('newsletter', 'Votre inscription à la newsletter est enregistrée. Merci !');
    }

    /**
     * Unsubscribing happens by following the link in an email, so it is
     * reachable without signing in — the token is what authorises it, and
     * it is the only thing that does.
     */
    public function unsubscribe(string $token): View
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->first();

        if ($subscriber && $subscriber->isSubscribed()) {
            $subscriber->unsubscribe();
        }

        return view('newsletter.unsubscribed', [
            // The same page whether the token was known or not: a stranger
            // must not be able to use this to test whether an address is
            // on the list.
            'done' => true,
        ]);
    }
}
