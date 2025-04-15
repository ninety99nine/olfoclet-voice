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

// Automatically require route files in routes/api/*.php
foreach (glob(__DIR__ . '/api/*.php') as $routeFile) {
    require $routeFile;
}

Route::post('/voice/ivr', function () {
    $response = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $response .= '<Response>';
    $response .= '<Say>Welcome to Telcoflo, how may I help you?</Say>';
    $response .= '</Response>';

    return response($response, 200)->header('Content-Type', 'application/xml');
});
