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
