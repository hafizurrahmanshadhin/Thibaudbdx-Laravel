<?php

use App\Http\Controllers\Api\Client\Document\DocumentController;
use Illuminate\Support\Facades\Route;

Route::controller(DocumentController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/client-document-list', 'index');
    Route::post('/client-document-create', 'create');
    Route::get('/client-document-details/{id}', 'details');
    Route::post('/client-document-update/{id}', 'update');
    Route::delete('/client-document-destroy/{id}', 'destroy');
});
