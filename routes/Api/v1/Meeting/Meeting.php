<?php

use App\Http\Controllers\Api\Prospect\Meeting\MeetingController;
use Illuminate\Support\Facades\Route;

Route::controller(MeetingController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/meeting-list', 'index');
    Route::post('/meeting-create', 'create');
    Route::get('/meeting-details/{id}', 'details');
    Route::post('/meeting-update/{id}', 'update');
    Route::delete('/meeting-destroy/{id}', 'destroy');
});