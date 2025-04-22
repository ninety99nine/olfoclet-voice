<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaFileController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('media-files')
    ->controller(MediaFileController::class)
    ->group(function () {
        Route::get('/', 'showMediaFiles')->name('show-media-files');
        Route::post('/', 'createMediaFile')->name('create-media-file');
        Route::delete('/', 'deleteMediaFiles')->name('delete-media-files');

        Route::prefix('{mediaFile}')->group(function () {
            Route::get('/', 'showMediaFile')->name('show-media-file');
            Route::put('/', 'updateMediaFile')->name('update-media-file');
            Route::delete('/', 'deleteMediaFile')->name('delete-media-file');
            Route::get('/presigned-url', 'getPresignedUrl')->name('get-media-file-presigned-url');
        });
    });
