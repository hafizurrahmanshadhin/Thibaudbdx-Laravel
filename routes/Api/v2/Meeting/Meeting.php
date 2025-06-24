<?php

use App\Http\Controllers\Api\Client\Meeting\MeetingController;
use Illuminate\Support\Facades\Route;

Route::controller(MeetingController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/client-meeting-list', 'index');
    Route::post('/client-meeting-create', 'create');
    Route::get('/client-meeting-details/{id}', 'details');
    Route::post('/client-meeting-update/{id}', 'update');
    Route::delete('/client-meeting-destroy/{id}', 'destroy');
});
