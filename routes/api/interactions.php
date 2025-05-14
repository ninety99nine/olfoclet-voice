<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InteractionController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('interactions')
    ->controller(InteractionController::class)
    ->group(function () {
        Route::get('/', 'showInteractions')->name('show-interactions');
        Route::post('/', 'createInteraction')->name('create-interaction');
        Route::delete('/', 'deleteInteractions')->name('delete-interactions');

        Route::prefix('{interaction}')->group(function () {
            Route::get('/', 'showInteraction')->name('show-interaction');
            Route::put('/', 'updateInteraction')->name('update-interaction');
            Route::delete('/', 'deleteInteraction')->name('delete-interaction');
        });
    });
