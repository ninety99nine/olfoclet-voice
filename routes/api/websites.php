<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('websites')
    ->controller(WebsiteController::class)
    ->group(function () {
        Route::get('/', 'showWebsites')->name('show-websites');
        Route::post('/', 'createWebsite')->name('create-website');
        Route::delete('/', 'deleteWebsites')->name('delete-websites');

        Route::prefix('{website}')->group(function () {
            Route::get('/', 'showWebsite')->name('show-website');
            Route::put('/', 'updateWebsite')->name('update-website');
            Route::delete('/', 'deleteWebsite')->name('delete-website');
            Route::get('/crawl-status', 'checkCrawlStatus')->name('check-crawl-status');
        });
    });

// Webhook route for Firecrawl updates
Route::post('/firecrawl/webhook/{website}', [WebsiteController::class, 'handleWebhook'])
    ->name('firecrawl-webhook');
