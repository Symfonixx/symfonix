<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Http\Controllers\PublicQuoteController;

Route::middleware(['auth'])->group(function () {
    Route::get('/quote/{uuid}', [PublicQuoteController::class, 'show'])->name('quotes.public');
    Route::get('/quote/{uuid}/pdf', [PublicQuoteController::class, 'downloadPdf'])->name('quotes.public.pdf');
    Route::post('/quote/{uuid}/accept', [PublicQuoteController::class, 'accept'])->name('quotes.public.accept');
    Route::post('/quote/{uuid}/reject', [PublicQuoteController::class, 'reject'])->name('quotes.public.reject');
});
