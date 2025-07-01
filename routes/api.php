<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

Route::prefix('v1')->group(function () {
    // Public authentication routes
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');

    // Protected routes (require Sanctum token authentication)
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
        Route::get('/me', [AuthController::class, 'me'])->name('api.me');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('api.refresh');
    });
});
