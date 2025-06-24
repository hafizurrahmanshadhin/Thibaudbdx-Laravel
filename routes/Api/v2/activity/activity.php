<?php

use App\Http\Controllers\Api\Client\Activity\ActivityController;
use Illuminate\Support\Facades\Route;

Route::controller(ActivityController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/client-activity-list', 'ActivityList');
});
