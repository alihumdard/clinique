<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin / CMS routes
|--------------------------------------------------------------------------
|
| Screens are built in Module 2; the CRUD behind them follows. Everything
| except sign-in sits behind the auth middleware so an unauthenticated
| visitor can never reach an administration screen.
|
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // Guest-only: an authenticated admin hitting /admin/login goes to the dashboard.
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'show'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])->name('login.attempt');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

        Route::view('/', 'admin.dashboard')->name('dashboard');

        /*
        | Content management screens. Each renders its listing view; the
        | create/edit/delete actions are wired in Module 2.
        */
        Route::view('/pages', 'admin.pages')->name('pages');
        Route::view('/pages/nouvelle', 'admin.pages')->name('pages.create');
        Route::view('/pages/categories', 'admin.pages')->name('pages.categories');
        Route::view('/actualites', 'admin.news')->name('news');
        Route::view('/web-tv', 'admin.webtv')->name('webtv');
        Route::view('/partenaires', 'admin.partners')->name('partners');
        Route::view('/equipe', 'admin.team')->name('team');
        Route::view('/activites', 'admin.activities')->name('activities');
        Route::view('/activites/nouvelle', 'admin.activities')->name('activities.create');
        Route::view('/fiches-pratiques', 'admin.guides')->name('guides');
        Route::view('/rapports-annuels', 'admin.reports')->name('reports');
        Route::view('/parametres', 'admin.settings')->name('settings');
        Route::view('/newsletter', 'admin.newsletter')->name('newsletter');
    });
});
