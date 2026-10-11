<?php

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

Route::get('/actualites', function () {
    return view('news.index');
})->name('news.index');

Route::get('/actualites/conference-acces-droit-personnes-vulnerables', function () {
    return view('news.show');
})->name('news.show');

// Placeholder routes so header nav links resolve while each page is built.
// Replaced with real controllers/views as each page is designed.
Route::get('/ressources', fn () => view('home'))->name('resources');
Route::get('/web-tv', fn () => view('home'))->name('webtv.index');
Route::get('/contact', fn () => view('home'))->name('contact');
