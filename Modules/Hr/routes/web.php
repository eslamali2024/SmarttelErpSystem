<?php

use Illuminate\Support\Facades\Route;
use Modules\Hr\Http\Controllers\{
    HrController,
    EmployeeController,
    BonusController,
    DeductionController,
    EmployeeActivityController,
    ContractController
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
    LeaveTypeController,
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

            // Work Schedule
            Route::resource('work-schedules',                   WorkScheduleController::class);
            Route::post('work-schedules-import',                [WorkScheduleController::class, 'import'])->name('work-schedules.import');
            Route::get('work-schedules-download-template',      [WorkScheduleController::class, 'downloadTemplate'])->name('work-schedules.downloadTemplate');

            // Allowance Type
            Route::resource('allowance-types',                  AllowanceTypeController::class)->except(['show', 'edit', 'create']);
            Route::post('allowance-types-import',               [AllowanceTypeController::class, 'import'])->name('allowance-types.import');
            Route::get('allowance-types-download-template',     [AllowanceTypeController::class, 'downloadTemplate'])->name('allowance-types.downloadTemplate');

            Route::resource('insurance-companies',              InsuranceCompanyController::class)->except(['show', 'edit', 'create']);

            // Public Holiday
            Route::resource('public-holidays',                  PublicHolidayController::class)->except(['show', 'edit', 'create']);
            Route::post('public-holidays-import',               [PublicHolidayController::class, 'import'])->name('public-holidays.import');
            Route::get('public-holidays-download-template',     [PublicHolidayController::class, 'downloadTemplate'])->name('public-holidays.downloadTemplate');

            // Bonus Type
            Route::resource('bonus-types',                      BonusTypeController::class)->except(['show', 'edit', 'create']);
            Route::post('bonus-types-import',                   [BonusTypeController::class, 'import'])->name('bonus-types.import');
            Route::get('bonus-types-download-template',         [BonusTypeController::class, 'downloadTemplate'])->name('bonus-types.downloadTemplate');

            // Deduction Type
            Route::resource('deduction-types',                  DeductionTypeController::class)->except(['show', 'edit', 'create']);
            Route::post('deduction-types-import',               [DeductionTypeController::class, 'import'])->name('deduction-types.import');
            Route::get('deduction-types-download-template',     [DeductionTypeController::class, 'downloadTemplate'])->name('deduction-types.downloadTemplate');

            // Leave Type
            Route::resource('leave-types',                      LeaveTypeController::class)->except(['show', 'edit', 'create']);
            Route::post('leave-types-import',                   [LeaveTypeController::class, 'import'])->name('leave-types.import');
            Route::get('leave-types-download-template',         [LeaveTypeController::class, 'downloadTemplate'])->name('leave-types.downloadTemplate');
        });

        Route::resource('bonuses',                              BonusController::class)->except(['edit']);
        Route::resource('deductions',                           DeductionController::class)->except(['edit']);

        // Employee
        Route::resource('employees',                           EmployeeController::class);
        Route::post('employees/{employee}/update-avatar',     [EmployeeController::class, 'updateAvatar'])->name('employees.update-avatar');
        Route::resource('contracts',                           ContractController::class)->except(['index']);
        Route::post('contract/{contract}/change-status/{status}', [ContractController::class, 'changeStatus'])->name('contracts.change-status');
        Route::post('gross-up',                                [EmployeeController::class, 'getGrossUp'])->name('employees.gross-up');

        // Employee Tabs
        Route::group(['prefix' => 'employees/{employee}/activity/', 'as' => 'employees.activity.', 'controller' => EmployeeActivityController::class], function () {
            Route::get('bonuses',           'bonuses')->name('bonuses');
            Route::get('deductions',        'deductions')->name('deductions');
            Route::get('contracts',         'contracts')->name('contracts');
        });
    });
});
