<?php

use App\Http\Controllers\Api\Client\Voice\VoiceController;
use Illuminate\Support\Facades\Route;

Route::controller(VoiceController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/client-voice-list', 'index');
    Route::post('/client-voice-create', 'create');
    Route::get('/client-voice-details/{id}', 'details');
    Route::post('/client-voice-update/{id}', 'update');
    Route::delete('/client-voice-destroy/{id}', 'destroy');
});
