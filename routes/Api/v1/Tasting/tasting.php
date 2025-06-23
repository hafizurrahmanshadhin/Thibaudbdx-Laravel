<?php

use App\Http\Controllers\Api\Prospect\Tasting\TastingController;
use Illuminate\Support\Facades\Route;

Route::controller(TastingController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/tasting-list', 'index');
    Route::post('/tasting-create', 'create');
    Route::get('/tasting-details/{id}', 'details');
    Route::post('/tasting-update/{id}', 'update');
    Route::delete('/tasting-destroy/{id}', 'destroy');
});
