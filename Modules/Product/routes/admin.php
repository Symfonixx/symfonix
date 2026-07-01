<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Admin\ProductCategoryController;
use Modules\Product\Http\Controllers\Admin\ProductController;

Route::middleware(['can:Product Management'])->group(function () {
    Route::patch('products/{product}/toggle-published', [ProductController::class, 'togglePublished'])
        ->name('products.toggle-published');
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('product-categories', ProductCategoryController::class)->except(['show']);
});
