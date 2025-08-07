<?php

use App\Http\Controllers\Api\Prospect\Notes\NotesController;
use Illuminate\Support\Facades\Route;

Route::controller(NotesController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/prospect-notes-list', 'index');
    Route::post('/prospect-notes-create', 'create');
    Route::get('/prospect-notes-details/{id}', 'details');
    Route::post('/prospect-notes-update/{id}', 'update');
    Route::delete('/prospect-notes-destroy/{id}', 'destroy');
});
