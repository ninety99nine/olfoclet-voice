<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\OrganisationPermission;
use App\Http\Controllers\ConversationMessageController;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('conversation-messages')
    ->controller(ConversationMessageController::class)
    ->group(function () {
        Route::get('/', 'showConversationMessages')->name('show-conversation-messages');
        Route::post('/', 'createConversationMessage')->name('create-conversation-message');
        Route::delete('/', 'deleteConversationMessages')->name('delete-conversation-messages');

        Route::prefix('{conversationMessage}')->group(function () {
            Route::get('/', 'showConversationMessage')->name('show-conversation-message');
            Route::put('/', 'updateConversationMessage')->name('update-conversation-message');
            Route::delete('/', 'deleteConversationMessage')->name('delete-conversation-message');
        });
    });
