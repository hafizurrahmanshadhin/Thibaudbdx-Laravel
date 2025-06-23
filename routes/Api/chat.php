<?php

use App\Http\Controllers\Api\Chat\MessageController;
use Illuminate\Support\Facades\Route;

//! Message Routes
Route::controller(MessageController::class)->group(function () {
    Route::get('/messages/{user}', 'GetMessages');
    Route::post('/messages/{user}', 'SendMessage');
    Route::get('/users-with-last-message', 'GetUsersWithLastMessage');
});
