<?php

use Illuminate\Support\Facades\Route;
use Modules\Team\Http\Controllers\Admin\TeamController;

Route::middleware('can:Team Management')->group(function () {
    Route::delete('teams/deleteMulti', [TeamController::class, 'deleteMulti'])->name('teams.deleteMulti');
    Route::resource('teams', TeamController::class)->except(['show']);
});
