<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganizationController;

Route::middleware('auth:sanctum')->prefix('organizations')->group(function () {
    Route::get('/', [OrganizationController::class, 'index']);
    Route::post('/', [OrganizationController::class, 'create']);
    Route::get('{organization}', [OrganizationController::class, 'show']);
    Route::put('{organization}', [OrganizationController::class, 'update']);
    Route::delete('{organization}', [OrganizationController::class, 'destroy']);
});
