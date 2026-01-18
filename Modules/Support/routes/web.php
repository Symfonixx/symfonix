<?php

use Illuminate\Support\Facades\Route;
use Modules\Support\app\Http\Controllers\ContactUsController;

Route::get('/contact-us', [ContactUsController::class, 'index'])->name('contact-us');
Route::post('/contact-us', [ContactUsController::class, 'store'])->name('contact-us.store');
Route::post('/subscribe', [ContactUsController::class, 'subscribe'])->name('subscribe');
