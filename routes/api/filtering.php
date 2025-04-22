<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilterController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('filtering')
    ->controller(FilterController::class)
    ->group(function () {
        Route::get('/', 'showFilterOptions')->name('show-filter-options');
    });
