<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\SortingController;
use App\Http\Controllers\Auth\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'showAuthUser']);
});

Route::get('/filters', [FilterController::class, 'showFilters']);
Route::get('/sorting', [SortingController::class, 'showSorting']);

foreach (glob(__DIR__ . '/api/*.php') as $routeFile) {
    require $routeFile;
}

