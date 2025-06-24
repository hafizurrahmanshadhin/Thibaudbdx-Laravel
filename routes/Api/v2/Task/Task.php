<?php

use App\Http\Controllers\Api\Client\Task\TaskController;
use Illuminate\Support\Facades\Route;

Route::controller(TaskController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/client-task-list', 'index');
    Route::post('/client-task-create', 'create');
    Route::get('/client-task-details/{id}', 'details');
    Route::post('/client-task-update/{id}', 'update');
    Route::delete('/client-task-destroy/{id}', 'destroy');
});
