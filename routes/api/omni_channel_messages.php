<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OmniChannelMessageController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('omni-channel-messages')
    ->controller(OmniChannelMessageController::class)
    ->group(function () {
        Route::get('/', 'showOmniChannelMessages')->name('show-omni-channel-messages');
        Route::post('/', 'createOmniChannelMessage')->name('create-omni-channel-message');
        Route::delete('/', 'deleteOmniChannelMessages')->name('delete-omni-channel-messages');

        Route::prefix('{omniChannelMessage}')->group(function () {
            Route::get('/', 'showOmniChannelMessage')->name('show-omni-channel-message');
            Route::put('/', 'updateOmniChannelMessage')->name('update-omni-channel-message');
            Route::delete('/', 'deleteOmniChannelMessage')->name('delete-omni-channel-message');
        });
    });
