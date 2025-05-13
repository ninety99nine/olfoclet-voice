<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContentItemController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('content-items')
    ->controller(ContentItemController::class)
    ->group(function () {
        Route::get('/', 'showContentItems')->name('show-content-items');
        Route::post('/', 'createContentItem')->name('create-content-item');
        Route::delete('/', 'deleteContentItems')->name('delete-content-items');

        Route::prefix('{contentItem}')->group(function () {
            Route::get('/', 'showContentItem')->name('show-content-item');
            Route::put('/', 'updateContentItem')->name('update-content-item');
            Route::delete('/', 'deleteContentItem')->name('delete-content-item');
        });
    });
