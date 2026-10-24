<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\WebTvController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site routes (Module 2 fills this in with full page inventory)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/a-propos', function () {
    return view('about');
})->name('about');

Route::get('/a-propos/equipe', [TeamController::class, 'index'])->name('about.team');

Route::get('/partenaires', [PartnerController::class, 'index'])->name('partners');

Route::get('/services', function () {
    return view('services');
})->name('services');

Route::get('/services/processus-de-consultation', function () {
    return view('services.process');
})->name('services.process');

Route::get('/services/conditions-acces-gratuite', function () {
    return view('services.eligibility');
})->name('services.eligibility');

Route::get('/services/tarifs-administratifs', function () {
    return view('services.fees');
})->name('services.fees');

Route::get('/actualites', [NewsController::class, 'index'])->name('news.index');
Route::get('/actualites/{article}', [NewsController::class, 'show'])->name('news.show');

/*
|--------------------------------------------------------------------------
| Resources (fiches pratiques, rapports annuels)
|--------------------------------------------------------------------------
*/

Route::redirect('/ressources', '/ressources/fiches-pratiques')->name('resources');
Route::get('/ressources/fiches-pratiques', [ResourceController::class, 'guides'])->name('resources.guides');
Route::get('/ressources/fiches-pratiques/{guide}', [ResourceController::class, 'guide'])->name('resources.guides.show');
Route::get('/ressources/rapports-annuels', [ResourceController::class, 'reports'])->name('resources.reports');

/*
| Stored PDFs. They sit on a private disk outside the web root, so the
| only way to one is through the controller, which checks the row is
| published and counts the download.
*/
Route::get('/ressources/fiches-pratiques/{guide}/telecharger', [DocumentController::class, 'guide'])->name('documents.guide');
Route::get('/ressources/rapports-annuels/{report}/telecharger', [DocumentController::class, 'report'])->name('documents.report');

/*
|--------------------------------------------------------------------------
| Activities and gallery
|--------------------------------------------------------------------------
*/

Route::get('/activites', [ActivityController::class, 'index'])->name('activities.index');
Route::get('/galerie', [ActivityController::class, 'gallery'])->name('gallery');

/*
|--------------------------------------------------------------------------
| Web TV
|--------------------------------------------------------------------------
*/

Route::get('/web-tv', [WebTvController::class, 'index'])->name('webtv.index');
Route::get('/web-tv/{video}', [WebTvController::class, 'show'])->name('webtv.show');

/*
|--------------------------------------------------------------------------
| Engagement and contact
|--------------------------------------------------------------------------
*/

Route::get('/rejoindre', fn () => view('join'))->name('join');
Route::get('/contact', fn () => view('contact'))->name('contact');
Route::get('/recherche', fn () => view('search'))->name('search');

/*
|--------------------------------------------------------------------------
| Newsletter
|--------------------------------------------------------------------------
|
| Subscribing is a POST from the form on the actualités page. Unsubscribing
| is a GET because it is followed from a link in an email, and the token in
| the URL is what authorises it.
|
*/

Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');
Route::get('/newsletter/desinscription/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

/*
|--------------------------------------------------------------------------
| Pre-saisine (Module 3 implements the flow; route reserved so the
| header and homepage CTAs resolve rather than pointing at '#')
|--------------------------------------------------------------------------
*/

Route::get('/pre-saisine', fn () => view('home'))->name('presaisine.index');

/*
|--------------------------------------------------------------------------
| Mandatory legal pages
|--------------------------------------------------------------------------
*/

Route::get('/mentions-legales', fn () => view('legal.notice'))->name('legal.notice');
Route::get('/conditions-utilisation', fn () => view('legal.terms'))->name('legal.terms');
Route::get('/politique-confidentialite', fn () => view('legal.privacy'))->name('legal.privacy');

/*
|--------------------------------------------------------------------------
| 404 page preview (direct URL so the design can be reviewed without
| having to visit a broken link)
|--------------------------------------------------------------------------
*/

Route::get('/404', fn () => response()->view('errors.404', [], 404))->name('error.404');
