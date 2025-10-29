<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Modules\Hr\Http\Controllers\HrController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('hrs', HrController::class)->names('hr');
});


Route::get('hr/departments',  function () {
    return Inertia::render('Hr::Departments/DepartmentsListComponent');
})->name('hr.departments');
