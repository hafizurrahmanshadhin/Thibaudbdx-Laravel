<?php

use App\Http\Controllers\Api\Client\Sales\SalesController;
use Illuminate\Support\Facades\Route;

Route::controller(SalesController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/client-sales-list', 'index');
    Route::post('/client-sales-create', 'create');
    Route::get('/client-sales-details/{id}', 'details');
    Route::post('/client-sales-update/{id}', 'update');
    Route::delete('/client-sales-destroy/{id}', 'destroy');
});
