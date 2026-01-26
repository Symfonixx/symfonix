<?php

use Illuminate\Support\Facades\Route;
use Modules\Base\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

