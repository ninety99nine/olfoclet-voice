<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Middleware\OrganisationPermission;

Route::middleware(['auth:sanctum', OrganisationPermission::class])
    ->prefix('departments')
    ->controller(DepartmentController::class)
    ->group(function () {

        Route::get('/', 'showDepartments')->name('show-departments');
        Route::post('/', 'createDepartment')->name('create-department');
        Route::delete('/', 'deleteDepartments')->name('delete-departments');

        Route::prefix('{department}')->group(function () {
            Route::get('/', 'showDepartment')->name('show-department');
            Route::put('/', 'updateDepartment')->name('update-department');
            Route::delete('/', 'deleteDepartment')->name('delete-department');
        });
    });
