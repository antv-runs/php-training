<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // If user is authenticated, send them to the right dashboard.
    if (auth()->check()) {
        $user = auth()->user();
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return redirect('/admin');
        }

        return redirect('/dashboard');
    }

    // Otherwise, redirect unauthenticated visitors to the login page
    return redirect()->route('login');
});

use App\Models\Product;

Route::get('/dashboard', function () {
    // show product list to regular users (read-only)
    $products = Product::latest()->paginate(10);
    return view('dashboard', compact('products'));
})->middleware(['auth'])->name('dashboard');

// Admin routes
use App\Http\Controllers\Admin\AdminController;

Route::prefix('admin')->middleware(['auth','is_admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    // Admin resource routes
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class, ['as' => 'admin']);
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class, ['as' => 'admin']);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class, ['as' => 'admin']);
});

require __DIR__.'/auth.php';
