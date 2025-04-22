<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CallActivityController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('call-activities')
    ->controller(CallActivityController::class)
    ->group(function () {
        Route::get('/', 'showCallActivities')->name('show-call-activities');
        Route::prefix('{callActivity}')->group(function () {
            Route::get('/', 'showCallActivity')->name('show-call-activity');
        });
    });
