<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1
Route::prefix('v1')->group(function () {

    // Test route (public, no authentication required)
    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'Hello World',
            'timestamp' => now()->toIso8601String(),
            'version' => 'v1',
        ]);
    })->name('api.test');

    // Public routes (no authentication required) - Rate limited
    Route::middleware('throttle:60,1')->group(function () {
        Route::post('/auth/login', [App\Http\Controllers\Api\AuthController::class, 'login'])->name('api.auth.login');
        Route::post('/auth/register', [App\Http\Controllers\Api\AuthController::class, 'register'])->name('api.auth.register');

        // Colors - Public route (returns colors from JSON file)
        Route::get('/colors', [App\Http\Controllers\Api\ColorController::class, 'index'])->name('api.colors.index');
        Route::get('/app-info', [App\Http\Controllers\Api\SettingsController::class, 'appInfo'])->name('api.settings.app-info');
        Route::get('/company', [App\Http\Controllers\Api\SettingsController::class, 'company'])->name('api.settings.company');
        Route::get('/brand-assets', [App\Http\Controllers\Api\SettingsController::class, 'brandAssets'])->name('api.settings.brand-assets');
        Route::get('/i18n/{domain}/languages', [App\Http\Controllers\Api\ClientTranslationController::class, 'languages'])
            ->where('domain', '[a-z0-9_-]{1,64}')
            ->name('api.i18n.languages');
        Route::get('/i18n/{domain}/{locale}', [App\Http\Controllers\Api\ClientTranslationController::class, 'bundle'])
            ->where('domain', '[a-z0-9_-]{1,64}')
            ->where('locale', '[a-z]{2}')
            ->name('api.i18n.bundle');
    });

    // Protected routes (authentication required) - Rate limited
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

        // Authentication
        Route::post('/auth/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->name('api.auth.logout');
        Route::get('/auth/me', [App\Http\Controllers\Api\AuthController::class, 'me'])->name('api.auth.me');

        // Users
        Route::get('/users', [App\Http\Controllers\Api\UserController::class, 'index'])->name('api.users.index');
        Route::get('/users/{id}', [App\Http\Controllers\Api\UserController::class, 'show'])->name('api.users.show');
        Route::put('/users/{id}', [App\Http\Controllers\Api\UserController::class, 'update'])->name('api.users.update');

        // Colors (protected routes - require authentication)
        Route::get('/colors/sync', [App\Http\Controllers\Api\ColorController::class, 'sync'])->name('api.colors.sync');
        Route::get('/colors/{id}', [App\Http\Controllers\Api\ColorController::class, 'show'])->name('api.colors.show');
        Route::put('/colors/{id}', [App\Http\Controllers\Api\ColorController::class, 'update'])->name('api.colors.update');

        // Brand
        Route::get('/brand', [App\Http\Controllers\Api\BrandController::class, 'index'])->name('api.brand.index');
        Route::post('/brand/logo', [App\Http\Controllers\Api\BrandController::class, 'uploadLogo'])->name('api.brand.upload-logo');
        Route::post('/brand/favicon', [App\Http\Controllers\Api\BrandController::class, 'uploadFavicon'])->name('api.brand.upload-favicon');

        // Settings
        Route::get('/settings', [App\Http\Controllers\Api\SettingsController::class, 'index'])->name('api.settings.index');
        Route::get('/settings/languages', [App\Http\Controllers\Api\SettingsController::class, 'languages'])->name('api.settings.languages');
        Route::get('/settings/colors', [App\Http\Controllers\Api\SettingsController::class, 'colors'])->name('api.settings.colors');

        // Appointments
        Route::get('/me/appointments', [App\Http\Controllers\Api\AppointmentController::class, 'index'])->name('api.appointments.me');
        Route::post('/appointments', [App\Http\Controllers\Api\AppointmentController::class, 'store'])->name('api.appointments.store');
        Route::patch('/appointments/{appointment}', [App\Http\Controllers\Api\AppointmentController::class, 'update'])->name('api.appointments.update');
    });
});
