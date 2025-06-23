<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\CSV\CustomerCSVImportController;
use App\Http\Controllers\Api\Customer\CustomerController;
use App\Http\Controllers\Api\Tag\TagController;

// This route is for getting terms and conditions and privacy policy.
Route::get('contents', [ContentController::class, 'index'])->middleware(['throttle:10,1']);



Route::controller(TagController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/tag-list', 'index');
    Route::post('/tag-create', 'create');
    Route::get('/tag-details/{id}', 'details');
    Route::post('/tag-update/{id}', 'update');
    Route::delete('/tag-destroy/{id}', 'destroy');
});

//customer create
Route::controller(CustomerController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/customer-list', 'index');
    Route::post('/customer-create', 'create');
    Route::get('/customer-details/{id}', 'details');
    Route::post('/customer-update/{id}', 'update');
    Route::delete('/customer-destroy/{id}', 'destroy');
});

//--Prospect customer route link
require "v1/product/Product.php";
require "v1/Tasting/tasting.php";
require "v1/Voice/voice.php";
require "v1/Task/Task.php";
require "v1/activity/activity.php";
require "v1/document/document.php";

//csv file upload 
require "CSV/CustomerCSVImport.php";
