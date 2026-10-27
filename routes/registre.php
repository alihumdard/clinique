<?php

use App\Http\Controllers\Registre\AuthController;
use App\Http\Controllers\Registre\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Registre numérique — module 3
|--------------------------------------------------------------------------
|
| A separate application area: its own guard, its own database, its own
| accounts. It answers under /registre for now; the plan reserves a
| distinct subdomain for it, which the UAM IT department still has to
| confirm, and moving there means setting a domain on this group and
| nothing else.
|
| Nothing here may read the public site's tables, and the public site
| must never read this one's.
|
*/

Route::prefix('registre')->name('registre.')->group(function () {

    Route::middleware('guest:registre')->group(function () {
        Route::get('/connexion', [AuthController::class, 'show'])->name('login');
        Route::post('/connexion', [AuthController::class, 'store'])->name('login.attempt');
    });

    Route::middleware('auth:registre')->group(function () {
        Route::post('/deconnexion', [AuthController::class, 'destroy'])->name('logout');

        Route::get('/', DashboardController::class)->name('dashboard');
    });
});
