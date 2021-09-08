<?php

use Illuminate\Support\Facades\Route;
use Sislamrafi\Webartisan\App\Http\Controllers\ArtisanController;
/*
Route::get('artisan', function () {
    return "web artisan";
});
*/

Route::name('artisan.')->prefix('artisan')->name('artisan.')->group(function () {
    Route::get('/', [ArtisanController::class,'index'])->name('artisan');
    Route::post('/submit', [ArtisanController::class,'submit'])->name('submit');
});
