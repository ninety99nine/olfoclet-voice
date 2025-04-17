<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CallController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('calls')
    ->controller(CallController::class)
    ->group(function () {

        Route::get('/', 'showCalls')->name('show-calls');
        Route::post('/', 'createCall')->name('create-call');
        Route::delete('/', 'deleteCalls')->name('delete-calls');

        Route::prefix('{call}')->group(function () {
            Route::get('/', 'showCall')->name('show-call');
            Route::put('/', 'updateCall')->name('update-call');
            Route::delete('/', 'deleteCall')->name('delete-call');
        });
    });
