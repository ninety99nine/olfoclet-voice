<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('roles')
    ->controller(RoleController::class)
    ->group(function () {
        Route::get('/', 'showRoles')->name('show-roles');
        Route::post('/', 'createRole')->name('create-role');
        Route::delete('/', 'deleteRoles')->name('delete-roles');

        Route::prefix('{role}')->group(function () {
            Route::get('/', 'showRole')->name('show-role');
            Route::put('/', 'updateRole')->name('update-role');
            Route::delete('/', 'deleteRole')->name('delete-role');
        });
    });
