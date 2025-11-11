<?php

use App\Http\Controllers\AppDataController;
use App\Http\Controllers\Approval\ApprovalController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// Localization
Route::get('/locale/{locale}', function (string $locale) {
    if (!in_array($locale, ['en', 'ar'])) {
        abort(400);
    }

    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
})->name('locale.change');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('api')->name('api.')->group(function () {
    Route::get('app-data', AppDataController::class)->name('app-data');
});


Route::middleware(['auth'])->group(function () {

    Route::post('approvals/{approval_request}/approve',           [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('approvals/{approval_request}/reject',            [ApprovalController::class, 'reject'])->name('approvals.reject');
    Route::post('approvals/{approval_request}/cancel-approval',   [ApprovalController::class, 'cancelApproval'])->name('approvals.cancel-approval');
});

require __DIR__ . '/settings.php';
