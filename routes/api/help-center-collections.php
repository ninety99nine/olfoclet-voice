<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelpCenterCollectionController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('help-center-collections')
    ->controller(HelpCenterCollectionController::class)
    ->group(function () {
        Route::get('/', 'showHelpCenterCollections')->name('show-help-center-collections');
        Route::post('/', 'createHelpCenterCollection')->name('create-help-center-collection');
        Route::delete('/', 'deleteHelpCenterCollections')->name('delete-help-center-collections');

        Route::prefix('{helpCenterCollection}')->group(function () {
            Route::get('/', 'showHelpCenterCollection')->name('show-help-center-collection');
            Route::put('/', 'updateHelpCenterCollection')->name('update-help-center-collection');
            Route::delete('/', 'deleteHelpCenterCollection')->name('delete-help-center-collection');
        });
    });
