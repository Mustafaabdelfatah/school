<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\User\RoleController;
use App\Http\Controllers\API\User\UserController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\User\PermissionController;
use App\Http\Controllers\API\Auth\ResetPasswordController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use App\Http\Controllers\API\Auth\ForgetPasswordController;
use App\Http\Controllers\API\Global\Setting\ActivityLogController;

Route::prefix('v1')->group(function () {
    // Admin Auth Routes
    Route::prefix('admin')
        ->name('admin.')
        ->group(function () {
            /*
            |--------------------------------------------------------------------------
            | Auth Routes
            |--------------------------------------------------------------------------
            */
            Route::post('login', [LoginController::class, 'login']);
            Route::post('forget', [ForgetPasswordController::class, 'forget'])->name('forget');
            Route::post('verify-otp', [ForgetPasswordController::class, 'verify'])->name('verify');
            Route::post('reset', [ResetPasswordController::class, 'reset'])->name('reset');

            Route::middleware(['auth:sanctum'])->group(function () {
                /*
                |--------------------------------------------------------------------------
                | activity log Routes
                |--------------------------------------------------------------------------
                */
                Route::get('get-activity-logs', [ActivityLogController::class, 'index']);
                Route::get('get-activity-logs/{activity}', [ActivityLogController::class, 'show']);

                /*
                |--------------------------------------------------------------------------
                | User Routes
                |--------------------------------------------------------------------------
                */
                Route::apiResource('users', UserController::class);

                //Role Routes
                Route::apiResource('roles', RoleController::class);

                Route::delete('permissions/delete-all', [PermissionController::class, 'destroyAll']);
                Route::apiResource('permissions', PermissionController::class);
            });
        });
});
