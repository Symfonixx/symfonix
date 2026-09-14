<?php

use Illuminate\Support\Facades\Route;
use Modules\Project\Http\Controllers\Admin\ProjectController;
use Modules\Project\Http\Controllers\Admin\ProjectStatusController;
use Modules\Project\Http\Controllers\Admin\ProjectUseCaseController;

Route::group([], function () {
    Route::delete('projects/deleteMulti', [ProjectController::class, 'deleteMulti'])->name('projects.deleteMulti');
    Route::post('projects/{project}/invoices', [ProjectController::class, 'storeInvoice'])->name('projects.invoices.store');
    Route::post('projects/{project}/employees', [ProjectController::class, 'assignEmployee'])->name('projects.employees.store');
    Route::post('projects/{project}/employees/{assignment}/finish', [ProjectController::class, 'finishEmployee'])->name('projects.employees.finish');
    Route::delete('projects/{project}/employees/{assignment}', [ProjectController::class, 'removeEmployee'])->name('projects.employees.destroy');
    Route::post('projects/{project}/expenses', [ProjectController::class, 'storeExpense'])->name('projects.expenses.store');
    Route::patch('projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.updateStatus');
    Route::resource('projects', ProjectController::class);

    Route::resource('project-statuses', ProjectStatusController::class)->except(['show']);

    Route::delete('project-use-cases/deleteMulti', [ProjectUseCaseController::class, 'deleteMulti'])->name('project-use-cases.deleteMulti');
    Route::resource('project-use-cases', ProjectUseCaseController::class)->except(['show']);
});
