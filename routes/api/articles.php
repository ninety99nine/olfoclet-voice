<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('articles')
    ->controller(ArticleController::class)
    ->group(function () {
        Route::get('/', 'showArticles')->name('show-articles');
        Route::post('/', 'createArticle')->name('create-article');
        Route::delete('/', 'deleteArticles')->name('delete-articles');

        Route::prefix('{article}')->group(function () {
            Route::get('/', 'showArticle')->name('show-article');
            Route::put('/', 'updateArticle')->name('update-article');
            Route::delete('/', 'deleteArticle')->name('delete-article');
        });
    });
