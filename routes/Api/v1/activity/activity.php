<?php

use App\Http\Controllers\Api\Prospect\Activity\ActivityController;
use Illuminate\Support\Facades\Route;

Route::controller(ActivityController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/activity-list', 'ActivityList');
});
