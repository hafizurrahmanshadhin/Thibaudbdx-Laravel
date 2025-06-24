<?php

use App\Http\Controllers\Api\Client\Tasting\TastingController;
use Illuminate\Support\Facades\Route;

Route::controller(TastingController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/client-tasting-list', 'index');
    Route::post('/client-tasting-create', 'create');
    Route::get('/client-tasting-details/{id}', 'details');
    Route::post('/client-tasting-update/{id}', 'update');
    Route::delete('/client-tasting-destroy/{id}', 'destroy');
});
