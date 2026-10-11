<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin / CMS routes (scaffold only - full CRUD implemented in Module 2)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', function () {
        return view('admin.auth.login');
    })->name('login');
});
