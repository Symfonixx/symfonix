<?php

use Illuminate\Support\Facades\Route;
use Modules\Support\app\Http\Controllers\Admin\SubscriberController;
use Modules\Support\app\Http\Controllers\Admin\TicketCategoryController;
use Modules\Support\app\Http\Controllers\Admin\TicketController;
use Modules\Support\app\Http\Controllers\Admin\VisitorController;

Route::group([], function () {
    // Ticket routes
    Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::put('tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');

    // Ticket category routes
    Route::get('ticket-categories', [TicketCategoryController::class, 'index'])->name('ticket_categories.index');
    Route::get('ticket-categories/create', [TicketCategoryController::class, 'create'])->name('ticket_categories.create');
    Route::post('ticket-categories', [TicketCategoryController::class, 'store'])->name('ticket_categories.store');
    Route::get('ticket-categories/{ticketCategory}/edit', [TicketCategoryController::class, 'edit'])->name('ticket_categories.edit');
    Route::put('ticket-categories/{ticketCategory}', [TicketCategoryController::class, 'update'])->name('ticket_categories.update');

    // Subscriber routes
    Route::delete('subscribers', [SubscriberController::class, 'deleteMulti'])->name('subscribers.deleteMulti');
    Route::get('subscribers/export', [SubscriberController::class, 'export'])->name('subscribers.export');
    Route::get('subscribers/import-sample', [SubscriberController::class, 'downloadSample'])->name('subscribers.importSample');
    Route::post('subscribers/import', [SubscriberController::class, 'import'])->name('subscribers.import');
    Route::get('subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');

    // Visitors routes
    Route::get('visitors', [VisitorController::class, 'index'])->name('visitors.index');
});
