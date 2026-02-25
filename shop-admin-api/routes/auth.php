<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Authentication Routes
|--------------------------------------------------------------------------
|
| All authentication routes return JSON responses for REST API clients.
|
*/

Route::middleware('guest')->group(function () {
    // User Registration
    Route::post('api/auth/register', [RegisteredUserController::class, 'store'])
                ->name('api.register');

    // User Login
    Route::post('api/auth/login', [AuthenticatedSessionController::class, 'store'])
                ->name('api.login');

    // Request Password Reset Link
    Route::post('api/auth/forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('api.password.email');

    // Reset Password with Token
    Route::post('api/auth/reset-password', [NewPasswordController::class, 'store'])
                ->name('api.password.update');
});

Route::middleware('auth:sanctum')->group(function () {
    // Verify Email
    Route::post('api/auth/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('api.verification.send');

    Route::post('api/auth/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('api.verification.verify');

    // Confirm Password
    Route::post('api/auth/confirm-password', [ConfirmablePasswordController::class, 'store'])
                ->name('api.password.confirm');

    // User Logout
    Route::post('api/auth/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('api.logout');
});
