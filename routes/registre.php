<?php

use App\Http\Controllers\Registre\AnalysisController;
use App\Http\Controllers\Registre\AssignmentController;
use App\Http\Controllers\Registre\AuditLogController;
use App\Http\Controllers\Registre\AuthController;
use App\Http\Controllers\Registre\DeadlineController;
use App\Http\Controllers\Registre\DocumentController;
use App\Http\Controllers\Registre\DossierController;
use App\Http\Controllers\Registre\DashboardController;
use App\Http\Controllers\Registre\SubmissionController;
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

        /*
        | Online requests, and what becomes of them: accepted, which opens
        | a dossier and moves the attachments to it, or refused, which
        | removes them. Both are recorded in the access log.
        */
        Route::get('/demandes', [SubmissionController::class, 'index'])->name('submissions');
        Route::get('/demandes/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
        Route::post('/demandes/{submission}/accepter', [SubmissionController::class, 'accept'])->name('submissions.accept');
        Route::post('/demandes/{submission}/refuser', [SubmissionController::class, 'refuse'])->name('submissions.refuse');
        Route::get('/demandes/{submission}/piece/{attachment}', [SubmissionController::class, 'attachment'])->name('submissions.attachment');

        /*
        | Dossiers. The export is declared before the {dossier} route so
        | "export" is not read as a reference.
        */
        Route::get('/dossiers', [DossierController::class, 'index'])->name('dossiers');
        Route::get('/dossiers/export', [DossierController::class, 'export'])->name('dossiers.export');
        // Walk-in: declared before {dossier} so "nouveau" is not read as
        // a reference.
        Route::get('/dossiers/nouveau', [DossierController::class, 'create'])->name('dossiers.create');
        Route::post('/dossiers', [DossierController::class, 'store'])->name('dossiers.store');
        Route::get('/dossiers/{dossier}', [DossierController::class, 'show'])->name('dossiers.show');
        Route::get('/dossiers/{dossier}/document/{document}', [DossierController::class, 'document'])->name('dossiers.document');

        Route::post('/dossiers/{dossier}/affectations', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::post('/dossiers/{dossier}/affectations/{assignment}/terminer', [AssignmentController::class, 'end'])->name('assignments.end');

        Route::post('/dossiers/{dossier}/echeances', [DeadlineController::class, 'store'])->name('deadlines.store');
        Route::post('/dossiers/{dossier}/echeances/{deadline}/faite', [DeadlineController::class, 'complete'])->name('deadlines.complete');

        Route::post('/dossiers/{dossier}/documents', [DocumentController::class, 'store'])->name('documents.store');

        Route::post('/dossiers/{dossier}/analyse', [AnalysisController::class, 'save'])->name('analysis.save');
        Route::post('/dossiers/{dossier}/analyse/valider', [AnalysisController::class, 'validateAnalysis'])->name('analysis.validate');

        // Read-only, by design: nothing edits or deletes a line.
        Route::get('/journal', [AuditLogController::class, 'index'])->name('journal');
    });
});
