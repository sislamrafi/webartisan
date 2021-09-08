<?php

use Illuminate\Support\Facades\Route;
use Sislamrafi\Webartisan\App\Http\Controllers\ArtisanController;

Route::name('artisan.')->prefix('artisan')->name('api.artisan.')->group(function () {
    Route::post('/submit', [ArtisanController::class,'submit'])->name('submit');
});
