<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('contacts')
    ->controller(ContactController::class)
    ->group(function () {
        Route::get('/', 'showContacts')->name('show-contacts');
        Route::post('/', 'createContact')->name('create-contact');
        Route::delete('/', 'deleteContacts')->name('delete-contacts');
        Route::post('/import', 'importContacts')->name('import-contacts');

        Route::prefix('{contact}')->group(function () {
            Route::get('/', 'showContact')->name('show-contact');
            Route::put('/', 'updateContact')->name('update-contact');
            Route::delete('/', 'deleteContact')->name('delete-contact');
        });
    });
