<?php
//customer create

use App\Http\Controllers\Api\CSV\CustomerCSVImportController;
use Illuminate\Support\Facades\Route;

Route::controller(CustomerCSVImportController::class)->middleware('auth.jwt')->group(function () {
    Route::post('/import-customers', 'import');
});

