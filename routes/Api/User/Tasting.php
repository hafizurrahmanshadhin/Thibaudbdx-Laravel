<?php

use App\Http\Controllers\Api\User\UserTastingController;
use Illuminate\Support\Facades\Route;

Route::controller(UserTastingController::class)->middleware('auth.jwt')->group(function () {

    //user notes route function
    Route::get('/user-tasting-list', 'tastingList');
    Route::post('/user-tasting-create', 'tastingCreate');
    Route::get('/user-tasting-details/{id}', 'tastingdetails');
    Route::post('/user-tasting-update/{id}', 'tastingUpdate');
    Route::delete('/user-tasting-destroy/{id}', 'tastingDestroy');
});
