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

// Placeholder routes so header nav links resolve while each page is built.
// Replaced with real controllers/views as each page is designed.
Route::get('/services', fn () => view('home'))->name('services');
Route::get('/actualites', fn () => view('home'))->name('news.index');
Route::get('/ressources', fn () => view('home'))->name('resources');
Route::get('/web-tv', fn () => view('home'))->name('webtv.index');
Route::get('/contact', fn () => view('home'))->name('contact');
