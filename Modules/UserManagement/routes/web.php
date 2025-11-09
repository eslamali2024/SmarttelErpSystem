<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\{
    ApprovalFlowController,
    ApprovalFlowStepController,
    UserManagementController,
    RoleController,
    PermissionController,
    UserController
};

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('usermanagements', UserManagementController::class)->names('usermanagement');

    Route::prefix('user-management/')->as('user-management.')->group(function () {
        Route::resource('roles',          RoleController::class);
        Route::resource('permissions',    PermissionController::class)->except(['show', 'edit', 'create']);
        Route::resource('users',          UserController::class)->except(['store']);

        Route::resource('approval-flows', ApprovalFlowController::class)->except(['edit', 'show']);
        Route::resource('approval-flows/{approvalFlow}/steps', ApprovalFlowStepController::class)->names('approval-flow-steps')->except(['edit', 'show']);
    });
});
