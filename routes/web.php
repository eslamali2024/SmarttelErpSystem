<?php

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

require __DIR__ . '/settings.php';
