<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CopilotController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('copilots')
    ->controller(CopilotController::class)
    ->group(function () {
        Route::get('/', 'showCopilots')->name('show-copilots');
        Route::post('/', 'createCopilot')->name('create-copilot');
        Route::delete('/', 'deleteCopilots')->name('delete-copilots');

        Route::prefix('{copilot}')->group(function () {
            Route::get('/', 'showCopilot')->name('show-copilot');
            Route::put('/', 'updateCopilot')->name('update-copilot');
            Route::delete('/', 'deleteCopilot')->name('delete-copilot');
            Route::post('/query', 'queryCopilot')->name('query-copilot');
        });
    });
