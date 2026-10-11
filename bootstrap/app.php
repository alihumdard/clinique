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
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Security headers on every HTML response (Module 4 checklist).
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Public site is served over HTTPS in production; force it there
        // so mixed-content and protocol-downgrade cannot occur.
        $middleware->trustProxies(at: '*');

        // Unauthenticated visitors to /admin are sent to the admin sign-in,
        // not to a generic 'login' route that this application has no need for.
        $middleware->redirectGuestsTo(fn () => route('admin.login'));

        // An already-signed-in admin hitting the sign-in page goes to the
        // dashboard, not to the public home page.
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
