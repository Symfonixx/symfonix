<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\JobController;
use Modules\CRM\Http\Controllers\Portal\SubscriptionController as PortalSubscriptionController;
use Modules\Finance\Http\Controllers\Portal\InvoiceController as PortalInvoiceController;
use Modules\Project\Http\Controllers\Portal\ProjectController as PortalProjectController;
use Modules\Support\app\Http\Controllers\Portal\TicketController as PortalTicketController;
use Modules\Testimonial\Http\Controllers\Portal\ProjectReviewController as PortalProjectReviewController;
use Modules\User\Http\Controllers\Portal\DashboardController;
use Modules\User\Http\Controllers\Portal\NotificationController;
use Modules\User\Http\Controllers\Portal\ProfileController;

Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{position}', [JobController::class, 'show'])->name('jobs.show');
Route::post('/jobs/{position}/apply', [JobController::class, 'store'])->name('jobs.apply');

Route::middleware(['auth', 'is_customer'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/subscriptions', [PortalSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/{subscription}', [PortalSubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::get('/projects', [PortalProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [PortalProjectController::class, 'show'])->name('projects.show');
    Route::post('/projects/{project}/review', [PortalProjectReviewController::class, 'store'])->name('projects.review');
    Route::get('/tickets', [PortalTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [PortalTicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [PortalTicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [PortalTicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [PortalTicketController::class, 'reply'])->name('tickets.reply');
    Route::post('/tickets/{ticket}/close', [PortalTicketController::class, 'close'])->name('tickets.close');
    Route::get('/invoices/{invoice}/pdf', [PortalInvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});
