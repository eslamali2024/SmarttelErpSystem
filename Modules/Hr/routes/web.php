<?php

use Illuminate\Support\Facades\Route;
use Modules\Hr\Http\Controllers\{
    HrController,
    EmployeeController,
    BonusController,
    DeductionController
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
    BonusTypeController,
    DeductionTypeController,
    InsuranceCompanyController,
    PublicHolidayController,
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

            // Public Holiday
            Route::resource('public-holidays',      PublicHolidayController::class)->except(['show', 'edit', 'create']);
            Route::post('public-holidays-import',   [PublicHolidayController::class, 'import'])->name('public-holidays.import');
            Route::get('public-holidays-download-template',   [PublicHolidayController::class, 'downloadTemplate'])->name('public-holidays.downloadTemplate');

            // Bonus Type
            Route::resource('bonus-types',          BonusTypeController::class)->except(['show', 'edit', 'create']);
            Route::post('bonus-types-import',       [BonusTypeController::class, 'import'])->name('bonus-types.import');
            Route::get('bonus-types-download-template',   [BonusTypeController::class, 'downloadTemplate'])->name('bonus-types.downloadTemplate');

            // Deduction Type
            Route::resource('deduction-types',          DeductionTypeController::class)->except(['show', 'edit', 'create']);
            Route::post('deduction-types-import',       [DeductionTypeController::class, 'import'])->name('deduction-types.import');
            Route::get('deduction-types-download-template',   [DeductionTypeController::class, 'downloadTemplate'])->name('deduction-types.downloadTemplate');
        });

        Route::resource('bonuses',                      BonusController::class)->except(['edit']);
        Route::resource('deductions',                   DeductionController::class)->except(['edit']);

        // Employee
        Route::resource('employees',                           EmployeeController::class);
        Route::post('employees/{employee}/update-avatar',     [EmployeeController::class, 'updateAvatar'])->name('employees.update-avatar');
        Route::post('gross-up', [EmployeeController::class, 'getGrossUp'])->name('employees.gross-up');
    });
});
