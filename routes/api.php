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
use App\Http\Controllers\API\Global\Setting\SettingController;
use App\Http\Controllers\API\Global\Setting\ActivityLogController;
use App\Http\Controllers\Api\SettingController as ApiSettingController;
use App\Http\Controllers\API\Page\PageController;
use App\Http\Controllers\API\Navigation\NavigationController;
use App\Http\Controllers\API\Setting\SiteSettingController;

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

            /*
            |--------------------------------------------------------------------------
            | Protected Admin Routes
            |--------------------------------------------------------------------------
            */
            Route::middleware(['auth:sanctum'])->group(function () {
                /*
                |--------------------------------------------------------------------------
                | Dashboard Routes
                |--------------------------------------------------------------------------
                */
                Route::post('logout', [LoginController::class, 'logout']);

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

                /*
                |--------------------------------------------------------------------------
                | Dynamic Content Management API Resources
                |--------------------------------------------------------------------------
                */

                // Pages API Resource
                Route::apiResource('pages', PageController::class);

                // Navigation API Resource
                Route::apiResource('navigation', NavigationController::class);

                // Site Settings API Resource (only index and update)
                Route::apiResource('settings', SiteSettingController::class)->only(['index', 'update']);

                // Additional custom routes for specific functionality
                Route::get('navigation/location/{location}', [NavigationController::class, 'getByLocation'])->name('navigation.by-location');
                Route::get('settings/group/{group}', [SiteSettingController::class, 'getByGroup'])->name('settings.by-group');
            });
        });
});

    /*
    |--------------------------------------------------------------------------
    | Public Frontend API Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('frontend')
    ->name('frontend.')
    ->group(function () {
        // Public Pages API
        Route::get('pages/slug/{slug}', [PageController::class, 'getBySlug'])
            ->name('pages.by-slug')
            ->where('slug', '.*');
        Route::get('pages/navigation', [PageController::class, 'getNavigation'])->name('pages.navigation');

        // Public Navigation API
        Route::get('navigation/location/{location}', [NavigationController::class, 'getByLocation'])->name('navigation.by-location');

        // Public Site Settings API
        Route::get('settings', [SiteSettingController::class, 'getPublicSettings'])->name('settings.public');
    });