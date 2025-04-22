<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('tags')
    ->controller(TagController::class)
    ->group(function () {
        Route::get('/', 'showTags')->name('show-tags');
        Route::post('/', 'createTag')->name('create-tag');
        Route::delete('/', 'deleteTags')->name('delete-tags');

        Route::prefix('{tag}')->group(function () {
            Route::get('/', 'showTag')->name('show-tag');
            Route::put('/', 'updateTag')->name('update-tag');
            Route::delete('/', 'deleteTag')->name('delete-tag');
        });
    });
