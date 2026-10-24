<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function store(ContactRequest $request): RedirectResponse
    {
        /*
         * The form is open to the public, so it is rate limited per address
         * on top of the validation and the honeypot. Three messages in ten
         * minutes is well above what a person needs and well below what a
         * script would send.
         */
        $key = 'contact:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()
                ->withInput()
                ->withErrors(['message' => 'Trop de messages envoyés. Merci de réessayer dans quelques minutes.']);
        }

        RateLimiter::hit($key, 600);

        $data = $request->validated();

        ContactMessage::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'],
            'message' => $data['message'],
            // When the agreement was given, which is what the retention
            // rules are read against.
            'consent_at' => now(),
            'status' => ContactMessage::STATUS_NEW,
            'ip_address' => $request->ip(),
        ]);

        /*
         * The message is stored, not emailed. No mail transport is
         * configured for this deployment, and a send that silently fails
         * would be worse than an inbox the Clinic reads: the admin screen
         * is where these arrive, and notification by email can be added
         * once the Clinic supplies SMTP credentials.
         */
        return back()
            ->with('contact', 'Votre message a bien été envoyé. Nous vous répondrons dans les meilleurs délais.')
            ->withFragment('envoyez-nous-un-message');
    }
}
