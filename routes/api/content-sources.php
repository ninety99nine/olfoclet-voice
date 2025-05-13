<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContentSourceController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('content-sources')
    ->controller(ContentSourceController::class)
    ->group(function () {
        Route::get('/', 'showContentSources')->name('show-content-sources');
        Route::post('/', 'createContentSource')->name('create-content-source');
        Route::delete('/', 'deleteContentSources')->name('delete-content-sources');

        Route::prefix('{contentSource}')->group(function () {
            Route::get('/', 'showContentSource')->name('show-content-source');
            Route::put('/', 'updateContentSource')->name('update-content-source');
            Route::delete('/', 'deleteContentSource')->name('delete-content-source');
        });
    });
