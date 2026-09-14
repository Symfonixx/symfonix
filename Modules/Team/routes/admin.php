<?php

use Illuminate\Support\Facades\Route;
use Modules\Team\Http\Controllers\Admin\TeamController;

Route::group([], function () {
    Route::delete('teams/deleteMulti', [TeamController::class, 'deleteMulti'])->name('teams.deleteMulti');
    Route::resource('teams', TeamController::class)->except(['show']);
});
