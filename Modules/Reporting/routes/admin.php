<?php

use Illuminate\Support\Facades\Route;
use Modules\Reporting\Http\Controllers\Admin\EmployeeReportController;
use Modules\Reporting\Http\Controllers\Admin\FinanceReportController;
use Modules\Reporting\Http\Controllers\Admin\MarketingReportController;
use Modules\Reporting\Http\Controllers\Admin\OperationsReportController;
use Modules\Reporting\Http\Controllers\Admin\SalesReportController;

Route::prefix('reporting')->name('reporting.')->group(function () {
    Route::get('finance', [FinanceReportController::class, 'index'])->name('finance');
    Route::get('finance/data', [FinanceReportController::class, 'data'])->name('finance.data');
    Route::get('finance/export', [FinanceReportController::class, 'export'])->name('finance.export');

    Route::get('sales', [SalesReportController::class, 'index'])->name('sales');
    Route::get('sales/data', [SalesReportController::class, 'data'])->name('sales.data');
    Route::get('sales/export', [SalesReportController::class, 'export'])->name('sales.export');

    Route::get('marketing', [MarketingReportController::class, 'index'])->name('marketing');
    Route::get('marketing/data', [MarketingReportController::class, 'data'])->name('marketing.data');
    Route::get('marketing/export', [MarketingReportController::class, 'export'])->name('marketing.export');

    Route::get('operations', [OperationsReportController::class, 'index'])->name('operations');
    Route::get('operations/data', [OperationsReportController::class, 'data'])->name('operations.data');
    Route::get('operations/export', [OperationsReportController::class, 'export'])->name('operations.export');

    Route::get('employee', [EmployeeReportController::class, 'index'])->name('employee');
    Route::get('employee/data', [EmployeeReportController::class, 'data'])->name('employee.data');
    Route::get('employee/export', [EmployeeReportController::class, 'export'])->name('employee.export');
    Route::get('employee/{employee}', [EmployeeReportController::class, 'show'])->whereNumber('employee')->name('employee.show');
});
