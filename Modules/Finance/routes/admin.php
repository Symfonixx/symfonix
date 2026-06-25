<?php

use Illuminate\Support\Facades\Route;
use Modules\Finance\Http\Controllers\Admin\CommissionController;
use Modules\Finance\Http\Controllers\Admin\DailyLogController;
use Modules\Finance\Http\Controllers\Admin\DashboardController;
use Modules\Finance\Http\Controllers\Admin\ExpenseCategoryController;
use Modules\Finance\Http\Controllers\Admin\ProductSaleController;
use Modules\Finance\Http\Controllers\Admin\SalaryController;

Route::middleware(['can:Finance Management'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('daily-log', [DailyLogController::class, 'index'])->name('daily-log');

    Route::resource('expense-categories', ExpenseCategoryController::class)->except(['show']);

    Route::get('salaries', [SalaryController::class, 'index'])->name('salaries.index');
    Route::post('salaries', [SalaryController::class, 'store'])->name('salaries.store');
    Route::post('salaries/{salary}/payout', [SalaryController::class, 'recordPayout'])->name('salaries.payout');

    Route::get('commissions', [CommissionController::class, 'index'])->name('commissions.index');
    Route::post('commissions/{commission}/payout', [CommissionController::class, 'recordPayout'])->name('commissions.payout');

    Route::get('product-sales', [ProductSaleController::class, 'index'])->name('product-sales.index');
    Route::post('product-sales', [ProductSaleController::class, 'store'])->name('product-sales.store');
    Route::delete('product-sales/{product_sale}', [ProductSaleController::class, 'destroy'])->name('product-sales.destroy');
});
