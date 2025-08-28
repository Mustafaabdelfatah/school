<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\AuthController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use App\Http\Controllers\Api\V1\Admin\UserController;

Route::prefix('v1')->group(function () {
    // Admin Auth Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Public routes
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

        // Protected routes
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/me', [AuthController::class, 'me'])->name('me');
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // User management
            Route::apiResource('users', UserController::class);
        });
    });
});

// Fallback route for undefined API endpoints
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
    ], 404);
});
