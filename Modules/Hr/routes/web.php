<?php

use Illuminate\Support\Facades\Route;
use Modules\Hr\Http\Controllers\HrController;
use Modules\Hr\Http\Controllers\Organization\PositionController;
use Modules\Hr\Http\Controllers\Organization\DepartmentController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('hrs', HrController::class)->names('hr');

    Route::prefix('hr/organization/')->as('hr.organization.')->group(function () {
        Route::resource('departments', DepartmentController::class);
        Route::resource('positions',   PositionController::class);
    });
});
