<?php

use Illuminate\Support\Facades\Route;
use Modules\Hr\Http\Controllers\{
    HrController,
    EmployeeController
};
use Modules\Hr\Http\Controllers\Organization\{
    DivisionController,
    DepartmentController,
    PositionController,
    SectionController
};

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('hrs', HrController::class)->names('hr');

    Route::group(['prefix' => 'hr', 'as' => 'hr.'], function () {
        Route::prefix('organization/')->as('organization.')->group(function () {
            Route::resource('divisions',    DivisionController::class)->except(['show', 'edit', 'create']);
            Route::resource('departments',  DepartmentController::class)->except(['show', 'edit', 'create']);
            Route::resource('sections',     SectionController::class)->except(['show', 'edit', 'create']);
            Route::resource('positions',    PositionController::class)->except(['show', 'edit', 'create']);
        });

        Route::resource('employees',        EmployeeController::class);
    });
});
