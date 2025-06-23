<?php

use App\Http\Controllers\Api\Prospect\Voice\VoiceController;
use Illuminate\Support\Facades\Route;

Route::controller(VoiceController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/voice-list', 'index');
    Route::post('/voice-create', 'create');
    Route::get('/voice-details/{id}', 'details');
    Route::post('/voice-update/{id}', 'update');
    Route::delete('/voice-destroy/{id}', 'destroy');
});
