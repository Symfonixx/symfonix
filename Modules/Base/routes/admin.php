<?php

use Illuminate\Support\Facades\Route;
use Modules\Base\Http\Controllers\Admin\FileManager;
use Modules\Base\Http\Controllers\Admin\LogController;
use Modules\Base\Http\Controllers\Admin\SeoController;
use Modules\Base\Http\Controllers\Admin\SettingsController;
use Modules\Base\Http\Controllers\Admin\SystemConfigurationController;
use UniSharp\LaravelFilemanager\Lfm;

// Group for Settings Management
Route::middleware('can:Settings Management')->group(function () {
    Route::resource('settings', SettingsController::class)->only(['index', 'store']);
    Route::get('system-configurations', [SystemConfigurationController::class, 'index'])->name('system-configurations.index');
    Route::post('system-configurations', [SystemConfigurationController::class, 'store'])->name('system-configurations.store');
    Route::resource('seo', SeoController::class)->only(['index', 'store']);
});

// Group for Logs Management
Route::middleware('can:Logs Management')->group(function () {
    Route::delete('logs/deleteMulti', [LogController::class, 'deleteMulti'])->name('logs.deleteMulti');
    Route::resource('logs', LogController::class)->only(['index', 'show']);
});

Route::get('filemanager', [FileManager::class, 'index'])->name('filemanager.index');

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['can:Media Management']], function () {
    Lfm::routes();
});
