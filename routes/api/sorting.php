<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SortingController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('sorting')
    ->controller(SortingController::class)
    ->group(function () {
        Route::get('/', 'showSortOptions')->name('show-sort-options');
    });
