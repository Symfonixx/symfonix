<?php

use Illuminate\Support\Facades\Route;
use Modules\Project\Http\Controllers\ProjectUseCaseController;

Route::get('/use-cases', [ProjectUseCaseController::class, 'index'])->name('use-cases.index');
Route::get('/use-cases/{slug}', [ProjectUseCaseController::class, 'show'])->name('use-cases.show');

Route::redirect('/portfolio', '/use-cases', 301);
Route::get('/portfolio/{slug}', fn (string $slug) => redirect("/use-cases/{$slug}", 301));
