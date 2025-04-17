<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CallActivityController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('call-activities')
    ->controller(CallActivityController::class)
    ->group(function () {

        Route::get('/', 'showCallActivities')->name('show-call-activities');
        Route::post('/', 'createCallActivity')->name('create-call-activity');
        Route::delete('/', 'deleteCallActivities')->name('delete-call-activities');

        Route::prefix('{callActivity}')->group(function () {
            Route::get('/', 'showCallActivity')->name('show-call-activity');
            Route::put('/', 'updateCallActivity')->name('update-call-activity');
            Route::delete('/', 'deleteCallActivity')->name('delete-call-activity');
        });
    });
