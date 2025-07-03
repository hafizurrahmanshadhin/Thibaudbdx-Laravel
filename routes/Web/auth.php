<?php

use App\Http\Controllers\Web\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Web\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Web\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Web\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Web\Auth\NewPasswordController;
use App\Http\Controllers\Web\Auth\PasswordController;
use App\Http\Controllers\Web\Auth\PasswordResetLinkController;
use App\Http\Controllers\Web\Auth\SocialiteController;
use App\Http\Controllers\Web\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Artisan;
// use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('guest')->group(function () {
    // Route::get('register', [RegisteredUserController::class, 'create'])
    //     ->name('register');

    // Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::controller(SocialiteController::class)->group(function () {
    Route::get('/login/google', 'GoogleRedirect')->name('google-login');
    Route::get('/login/google/callback', 'GoogleCallback');
    Route::get('/google-refresh-token', 'GoogleRefreshToken')->name('google.refresh');
});



//Database and others Command 
Route::get('/run-command', function () {
    return view('components.command_runner');
})->name('run.command.form');

Route::post('/run-command', function (Request $request) {
    // Validate the command input
    $request->validate([
        'command' => 'required|string',
    ]);

    // Get the command from the input
    $command = $request->input('command');

    // Strip "php artisan" from the command if present
    $cleanedCommand = trim(str_replace(['php artisan', 'artisan'], '', $command));

    try {
        // Run the cleaned Artisan command
        Artisan::call($cleanedCommand);
        $output = Artisan::output();
    } catch (\Exception $e) {
        // Handle any errors from the Artisan command
        $output = "Error running command: " . $e->getMessage();
    }

    // Return the output to the view
    return redirect()->route('run.command.form')->with('output', $output);
})->name('run.command');
