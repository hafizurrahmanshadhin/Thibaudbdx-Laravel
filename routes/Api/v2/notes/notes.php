<?php

use App\Http\Controllers\Api\Client\Notes\NotesController;
use Illuminate\Support\Facades\Route;

Route::controller(NotesController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/client-notes-list', 'index');
    Route::post('/client-notes-create', 'create');
    Route::get('/client-notes-details/{id}', 'details');
    Route::post('/client-notes-update/{id}', 'update');
    Route::delete('/client-notes-destroy/{id}', 'destroy');
});
