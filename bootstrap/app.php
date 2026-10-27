<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        then: function () {
            // Admin area: shared web middleware plus the idle-session guard.
            Route::middleware(['web', \App\Http\Middleware\AdminSessionTimeout::class])
                ->group(__DIR__.'/../routes/admin.php');

            /*
             * Registre numérique (module 3). Its own guard and its own
             * idle timeout, and like the admin group it is registered
             * before the CMS catch-all below so /registre is not
             * swallowed by it.
             */
            Route::middleware(['web', \App\Http\Middleware\RegistreSessionTimeout::class])
                ->group(__DIR__.'/../routes/registre.php');

            /*
             * CMS pages, registered last on purpose.
             *
             * This route matches any single segment, so it has to come
             * after every other route in the application — including the
             * admin group, which routes/web.php is loaded before. Declaring
             * it at the end of web.php was not enough: it swallowed /admin.
             */
            Route::middleware('web')->get('/{page}', [\App\Http\Controllers\PageController::class, 'show'])
                ->name('pages.show');
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Security headers on every HTML response (Module 4 checklist).
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        /*
         * The visitor's language. Appended, not prepended: the choice is
         * in a cookie, and a middleware that runs before the cookies are
         * decrypted reads an encrypted string and falls back to French on
         * every request.
         */
        $middleware->web(append: [\App\Http\Middleware\SetLocale::class]);


        // Screens an éditeur de contenu may not open: settings,
        // référentiels, the public's personal data, permanent deletion.
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdministrator::class,
        ]);

        // Public site is served over HTTPS in production; force it there
        // so mixed-content and protocol-downgrade cannot occur.
        $middleware->trustProxies(at: '*');

        /*
         * Two signed-in areas, so the redirect depends on which one was
         * asked for: a visitor turned away from the Registre must land on
         * the Registre's sign-in, not on the website's — the two have
         * different accounts, and sending someone to the wrong one reads
         * as "your password is wrong".
         */
        $middleware->redirectGuestsTo(fn ($request) => $request->is('registre', 'registre/*')
            ? route('registre.login')
            : route('admin.login'));

        $middleware->redirectUsersTo(fn ($request) => $request->is('registre', 'registre/*')
            ? route('registre.dashboard')
            : route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
