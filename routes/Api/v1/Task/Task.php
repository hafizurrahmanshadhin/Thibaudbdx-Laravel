<?php

use App\Http\Controllers\Api\Prospect\Task\Taskcontroller;
use Illuminate\Support\Facades\Route;

Route::controller(Taskcontroller::class)->middleware('auth.jwt')->group(function () {
    Route::get('/task-list', 'index');
    Route::post('/task-create', 'create');
    Route::get('/task-details/{id}', 'details');
    Route::post('/task-update/{id}', 'update');
    Route::delete('/task-destroy/{id}', 'destroy');
});