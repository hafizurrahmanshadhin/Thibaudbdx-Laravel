<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\SocialiteController;
use App\Http\Controllers\imageTestController;
use Illuminate\Support\Facades\Route;

//# Auth Routes Start

Route::prefix('auth')->middleware(['throttle:10,1'])->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/verify-email', [RegisterController::class, 'VerifyEmail']);
    Route::post('/resend-otp', [RegisterController::class, 'ResendOtp']);
    Route::post('/login', [LoginController::class, 'login']);


    Route::post('/logout', [LogoutController::class, 'logout'])->middleware(['auth.jwt']);

    Route::controller(PasswordResetController::class)
        ->group(function () {
            Route::post('/forget-password', 'sendOtpToEmail');
            Route::post('/verify-otp', 'verifyOTP');
            Route::post('/reset-password', 'resetPassword');
        });

    Route::post('/socialite-login', [SocialiteController::class, 'socialiteLogin']);
});
//~ Auth Routes End
