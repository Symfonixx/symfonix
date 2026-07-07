<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Http\Controllers\Portal\SubscriptionController as PortalSubscriptionController;
use Modules\Finance\Http\Controllers\Portal\InvoiceController as PortalInvoiceController;
use Modules\Project\Http\Controllers\Portal\ProjectController as PortalProjectController;
use Modules\User\Http\Controllers\Portal\DashboardController;
use Modules\User\Http\Controllers\Portal\NotificationController;

Route::middleware(['auth', 'is_customer'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/subscriptions', [PortalSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/projects', [PortalProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [PortalProjectController::class, 'show'])->name('projects.show');
    Route::get('/invoices/{invoice}/pdf', [PortalInvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});
