<?php

use Illuminate\Support\Facades\Route;
use Modules\Reporting\Http\Controllers\Api\ReportDataController;

Route::middleware(['auth:sanctum'])->prefix('reporting')->name('reporting.')->group(function () {
    Route::get('finance', [ReportDataController::class, 'finance'])->name('finance');
    Route::get('sales', [ReportDataController::class, 'sales'])->name('sales');
    Route::get('marketing', [ReportDataController::class, 'marketing'])->name('marketing');
    Route::get('operations', [ReportDataController::class, 'operations'])->name('operations');
    Route::get('employee', [ReportDataController::class, 'employee'])->name('employee');
});
