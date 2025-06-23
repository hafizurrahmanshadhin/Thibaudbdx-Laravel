<?php

use App\Http\Controllers\Api\Prospect\Document\DocumentController;
use Illuminate\Support\Facades\Route;

Route::controller(DocumentController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/document-list', 'index');
    Route::post('/document-create', 'create');
    Route::get('/document-details/{id}', 'details');
    Route::post('/document-update/{id}', 'update');
    Route::delete('/document-destroy/{id}', 'destroy');
});
