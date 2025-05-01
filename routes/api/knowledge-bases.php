<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\OrganisationPermission;
use App\Http\Controllers\KnowledgeBaseController;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('knowledge-bases')
    ->controller(KnowledgeBaseController::class)
    ->group(function () {
        Route::get('/', 'showKnowledgeBases')->name('show-knowledge-bases');
        Route::post('/', 'createKnowledgeBase')->name('create-knowledge-base');
        Route::delete('/', 'deleteKnowledgeBases')->name('delete-knowledge-bases');

        Route::prefix('{knowledgeBase}')->group(function () {
            Route::get('/', 'showKnowledgeBase')->name('show-knowledge-base');
            Route::put('/', 'updateKnowledgeBase')->name('update-knowledge-base');
            Route::delete('/', 'deleteKnowledgeBase')->name('delete-knowledge-base');
        });
    });
