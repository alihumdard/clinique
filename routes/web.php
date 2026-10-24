<?php

use App\Http\Controllers\NewsController;
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

Route::get('/a-propos/equipe', function () {
    return view('team');
})->name('about.team');

Route::get('/partenaires', function () {
    return view('partners');
})->name('partners');

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
Route::get('/ressources/fiches-pratiques', fn () => view('resources.guides'))->name('resources.guides');
Route::get('/ressources/fiches-pratiques/droits-de-la-famille', fn () => view('resources.guide-show'))->name('resources.guides.show');
Route::get('/ressources/rapports-annuels', fn () => view('resources.reports'))->name('resources.reports');

/*
|--------------------------------------------------------------------------
| Activities and gallery
|--------------------------------------------------------------------------
*/

Route::get('/activites', fn () => view('activities'))->name('activities.index');
Route::get('/galerie', fn () => view('gallery'))->name('gallery');

/*
|--------------------------------------------------------------------------
| Web TV
|--------------------------------------------------------------------------
*/

Route::get('/web-tv', fn () => view('webtv.index'))->name('webtv.index');
Route::get('/web-tv/caravane-juridique-tillaberi', fn () => view('webtv.show'))->name('webtv.show');

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
