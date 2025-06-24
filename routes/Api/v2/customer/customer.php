
<?php

use App\Http\Controllers\Api\Client\CustomerController;
use Illuminate\Support\Facades\Route;

Route::controller(CustomerController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/client-customer-list', 'index');
    Route::post('/client-customer-create', 'create');
    Route::get('/client-customer-details/{id}', 'details');
    Route::post('/client-customer-update/{id}', 'update');
    Route::delete('/client-customer-destroy/{id}', 'destroy');
});
