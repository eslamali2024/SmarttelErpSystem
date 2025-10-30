<?php

use Illuminate\Support\Facades\Route;
use Modules\Hr\Http\Controllers\HrController;
use Modules\Hr\Http\Controllers\Organization\{
    DivisionController,
    DepartmentController,
    PositionController,
    SectionController
};

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('hrs', HrController::class)->names('hr');

    Route::prefix('hr/organization/')->as('hr.organization.')->group(function () {
        Route::resource('divisions',    DivisionController::class)->except(['show', 'edit', 'create']);
        Route::resource('departments',  DepartmentController::class)->except(['show', 'edit', 'create']);
        Route::resource('sections',     SectionController::class)->except(['show', 'edit', 'create']);
        Route::resource('positions',    PositionController::class)->except(['show', 'edit', 'create']);
    });
});
