<?php

use App\Http\Controllers\AppDataController;
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

require __DIR__ . '/settings.php';
