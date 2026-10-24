<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\GuideController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\VideoController;
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
        /*
        | Actualités — the first content type wired to the database.
        | Restore and permanent delete are POST/DELETE because both change
        | state; neither may be reachable by following a link.
        */
        Route::get('/actualites', [ArticleController::class, 'index'])->name('news');
        Route::post('/actualites', [ArticleController::class, 'store'])->name('news.store');
        Route::put('/actualites/{article:id}', [ArticleController::class, 'update'])->name('news.update');
        Route::delete('/actualites/{article:id}', [ArticleController::class, 'destroy'])->name('news.destroy');
        Route::post('/actualites/{id}/restaurer', [ArticleController::class, 'restore'])->name('news.restore');
        Route::delete('/actualites/{id}/definitif', [ArticleController::class, 'forceDestroy'])->name('news.force-destroy');
        /*
        | Web TV. The capsule is published by pasting a link: the provider
        | and its id are parsed server-side, never typed by the editor.
        */
        Route::get('/web-tv', [VideoController::class, 'index'])->name('webtv');
        Route::post('/web-tv', [VideoController::class, 'store'])->name('webtv.store');
        Route::put('/web-tv/{video:id}', [VideoController::class, 'update'])->name('webtv.update');
        Route::delete('/web-tv/{video:id}', [VideoController::class, 'destroy'])->name('webtv.destroy');
        Route::post('/web-tv/{id}/restaurer', [VideoController::class, 'restore'])->name('webtv.restore');
        Route::delete('/web-tv/{id}/definitif', [VideoController::class, 'forceDestroy'])->name('webtv.force-destroy');
        Route::view('/partenaires', 'admin.partners')->name('partners');
        Route::view('/equipe', 'admin.team')->name('team');

        /*
        | Activités et galerie photos. The gallery is a hasMany on the
        | activity, so photos are added with the activity and removed one
        | at a time from its own row.
        */
        Route::get('/activites', [ActivityController::class, 'index'])->name('activities');
        Route::post('/activites', [ActivityController::class, 'store'])->name('activities.store');
        Route::put('/activites/{activity:id}', [ActivityController::class, 'update'])->name('activities.update');
        Route::delete('/activites/{activity:id}', [ActivityController::class, 'destroy'])->name('activities.destroy');
        Route::post('/activites/{id}/restaurer', [ActivityController::class, 'restore'])->name('activities.restore');
        Route::delete('/activites/{id}/definitif', [ActivityController::class, 'forceDestroy'])->name('activities.force-destroy');
        Route::delete('/activites/{activity:id}/photos/{photo}', [ActivityController::class, 'destroyPhoto'])->name('activities.photos.destroy');


        /*
        | Fiches pratiques and rapports annuels. Both carry a PDF, which is
        | written to a private disk and only ever served through
        | DocumentController.
        */
        Route::get('/fiches-pratiques', [GuideController::class, 'index'])->name('guides');
        Route::post('/fiches-pratiques', [GuideController::class, 'store'])->name('guides.store');
        Route::put('/fiches-pratiques/{guide:id}', [GuideController::class, 'update'])->name('guides.update');
        Route::delete('/fiches-pratiques/{guide:id}', [GuideController::class, 'destroy'])->name('guides.destroy');
        Route::post('/fiches-pratiques/{id}/restaurer', [GuideController::class, 'restore'])->name('guides.restore');
        Route::delete('/fiches-pratiques/{id}/definitif', [GuideController::class, 'forceDestroy'])->name('guides.force-destroy');

        Route::get('/rapports-annuels', [ReportController::class, 'index'])->name('reports');
        Route::post('/rapports-annuels', [ReportController::class, 'store'])->name('reports.store');
        Route::put('/rapports-annuels/{report:id}', [ReportController::class, 'update'])->name('reports.update');
        Route::delete('/rapports-annuels/{report:id}', [ReportController::class, 'destroy'])->name('reports.destroy');
        Route::post('/rapports-annuels/{id}/restaurer', [ReportController::class, 'restore'])->name('reports.restore');
        Route::delete('/rapports-annuels/{id}/definitif', [ReportController::class, 'forceDestroy'])->name('reports.force-destroy');

        Route::view('/parametres', 'admin.settings')->name('settings');
        Route::view('/newsletter', 'admin.newsletter')->name('newsletter');
    });
});
