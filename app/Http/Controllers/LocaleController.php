<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

/**
 * The FR / EN switch.
 *
 * It returns the visitor to the page they were on, which is the only
 * behaviour that makes sense for a switch in the header: changing the
 * language is not a reason to lose your place.
 */
class LocaleController extends Controller
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, SetLocale::SUPPORTED, true), 404);

        // A year, and readable by the server only — the page is rendered
        // in the chosen language, so no script needs to see it.
        $cookie = Cookie::make(
            name: SetLocale::COOKIE,
            value: $locale,
            minutes: 60 * 24 * 365,
            httpOnly: true,
        );

        // back(), but never off-site: the form posts from our own pages.
        return redirect()->back(fallback: route('home'))->withCookie($cookie);
    }
}
