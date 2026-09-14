<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Admin\AdminController;
use Modules\User\Http\Controllers\Admin\DashboardController;
use Modules\User\Http\Controllers\Admin\FingerprintController;
use Modules\User\Http\Controllers\Admin\JobApplicationController;
use Modules\User\Http\Controllers\Admin\JobPositionController;
use Modules\User\Http\Controllers\Admin\LeaveController;
use Modules\User\Http\Controllers\Admin\NotificationController;
use Modules\User\Http\Controllers\Admin\ProfileController;
use Modules\User\Http\Controllers\Admin\RoleController;
use Modules\User\Http\Controllers\Admin\StaffController;
use Modules\User\Http\Controllers\Admin\UserController;

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

// Profile (for any authenticated admin)
Route::prefix('profile')->name('profile.')
    ->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('/', [ProfileController::class, 'update'])->name('update');
    });

Route::group([], function () {
    Route::prefix('roles')->name('roles.')
        ->group(function () {
            Route::post('assign_users', [RoleController::class, 'assignUsersToRole'])->name('assign_users');
            Route::post('remove_user_from_role', [RoleController::class, 'removeUserFromRole'])->name('remove_user_from_role');
            Route::post('remove_users_from_role', [RoleController::class, 'removeUsersFromRole'])->name('remove_users_from_role');
            Route::get('delete_role/{id}', [RoleController::class, 'delete_role'])->name('delete_role');
        });
    Route::resource('roles', RoleController::class)->except(['destroy', 'create', 'edit']);

    Route::resource('admins', AdminController::class)->except('create', 'edit');

    Route::resource('employees', StaffController::class)->except('create', 'edit');
    Route::post('employees/{employee}/convert-to-admin', [StaffController::class, 'convertToAdmin'])
        ->name('employees.convert-to-admin');
    Route::post('employees/{employee}/add-to-team', [StaffController::class, 'addToTeam'])
        ->name('employees.add-to-team');

    Route::prefix('fingerprint')->name('fingerprint.')
        ->group(function () {
            Route::get('/', [FingerprintController::class, 'index'])->name('index');
            Route::post('test-connection', [FingerprintController::class, 'testConnection'])->name('test-connection');
            Route::post('enroll-all', [FingerprintController::class, 'enrollAll'])->name('enroll-all');
            Route::post('sync-attendance', [FingerprintController::class, 'syncAttendance'])->name('sync-attendance');
            Route::post('employees/{employee}/enroll', [FingerprintController::class, 'enroll'])->name('enroll');
        });

    Route::resource('leaves', LeaveController::class)
        ->parameters(['leaves' => 'leaveRequest'])
        ->except('create', 'edit', 'show');

    Route::resource('job-positions', JobPositionController::class)->except('show');
    Route::resource('job-applications', JobApplicationController::class)->only(['index', 'show', 'update']);
    Route::post('job-applications/{job_application}/hire', [JobApplicationController::class, 'hire'])
        ->name('job-applications.hire');
});

Route::group([], function () {
    Route::resource('customers', UserController::class)->except('create', 'edit', 'show');
});
