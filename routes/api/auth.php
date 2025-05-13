<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/validate-token', [AuthController::class, 'validateToken'])->name('auth.validate-token');
    Route::post('/setup-password', [AuthController::class, 'setupPassword'])->name('auth.setup-password');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('auth.forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('auth.reset-password');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/user', [AuthController::class, 'showAuthUser'])->name('auth.show-user');
        Route::post('/update', [AuthController::class, 'updateAccount'])->name('auth.update-account');
    });
});
