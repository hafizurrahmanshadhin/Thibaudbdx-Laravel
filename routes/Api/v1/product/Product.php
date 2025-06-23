<?php

use App\Http\Controllers\Api\Product\ProductControler;
use Illuminate\Support\Facades\Route;

Route::controller(ProductControler::class)->middleware('auth.jwt')->group(function () {
    Route::get('/product-list', 'index');
    Route::post('/product-create', 'create');
    Route::get('/product-details/{id}', 'details');
    Route::post('/product-update/{id}', 'update');
    Route::delete('/product-destroy/{id}', 'destroy');
    Route::get('/product-pdf-generate/{id}', 'ProductPDF');
});
