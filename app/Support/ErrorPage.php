<?php

namespace App\Support;

use Illuminate\Support\Facades\Request;

/**
 * Chooses the shell an error page is drawn in.
 *
 * Every error view used to extend the public layout, so a 404 inside the
 * administration answered with the public site's header, the Clinic's
 * telephone number and the FR/EN switcher — the administrator was simply
 * ejected from the area they were working in. The administration and the
 * registre get a plain shell instead: no navigation to the wrong site, and
 * no dependency on who is signed in, which an error page cannot assume.
 */
final class ErrorPage
{
    public static function layout(): string
    {
        return Request::is('admin', 'admin/*', 'registre', 'registre/*')
            ? 'layouts.error'
            : 'layouts.app';
    }
}
