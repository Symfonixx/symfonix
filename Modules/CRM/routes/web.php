<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Http\Controllers\ContactUsController;

Route::get('/contact-us', [ContactUsController::class, 'index'])->name('contact-us');
Route::post('/contact-us', [ContactUsController::class, 'store'])->name('contact-us.store');
