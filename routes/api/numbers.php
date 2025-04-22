<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NumberController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('numbers')
    ->controller(NumberController::class)
    ->group(function () {
        Route::get('/', 'showNumbers')->name('show-numbers');
        Route::post('/', 'createNumber')->name('create-number');
        Route::delete('/', 'deleteNumbers')->name('delete-numbers');

        Route::prefix('{number}')->group(function () {
            Route::get('/', 'showNumber')->name('show-number');
            Route::put('/', 'updateNumber')->name('update-number');
            Route::delete('/', 'deleteNumber')->name('delete-number');
        });
    });
