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
use Modules\Hr\Http\Controllers\MasterData\{
    TimeManagementController,
    AllowanceTypeController,
    InsuranceCompanyController,
    WorkScheduleController
};

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('hrs',                          HrController::class)->names('hr');

    Route::group(['prefix' => 'hr', 'as' => 'hr.'], function () {
        Route::prefix('organization/')->as('organization.')->group(function () {
            Route::resource('divisions',            DivisionController::class)->except(['show', 'edit', 'create']);
            Route::resource('departments',          DepartmentController::class)->except(['show', 'edit', 'create']);
            Route::resource('sections',             SectionController::class)->except(['show', 'edit', 'create']);
            Route::resource('positions',            PositionController::class)->except(['show', 'edit', 'create']);
        });

        Route::prefix('master-data/')->as('master-data.')->group(function () {
            Route::resource('time-managements',     TimeManagementController::class)->except(['show', 'edit', 'create']);
            Route::resource('work-schedules',       WorkScheduleController::class);
            Route::resource('allowance-types',      AllowanceTypeController::class)->except(['show', 'edit', 'create']);
            Route::resource('insurance-companies',  InsuranceCompanyController::class)->except(['show', 'edit', 'create']);
        });

        Route::resource('employees',                           EmployeeController::class);
        Route::post('employees/{employee}/update-avatar',     [EmployeeController::class, 'updateAvatar'])->name('employees.update-avatar');
        Route::post('gross-up', [EmployeeController::class, 'getGrossUp'])->name('employees.gross-up');
    });
});
