<?php

use Illuminate\Support\Facades\Route;
use Modules\Support\app\Http\Controllers\Admin\SubscriberController;
use Modules\Support\app\Http\Controllers\Admin\VisitorController;

Route::middleware('can:Support Management')->group(function () {
    // Subscriber routes
    Route::delete('subscribers', [SubscriberController::class, 'deleteMulti'])->name('subscribers.deleteMulti');
    Route::get('subscribers/export', [SubscriberController::class, 'export'])->name('subscribers.export');
    Route::post('subscribers/import', [SubscriberController::class, 'import'])->name('subscribers.import');
    Route::get('subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');

    // Visitors routes
    Route::get('visitors', [VisitorController::class, 'index'])->name('visitors.index');
});
