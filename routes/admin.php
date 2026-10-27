<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CallController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\GuideController;
use App\Http\Controllers\Admin\ReferenceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TeamController;
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

        Route::get('/', DashboardController::class)->name('dashboard');

        /*
        | Content management screens. Each renders its listing view; the
        | create/edit/delete actions are wired in Module 2.
        */
        /*
        | Pages the Clinic writes themselves. The site's own pages are
        | designed templates with their own routes, so they appear on this
        | screen as a read-only reference rather than as editable rows.
        */
        Route::get('/pages', [PageController::class, 'index'])->name('pages');
        Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
        Route::put('/pages/{page:id}', [PageController::class, 'update'])->name('pages.update');
        Route::post('/pages/{page:id}/dupliquer', [PageController::class, 'duplicate'])->name('pages.duplicate');
        Route::delete('/pages/{page:id}', [PageController::class, 'destroy'])->name('pages.destroy');
        Route::post('/pages/{id}/restaurer', [PageController::class, 'restore'])->name('pages.restore');
        Route::delete('/pages/{id}/definitif', [PageController::class, 'forceDestroy'])->middleware('admin')->name('pages.force-destroy');
        /*
        | Actualités — the first content type wired to the database.
        | Restore and permanent delete are POST/DELETE because both change
        | state; neither may be reachable by following a link.
        */
        Route::get('/actualites', [ArticleController::class, 'index'])->name('news');
        Route::post('/actualites', [ArticleController::class, 'store'])->name('news.store');
        Route::put('/actualites/{article:id}', [ArticleController::class, 'update'])->name('news.update');
        Route::post('/actualites/{article:id}/dupliquer', [ArticleController::class, 'duplicate'])->name('news.duplicate');
        Route::delete('/actualites/{article:id}', [ArticleController::class, 'destroy'])->name('news.destroy');
        Route::post('/actualites/{id}/restaurer', [ArticleController::class, 'restore'])->name('news.restore');
        Route::delete('/actualites/{id}/definitif', [ArticleController::class, 'forceDestroy'])->middleware('admin')->name('news.force-destroy');
        /*
        | Web TV. The capsule is published by pasting a link: the provider
        | and its id are parsed server-side, never typed by the editor.
        */
        Route::get('/web-tv', [VideoController::class, 'index'])->name('webtv');
        Route::post('/web-tv', [VideoController::class, 'store'])->name('webtv.store');
        Route::put('/web-tv/{video:id}', [VideoController::class, 'update'])->name('webtv.update');
        Route::post('/web-tv/{video:id}/dupliquer', [VideoController::class, 'duplicate'])->name('webtv.duplicate');
        Route::delete('/web-tv/{video:id}', [VideoController::class, 'destroy'])->name('webtv.destroy');
        Route::post('/web-tv/{id}/restaurer', [VideoController::class, 'restore'])->name('webtv.restore');
        Route::delete('/web-tv/{id}/definitif', [VideoController::class, 'forceDestroy'])->middleware('admin')->name('webtv.force-destroy');

        /*
        | Partenaires and équipe. Both are a list the Clinic orders and
        | shows or hides rather than a timeline, so each row carries
        | is_visible and position instead of a publication date, and each
        | has a toggle of its own.
        */
        Route::get('/partenaires', [PartnerController::class, 'index'])->name('partners');
        Route::post('/partenaires', [PartnerController::class, 'store'])->name('partners.store');
        Route::put('/partenaires/{partner:id}', [PartnerController::class, 'update'])->name('partners.update');
        Route::post('/partenaires/{partner:id}/dupliquer', [PartnerController::class, 'duplicate'])->name('partners.duplicate');
        Route::delete('/partenaires/{partner:id}', [PartnerController::class, 'destroy'])->name('partners.destroy');
        Route::post('/partenaires/{partner:id}/visibilite', [PartnerController::class, 'toggle'])->name('partners.toggle');
        Route::post('/partenaires/{id}/restaurer', [PartnerController::class, 'restore'])->name('partners.restore');
        Route::delete('/partenaires/{id}/definitif', [PartnerController::class, 'forceDestroy'])->middleware('admin')->name('partners.force-destroy');

        Route::get('/equipe', [TeamController::class, 'index'])->name('team');
        Route::post('/equipe', [TeamController::class, 'store'])->name('team.store');
        Route::put('/equipe/{member:id}', [TeamController::class, 'update'])->name('team.update');
        Route::post('/equipe/{member:id}/dupliquer', [TeamController::class, 'duplicate'])->name('team.duplicate');
        Route::delete('/equipe/{member:id}', [TeamController::class, 'destroy'])->name('team.destroy');
        Route::post('/equipe/{member:id}/visibilite', [TeamController::class, 'toggle'])->name('team.toggle');
        Route::post('/equipe/{id}/restaurer', [TeamController::class, 'restore'])->name('team.restore');
        Route::delete('/equipe/{id}/definitif', [TeamController::class, 'forceDestroy'])->middleware('admin')->name('team.force-destroy');


        /*
        | Activités et galerie photos. The gallery is a hasMany on the
        | activity, so photos are added with the activity and removed one
        | at a time from its own row.
        */
        Route::get('/activites', [ActivityController::class, 'index'])->name('activities');
        Route::post('/activites', [ActivityController::class, 'store'])->name('activities.store');
        Route::put('/activites/{activity:id}', [ActivityController::class, 'update'])->name('activities.update');
        Route::post('/activites/{activity:id}/dupliquer', [ActivityController::class, 'duplicate'])->name('activities.duplicate');
        Route::delete('/activites/{activity:id}', [ActivityController::class, 'destroy'])->name('activities.destroy');
        Route::post('/activites/{id}/restaurer', [ActivityController::class, 'restore'])->name('activities.restore');
        Route::delete('/activites/{id}/definitif', [ActivityController::class, 'forceDestroy'])->middleware('admin')->name('activities.force-destroy');
        Route::delete('/activites/{activity:id}/photos/{photo}', [ActivityController::class, 'destroyPhoto'])->name('activities.photos.destroy');


        /*
        | Fiches pratiques and rapports annuels. Both carry a PDF, which is
        | written to a private disk and only ever served through
        | DocumentController.
        */
        Route::get('/fiches-pratiques', [GuideController::class, 'index'])->name('guides');
        Route::post('/fiches-pratiques', [GuideController::class, 'store'])->name('guides.store');
        Route::put('/fiches-pratiques/{guide:id}', [GuideController::class, 'update'])->name('guides.update');
        Route::post('/fiches-pratiques/{guide:id}/dupliquer', [GuideController::class, 'duplicate'])->name('guides.duplicate');
        Route::get('/fiches-pratiques/{guide:id}/telecharger', [GuideController::class, 'download'])->name('guides.download');
        Route::delete('/fiches-pratiques/{guide:id}', [GuideController::class, 'destroy'])->name('guides.destroy');
        Route::post('/fiches-pratiques/{id}/restaurer', [GuideController::class, 'restore'])->name('guides.restore');
        Route::delete('/fiches-pratiques/{id}/definitif', [GuideController::class, 'forceDestroy'])->middleware('admin')->name('guides.force-destroy');

        Route::get('/rapports-annuels', [ReportController::class, 'index'])->name('reports');
        Route::post('/rapports-annuels', [ReportController::class, 'store'])->name('reports.store');
        Route::put('/rapports-annuels/{report:id}', [ReportController::class, 'update'])->name('reports.update');
        Route::post('/rapports-annuels/{report:id}/dupliquer', [ReportController::class, 'duplicate'])->name('reports.duplicate');
        Route::get('/rapports-annuels/{report:id}/telecharger', [ReportController::class, 'download'])->name('reports.download');
        Route::delete('/rapports-annuels/{report:id}', [ReportController::class, 'destroy'])->name('reports.destroy');
        Route::post('/rapports-annuels/{id}/restaurer', [ReportController::class, 'restore'])->name('reports.restore');
        Route::delete('/rapports-annuels/{id}/definitif', [ReportController::class, 'forceDestroy'])->middleware('admin')->name('reports.force-destroy');

        /*
        | Appels à candidatures. Not one of the twelve designed screens:
        | the content type was in the plan and in the schema, but the
        | screen inventory never covered it.
        */
        Route::get('/appels', [CallController::class, 'index'])->name('calls');
        Route::post('/appels', [CallController::class, 'store'])->name('calls.store');
        Route::put('/appels/{call:id}', [CallController::class, 'update'])->name('calls.update');
        Route::post('/appels/{call:id}/dupliquer', [CallController::class, 'duplicate'])->name('calls.duplicate');
        Route::delete('/appels/{call:id}', [CallController::class, 'destroy'])->name('calls.destroy');
        Route::post('/appels/{id}/restaurer', [CallController::class, 'restore'])->name('calls.restore');
        Route::delete('/appels/{id}/definitif', [CallController::class, 'forceDestroy'])->middleware('admin')->name('calls.force-destroy');

        /*
        |--------------------------------------------------------------------------
        | Administrator-only screens
        |--------------------------------------------------------------------------
        |
        | Module 2 asks for "les autorisations des éditeurs de contenu": an
        | éditeur writes, publishes and withdraws content, and that is all.
        | What sits below is a different kind of work — the settings the
        | whole site reads, the référentiels other screens choose from, and
        | the personal data the public leaves — so it is kept for an
        | administrateur. The sidebar hides this group from an éditeur too.
        |
        */
        Route::middleware('admin')->group(function () {

            /*
            | Paramètres du site. These are the CMS-editable half of
            | config/clinique.php, plus the bandeau d'état.
            */
            Route::get('/parametres', [SettingController::class, 'index'])->name('settings');
            Route::put('/parametres', [SettingController::class, 'update'])->name('settings.update');

            /*
            | Référentiels: the fee grid and the legal domains. Two flat lists
            | of a dozen short rows each, edited in place on one screen rather
            | than taking one apiece.
            */
            Route::get('/referentiels', [ReferenceController::class, 'index'])->name('references');

            Route::post('/referentiels/tarifs', [ReferenceController::class, 'storeFee'])->name('references.fees.store');
            Route::put('/referentiels/tarifs/{fee}', [ReferenceController::class, 'updateFee'])->name('references.fees.update');
            Route::delete('/referentiels/tarifs/{fee}', [ReferenceController::class, 'destroyFee'])->name('references.fees.destroy');

            Route::post('/referentiels/domaines', [ReferenceController::class, 'storeDomain'])->name('references.domains.store');
            Route::put('/referentiels/domaines/{domain}', [ReferenceController::class, 'updateDomain'])->name('references.domains.update');
            Route::delete('/referentiels/domaines/{domain}', [ReferenceController::class, 'destroyDomain'])->name('references.domains.destroy');

            /*
            | Messages de contact. Not one of the twelve designed screens:
            | without it the messages the public form stores would be
            | unreachable, so the form would collect personal data nobody
            | could read or delete.
            */
            Route::get('/messages', [MessageController::class, 'index'])->name('messages');
            Route::post('/messages/{message}/traite', [MessageController::class, 'handle'])->name('messages.handle');
            Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

            /*
            | Newsletter. Phase 2 collects addresses and exports them; the
            | Clinic sends with their own tool, so there is nothing to send
            | from here.
            */
            Route::get('/newsletter', [NewsletterController::class, 'index'])->name('newsletter');
            Route::get('/newsletter/export', [NewsletterController::class, 'export'])->name('newsletter.export');
            Route::post('/newsletter/{subscriber}/desinscrire', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
            Route::delete('/newsletter/{subscriber}', [NewsletterController::class, 'destroy'])->name('newsletter.destroy');
        });
    });
});
