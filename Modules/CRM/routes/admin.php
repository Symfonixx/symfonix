<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Http\Controllers\Admin\ActivityController;
use Modules\CRM\Http\Controllers\Admin\CompanyController;
use Modules\CRM\Http\Controllers\Admin\ContactFormController;
use Modules\CRM\Http\Controllers\Admin\CrmDashboardController;
use Modules\CRM\Http\Controllers\Admin\DealController;
use Modules\CRM\Http\Controllers\Admin\LeadController;
use Modules\CRM\Http\Controllers\Admin\SalesTargetController;
use Modules\CRM\Http\Controllers\Admin\SubscriptionController;

Route::middleware(['can:CRM Management'])->group(function () {
    Route::get('crm/dashboard', [CrmDashboardController::class, 'index'])->name('crm.dashboard');
    Route::get('crm/sales-targets', [SalesTargetController::class, 'index'])->name('crm.sales-targets.index');
    Route::put('crm/sales-targets', [SalesTargetController::class, 'update'])->name('crm.sales-targets.update');

    Route::delete('companies/deleteMulti', [CompanyController::class, 'deleteMulti'])->name('companies.deleteMulti');
    Route::resource('companies', CompanyController::class);

    Route::delete('deals/deleteMulti', [DealController::class, 'deleteMulti'])->name('deals.deleteMulti');
    Route::patch('deals/{deal}/stage', [DealController::class, 'moveStage'])->name('deals.moveStage');
    Route::resource('deals', DealController::class);

    Route::delete('subscriptions/deleteMulti', [SubscriptionController::class, 'deleteMulti'])->name('subscriptions.deleteMulti');
    Route::resource('subscriptions', SubscriptionController::class);

    Route::post('activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::delete('activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

    Route::delete('contact_forms', [ContactFormController::class, 'deleteMulti'])->name('contact_forms.deleteMulti');
    Route::get('contact_forms/export', [ContactFormController::class, 'export'])->name('contact_forms.export');
    Route::get('contact_forms/create', [ContactFormController::class, 'create'])->name('contact_forms.create');
    Route::post('contact_forms', [ContactFormController::class, 'store'])->name('contact_forms.store');
    Route::get('contact_forms/{contact_form}/edit', [ContactFormController::class, 'edit'])->name('contact_forms.edit');
    Route::patch('contact_forms/{contact_form}', [ContactFormController::class, 'update'])->name('contact_forms.update');
    Route::get('contact_forms', [ContactFormController::class, 'index'])->name('contact_forms.index');

    Route::delete('leads', [LeadController::class, 'deleteMulti'])->name('leads.deleteMulti');
    Route::get('leads/create', [LeadController::class, 'create'])->name('leads.create');
    Route::post('leads', [LeadController::class, 'store'])->name('leads.store');
    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
    Route::put('leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::get('leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
    Route::post('leads/{lead}/block', [LeadController::class, 'block'])->name('leads.block');
    Route::post('leads/{lead}/unblock', [LeadController::class, 'unblock'])->name('leads.unblock');
    Route::post('leads/{lead}/convert', [LeadController::class, 'convertToDeal'])->name('leads.convert');
});
