<?php

use Illuminate\Support\Facades\Route;
use Modules\Base\Http\Controllers\Admin\BackupController;
use Modules\Base\Http\Controllers\Admin\FileManager;
use Modules\Base\Http\Controllers\Admin\IntegrationsController;
use Modules\Base\Http\Controllers\Admin\LogController;
use Modules\Base\Http\Controllers\Admin\SeoController;
use Modules\Base\Http\Controllers\Admin\SettingsController;
use Modules\Base\Http\Controllers\Admin\SystemConfigurationController;
use UniSharp\LaravelFilemanager\Lfm;

// Group for Settings Management
Route::group([], function () {
    Route::resource('settings', SettingsController::class)->only(['index', 'store']);
    Route::get('system-configurations', [SystemConfigurationController::class, 'index'])->name('system-configurations.index');
    Route::post('system-configurations', [SystemConfigurationController::class, 'store'])->name('system-configurations.store');
    Route::post('system-configurations/fetch-rates', [SystemConfigurationController::class, 'fetchRates'])->name('system-configurations.fetch-rates');
    Route::post('system-configurations/test-fingerprint', [SystemConfigurationController::class, 'testFingerprint'])->name('system-configurations.test-fingerprint');
    Route::get('integrations', [IntegrationsController::class, 'index'])->name('integrations.index');
    Route::post('integrations', [IntegrationsController::class, 'store'])->name('integrations.store');
    Route::resource('seo', SeoController::class)->only(['index', 'store']);

    Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('backups', [BackupController::class, 'store'])->name('backups.store');
    Route::post('backups/import', [BackupController::class, 'import'])->name('backups.import');
    Route::post('backups/{filename}/restore', [BackupController::class, 'restore'])->name('backups.restore');
    Route::get('backups/{filename}/download', [BackupController::class, 'download'])->name('backups.download');
    Route::delete('backups/{filename}', [BackupController::class, 'destroy'])->name('backups.destroy');
});

// Group for Logs Management
Route::group([], function () {
    Route::delete('logs/deleteMulti', [LogController::class, 'deleteMulti'])->name('logs.deleteMulti');
    Route::resource('logs', LogController::class)->only(['index', 'show']);
});

Route::get('filemanager', [FileManager::class, 'index'])->name('filemanager.index');

Route::group(['prefix' => 'laravel-filemanager'], function () {
    Lfm::routes();
});
