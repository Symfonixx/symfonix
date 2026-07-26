<?php

use Illuminate\Support\Facades\Route;
use Modules\Project\Http\Controllers\ProjectUseCaseController;

Route::get('/use-cases', [ProjectUseCaseController::class, 'index'])->name('use-cases.index');
Route::get('/use-cases/{slug}', [ProjectUseCaseController::class, 'show'])->name('use-cases.show');

// Named redirects keep the locale prefix (avoid /portfolio → /use-cases → /en/use-cases hop).
Route::get('/portfolio', fn () => redirect()->route('use-cases.index', [], 301));
Route::get('/portfolio/{slug}', fn (string $slug) => redirect()->route('use-cases.show', ['slug' => $slug], 301));
