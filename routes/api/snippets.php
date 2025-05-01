<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SnippetController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('snippets')
    ->controller(SnippetController::class)
    ->group(function () {
        Route::get('/', 'showSnippets')->name('show-snippets');
        Route::post('/', 'createSnippet')->name('create-snippet');
        Route::delete('/', 'deleteSnippets')->name('delete-snippets');

        Route::prefix('{snippet}')->group(function () {
            Route::get('/', 'showSnippet')->name('show-snippet');
            Route::put('/', 'updateSnippet')->name('update-snippet');
            Route::delete('/', 'deleteSnippet')->name('delete-snippet');
        });
    });
