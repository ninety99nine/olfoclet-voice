<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAttributeController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('custom-attributes')
    ->controller(CustomAttributeController::class)
    ->group(function () {
        Route::get('/', 'showCustomAttributes')->name('show-custom-attributes');
        Route::post('/', 'createCustomAttribute')->name('create-custom-attribute');
        Route::delete('/', 'deleteCustomAttributes')->name('delete-custom-attributes');

        Route::prefix('{customAttribute}')->group(function () {
            Route::get('/', 'showCustomAttribute')->name('show-custom-attribute');
            Route::put('/', 'updateCustomAttribute')->name('update-custom-attribute');
            Route::delete('/', 'deleteCustomAttribute')->name('delete-custom-attribute');
        });
    });
