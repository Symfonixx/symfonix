<?php

use Illuminate\Support\Facades\Route;
use Modules\Finance\Http\Controllers\Admin\AccountsReceivableController;
use Modules\Finance\Http\Controllers\Admin\CommissionController;
use Modules\Finance\Http\Controllers\Admin\DailyLogController;
use Modules\Finance\Http\Controllers\Admin\DashboardController;
use Modules\Finance\Http\Controllers\Admin\DisplayCurrencyController;
use Modules\Finance\Http\Controllers\Admin\ExpenseCategoryController;
use Modules\Finance\Http\Controllers\Admin\InvoiceController;
use Modules\Finance\Http\Controllers\Admin\ProductSaleController;
use Modules\Finance\Http\Controllers\Admin\SalaryController;

Route::prefix('finance')->name('finance.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('daily-log', [DailyLogController::class, 'index'])->name('daily-log');
    Route::get('accounts-receivable', [AccountsReceivableController::class, 'index'])->name('accounts-receivable');

    Route::resource('expense-categories', ExpenseCategoryController::class)->except(['show']);

    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('invoices/{invoice}/sent', [InvoiceController::class, 'markSent'])->name('invoices.sent');
    Route::post('invoices/{invoice}/paid', [InvoiceController::class, 'markPaid'])->name('invoices.paid');
    Route::post('invoices/{invoice}/void', [InvoiceController::class, 'void'])->name('invoices.void');
    Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::post('invoices/from-deal/{deal}', [InvoiceController::class, 'createFromDeal'])->name('invoices.from-deal');

    Route::get('salaries', [SalaryController::class, 'index'])->name('salaries.index');
    Route::post('salaries', [SalaryController::class, 'store'])->name('salaries.store');
    Route::post('salaries/{salary}/payout', [SalaryController::class, 'recordPayout'])->name('salaries.payout');
    Route::delete('salaries/{salary}', [SalaryController::class, 'destroy'])->name('salaries.destroy');

    Route::get('commissions', [CommissionController::class, 'index'])->name('commissions.index');
    Route::post('commissions/{commission}/payout', [CommissionController::class, 'recordPayout'])->name('commissions.payout');

    Route::get('product-sales', [ProductSaleController::class, 'index'])->name('product-sales.index');
    Route::post('product-sales', [ProductSaleController::class, 'store'])->name('product-sales.store');
    Route::delete('product-sales/{product_sale}', [ProductSaleController::class, 'destroy'])->name('product-sales.destroy');
});

Route::post('display-currency', [DisplayCurrencyController::class, 'update'])
    ->name('display-currency.update');
