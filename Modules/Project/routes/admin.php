<?php

use Illuminate\Support\Facades\Route;
use Modules\Project\Http\Controllers\Admin\ProjectController;
use Modules\Project\Http\Controllers\Admin\ProjectStatusController;
use Modules\Project\Http\Controllers\Admin\ProjectUseCaseController;

Route::middleware(['can:Project Management'])->group(function () {
    Route::delete('projects/deleteMulti', [ProjectController::class, 'deleteMulti'])->name('projects.deleteMulti');
    Route::resource('projects', ProjectController::class)->except(['show']);

    Route::resource('project-statuses', ProjectStatusController::class)->except(['show']);

    Route::delete('project-use-cases/deleteMulti', [ProjectUseCaseController::class, 'deleteMulti'])->name('project-use-cases.deleteMulti');
    Route::resource('project-use-cases', ProjectUseCaseController::class)->except(['show']);
});
