<?php

use Illuminate\Support\Facades\Route;
use Modules\Testimonial\Http\Controllers\Admin\TestimonialController;

Route::middleware('can:Testimonials Management')->group(function () {
    Route::delete('testimonials/deleteMulti', [TestimonialController::class, 'deleteMulti'])->name('testimonials.deleteMulti');
    Route::resource('testimonials', TestimonialController::class)->except(['show']);
});
