<?php

declare(strict_types=1);

use App\Http\Controllers\API\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication API Routes with Cookie Support
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public authentication routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected authentication routes with cookie auth
    Route::middleware(['cookie.auth', 'auth:sanctum', 'active'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
    });

    // Admin-only routes
    Route::middleware(['cookie.auth', 'auth:sanctum', 'admin'])->group(function () {
        Route::put('/users/{user}/role', [AuthController::class, 'changeRole']);
    });

    // Moderator routes
    Route::middleware(['cookie.auth', 'auth:sanctum', 'moderator'])->group(function () {
    });

});
