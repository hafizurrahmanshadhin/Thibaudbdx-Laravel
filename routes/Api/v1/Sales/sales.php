<?php

use App\Http\Controllers\Api\Prospect\Sales\SalesController;
use Illuminate\Support\Facades\Route;

Route::controller(SalesController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/prospect-sales-list', 'index');
    Route::post('/prospect-sales-create', 'create');
    Route::get('/prospect-sales-details/{id}', 'details');
    Route::post('/prospect-sales-update/{id}', 'update');
    Route::delete('/prospect-sales-destroy/{id}', 'destroy');
});
