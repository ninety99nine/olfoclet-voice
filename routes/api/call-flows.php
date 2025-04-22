<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CallFlowController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('call-flows')
    ->controller(CallFlowController::class)
    ->group(function () {
        Route::get('/', 'showCallFlows')->name('show-call-flows');
        Route::post('/', 'createCallFlow')->name('create-call-flow');
        Route::delete('/', 'deleteCallFlows')->name('delete-call-flows');

        Route::prefix('{callFlow}')->group(function () {
            Route::get('/', 'showCallFlow')->name('show-call-flow');
            Route::put('/', 'updateCallFlow')->name('update-call-flow');
            Route::delete('/', 'deleteCallFlow')->name('delete-call-flow');
        });
    });
