<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
| All routes return JSON responses.
|
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store']);
Route::post('/auth/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store']);

// Products and Categories (public read-only)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// Protected routes (requires authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth endpoints
    Route::post('/auth/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy']);
    Route::post('/auth/verify-email/{id}/{hash}', [\App\Http\Controllers\Auth\VerifyEmailController::class, '__invoke']);
    Route::post('/auth/email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store']);
    Route::post('/auth/confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'store']);

    // User info
    Route::get('/auth/me', function (Request $request) {
        return $request->user();
    });

    // User Profile routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile/image', [ProfileController::class, 'deleteImage']);

    // Admin only routes
    Route::middleware(['is_admin'])->group(function () {
        // Users Management
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::patch('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        // Soft delete for users
        Route::get('/users/trashed', [UserController::class, 'trashed']);
        Route::patch('/users/{id}/restore', [UserController::class, 'restore']);
        Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete']);

        // Products Management
        Route::post('/products', [ProductController::class, 'store']);
        Route::patch('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        // Soft delete for products
        Route::get('/products/trashed', [ProductController::class, 'trashed']);
        Route::patch('/products/{id}/restore', [ProductController::class, 'restore']);
        Route::delete('/products/{id}/force-delete', [ProductController::class, 'forceDelete']);

        // Categories Management
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::patch('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

        // Soft delete for categories
        Route::get('/categories/trashed', [CategoryController::class, 'trashed']);
        Route::patch('/categories/{id}/restore', [CategoryController::class, 'restore']);
        Route::delete('/categories/{id}/force-delete', [CategoryController::class, 'forceDelete']);
    });
});
