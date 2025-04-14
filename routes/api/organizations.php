<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganizationController;

Route::middleware('auth:sanctum')
    ->prefix('organizations')
    ->controller(OrganizationController::class)
    ->group(function () {

        Route::get('/', 'showOrganizations')->name('show-organizations');
        Route::post('/', 'createOrganization')->name('create-organization');
        Route::delete('/', 'deleteOrganizations')->name('delete-organizations');
        Route::get('alias/{alias}', 'showOrganizationByAlias')->name('show-organization-by-alias');

        // Single organization routes
        Route::prefix('{organization}')->group(function () {
            Route::get('/', 'showOrganization')->name('show-organization');
            Route::put('/', 'updateOrganization')->name('update-organization');
            Route::delete('/', 'deleteOrganization')->name('delete-organization');
        });

    });
