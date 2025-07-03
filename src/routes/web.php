<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('images/edit-selection', [ImageController::class, 'editSelection'])->name('images.edit-selection');
    Route::put('images/update-selection', [ImageController::class, 'updateSelection'])->name('images.update-selection');
    Route::get('images/export-selection', [ImageController::class, 'exportSelection'])->name('images.export-selection');
    Route::delete('images/destroy-selection', [ImageController::class, 'destroySelection'])->name('images.destroy-selection');
    Route::get('images/{image}/preview', [ImageController::class, 'preview'])->name('images.preview');
    Route::get('images/{image}/export', [ImageController::class, 'export'])->name('images.export');

    Route::apiResource('images', ImageController::class)
        ->only(['index', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('images');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
