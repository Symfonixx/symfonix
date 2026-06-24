<?php

use Illuminate\Support\Facades\Route;
use Modules\Support\app\Http\Controllers\SubscribeController;

Route::post('/subscribe', [SubscribeController::class, 'store'])->name('subscribe');
