<?php

use Illuminate\Support\Facades\Route;
use Modules\Tax\Http\Controllers\Admin\TaxLedgerController;
use Modules\Tax\Http\Controllers\Admin\TaxRateController;
use Modules\Tax\Http\Controllers\Admin\TaxReportController;

Route::prefix('tax')
    ->name('tax.')
    ->group(function () {
        Route::resource('rates', TaxRateController::class)->except(['show']);
        Route::get('ledger', [TaxLedgerController::class, 'index'])->name('ledger.index');
        Route::get('reports/filing', [TaxReportController::class, 'index'])->name('reports.filing');
        Route::get('reports/filing/export', [TaxReportController::class, 'export'])->name('reports.filing.export');
    });
