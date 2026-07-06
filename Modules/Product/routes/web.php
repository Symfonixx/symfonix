<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

    Route::get('products', [ProductController::class, 'index'])->name('product.index');
    Route::get('products/{slug}', [ProductController::class, 'show'])->name('product.show');

    Route::redirect('product', 'products', 301);
    Route::get('product/{slug}', fn (string $slug) => redirect("products/{$slug}", 301));

