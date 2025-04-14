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

Route::apiResource('images', ImageController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy'])
    ->middleware(['auth', 'verified'])->names('images');

Route::get('images/{image}/preview', [ImageController::class, 'preview'])
    ->middleware(['auth', 'verified'])->name('images.preview');

Route::get('images/{image}/export', [ImageController::class, 'export'])
    ->middleware(['auth', 'verified'])->name('images.export');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
