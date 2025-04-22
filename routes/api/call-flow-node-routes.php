<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CallFlowNodeController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('call-flow-nodes')
    ->controller(CallFlowNodeController::class)
    ->group(function () {
        Route::get('/', 'showCallFlowNodes')->name('show-call-flow-nodes');
        Route::post('/', 'createCallFlowNode')->name('create-call-flow-node');
        Route::delete('/', 'deleteCallFlowNodes')->name('delete-call-flow-nodes');

        Route::prefix('{callFlowNode}')->group(function () {
            Route::get('/', 'showCallFlowNode')->name('show-call-flow-node');
            Route::put('/', 'updateCallFlowNode')->name('update-call-flow-node');
            Route::delete('/', 'deleteCallFlowNode')->name('delete-call-flow-node');
        });
    });
