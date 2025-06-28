<?php

use App\Http\Controllers\ResetController;
use App\Http\Controllers\Web\Frontend\HomeController;
use App\Http\Controllers\Web\Frontend\PageController;
use Illuminate\Support\Facades\Route;

// Route for Reset Database and Optimize Clear and Cache
Route::get('/reset', [ResetController::class, 'Reset'])->name('reset');
Route::get('/cache', [ResetController::class, 'Cache'])->name('cache');

// Route for Landing Page
Route::get('/', [HomeController::class, 'index'])->name('index');

// Route for Dynamic Pages (Privacy Policy, Terms and Conditions)
Route::get('/page/{type}', [PageController::class, 'dynamicPage'])
    ->whereIn('type', ['privacyPolicy', 'termsAndConditions'])
    ->name('dynamicPage.show');
