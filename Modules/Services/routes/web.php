<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Modules\Services\Http\Controllers\ServiceController;

// Service routes
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/service/{slug}', [ServiceController::class, 'show'])->name('services.show');
