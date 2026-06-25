<?php

use Illuminate\Support\Facades\Route;
use Modules\Project\Http\Controllers\ProjectUseCaseController;

Route::get('/portfolio', [ProjectUseCaseController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/{slug}', [ProjectUseCaseController::class, 'show'])->name('portfolio.show');
