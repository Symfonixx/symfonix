<?php

use Illuminate\Support\Facades\Route;
use Modules\Services\Http\Controllers\Admin\ServiceController;
use Modules\Services\Http\Controllers\Admin\ServiceCategoryController;

Route::prefix('services')->name('services.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('create', [ServiceController::class, 'create'])->name('create');
    Route::post('store', [ServiceController::class, 'store'])->name('store');
    Route::get('{service}/edit', [ServiceController::class, 'edit'])->name('edit');
    Route::put('{service}/update', [ServiceController::class, 'update'])->name('update');
    Route::delete('delete-multi', [ServiceController::class, 'deleteMulti'])->name('deleteMulti');
});

Route::delete('service_categories/deleteMulti', [ServiceCategoryController::class, 'deleteMulti'])->name('service_categories.deleteMulti');
Route::resource('service_categories', ServiceCategoryController::class)->except(['destroy', 'show']);
