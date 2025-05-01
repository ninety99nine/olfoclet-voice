<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\OrganisationPermission;
use App\Http\Controllers\ConversationThreadController;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('conversation-threads')
    ->controller(ConversationThreadController::class)
    ->group(function () {
        Route::get('/', 'showConversationThreads')->name('show-conversation-threads');
        Route::post('/', 'createConversationThread')->name('create-conversation-thread');
        Route::delete('/', 'deleteConversationThreads')->name('delete-conversation-threads');

        Route::prefix('{conversationThread}')->group(function () {
            Route::get('/', 'showConversationThread')->name('show-conversation-thread');
            Route::put('/', 'updateConversationThread')->name('update-conversation-thread');
            Route::delete('/', 'deleteConversationThread')->name('delete-conversation-thread');
        });
    });
