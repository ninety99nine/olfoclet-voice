<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QueueController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('queues')
    ->controller(QueueController::class)
    ->group(function () {
        Route::get('/', 'showQueues')->name('show-queues');
        Route::post('/', 'createQueue')->name('create-queue');
        Route::delete('/', 'deleteQueues')->name('delete-queues');

        Route::prefix('{queue}')->group(function () {
            Route::get('/', 'showQueue')->name('show-queue');
            Route::put('/', 'updateQueue')->name('update-queue');
            Route::delete('/', 'deleteQueue')->name('delete-queue');
        });
    });
