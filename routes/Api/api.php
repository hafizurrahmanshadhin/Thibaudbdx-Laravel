<?php

use App\Http\Controllers\Api\CMS\FaqController;
use App\Http\Controllers\Api\CMS\ServiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\Customer\CustomerController;
use App\Http\Controllers\Api\Tag\TagController;
use App\Http\Controllers\Api\User\HomepageController;
use App\Models\Sale;

// This route is for getting terms and conditions and privacy policy.
Route::get('contents', [ContentController::class, 'index'])->middleware(['throttle:10,1']);

//home page route
Route::controller(HomepageController::class)->middleware('auth.jwt')->group(function () {
    Route::get('/upcommig-meeting', 'upcomingMeetings');
    Route::get('/upcomming-task', 'upcomingTask');
});

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

//User Nodes(node,voice)|Tasting
require "User/notes-tasting.php";
require "User/Tasting.php";

//--Prospect customer route link
require "v1/product/Product.php";
require "v1/Meeting/Meeting.php";
require "v1/Tasting/tasting.php";
require "v1/Voice/voice.php";
require "v1/Task/Task.php";
require "v1/activity/activity.php";
require "v1/document/document.php";
require "v1/sales/sales.php";


//--Client All Route Link
require "v2/customer/customer.php";
require "v2/Meeting/Meeting.php";
require "v2/Task/Task.php";
require "v2/Voice/voice.php";
require "v2/Tasting/Tasting.php";
require "v2/activity/activity.php";
require "v2/document/document.php";
require "v2/sales/sales.php";
//csv file upload
require "CSV/CustomerCSVImport.php";

Route::middleware('auth.jwt')->group(function () {
    Route::get('/user-list-faq', [FaqController::class, 'FaqList']);
    Route::get('/user-list-support', [ServiceController::class, 'ServiceList']);
});
