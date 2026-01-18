<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Admin\DashboardController;
use Modules\User\Http\Controllers\Admin\ProfileController;
use Modules\User\Http\Controllers\Admin\RoleController;
use Modules\User\Http\Controllers\Admin\StaffController;
use Modules\User\Http\Controllers\Admin\UserController;

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

// Profile (for any authenticated admin)
Route::prefix('profile')->name('profile.')
    ->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('/', [ProfileController::class, 'update'])->name('update');
    });

Route::middleware('can:Hr Management')
    ->group(function () {
        Route::prefix('roles')->name('roles.')
            ->group(function () {
                Route::post('assign_users', [RoleController::class, 'assignUsersToRole'])->name('assign_users');
                Route::post('remove_user_from_role', [RoleController::class, 'removeUserFromRole'])->name('remove_user_from_role');
                Route::post('remove_users_from_role', [RoleController::class, 'removeUsersFromRole'])->name('remove_users_from_role');
                Route::get('delete_role/{id}', [RoleController::class, 'delete_role'])->name('delete_role');
            });
        Route::resource('roles', RoleController::class)->except('destroy');

        Route::resource('staffs', StaffController::class)->except('create', 'edit', 'show');

        Route::resource('users', UserController::class)->except('create', 'edit', 'show');
    });
