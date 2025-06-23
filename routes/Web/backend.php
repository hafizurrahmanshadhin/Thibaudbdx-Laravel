<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\FAQController;
use App\Http\Controllers\Web\Backend\ServiceController;
use App\Http\Controllers\Web\Backend\DashboardController;

// Route for Admin Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Route for Service Backend
Route::controller(ServiceController::class)->group(function () {
    Route::get('/service', 'index')->name('service.index');
    Route::post('/service/store', 'store')->name('service.store');
    Route::put('/service/update/{id}', 'update')->name('service.update');
    Route::get('/service/status/{id}', 'status')->name('service.status');
    Route::delete('/service/destroy/{id}', 'destroy')->name('service.destroy');
});

// Route for FAQ Page
Route::controller(FAQController::class)->group(function () {
    Route::get('/faq', 'index')->name('faq.index');
    Route::get('/faq/show/{id}', 'show')->name('faq.show');
    Route::get('/faq/create', 'create')->name('faq.create');
    Route::post('/faq/store', 'store')->name('faq.store');
    Route::get('/faq/edit/{id}', 'edit')->name('faq.edit');
    Route::put('/faq/update/{id}', 'update')->name('faq.update');
    Route::get('/faq/status/{id}', 'status')->name('faq.status');
    Route::delete('/faq/destroy/{id}', 'destroy')->name('faq.destroy');
});
