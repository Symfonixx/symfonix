<?php

use Illuminate\Support\Facades\Route;
use Modules\Testimonial\Http\Controllers\Admin\TestimonialController;

Route::group([], function () {
    Route::delete('testimonials/deleteMulti', [TestimonialController::class, 'deleteMulti'])->name('testimonials.deleteMulti');
    Route::post('testimonials/{testimonial}/approve', [TestimonialController::class, 'approve'])->name('testimonials.approve');
    Route::post('testimonials/{testimonial}/unpublish', [TestimonialController::class, 'unpublish'])->name('testimonials.unpublish');
    Route::resource('testimonials', TestimonialController::class)->only(['index', 'edit', 'update']);
});
