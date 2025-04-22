<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('users')
    ->controller(UserController::class)
    ->group(function () {
        Route::get('/', 'showUsers')->name('show-users');
        Route::post('/', 'createUser')->name('create-user');
        Route::delete('/', 'deleteUsers')->name('delete-users');

        Route::prefix('{user}')->group(function () {
            Route::get('/', 'showUser')->name('show-user');
            Route::put('/', 'updateUser')->name('update-user');
            Route::delete('/', 'deleteUser')->name('delete-user');
        });
    });
