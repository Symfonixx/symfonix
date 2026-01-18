<?php

use Illuminate\Support\Facades\Route;
use Modules\Support\app\Http\Controllers\Admin\ContactFormController;
use Modules\Support\app\Http\Controllers\Admin\SubscriberController;
use Modules\Support\app\Http\Controllers\Admin\FirewallController;
use Modules\Support\app\Http\Controllers\Admin\BlockedIpController;
use Modules\Support\app\Http\Controllers\Admin\FirewallLogController;
use Modules\Support\app\Http\Controllers\Admin\ComplaintController;

Route::middleware('can:Support Management')->group(function () {
    // Subscriber routes
    Route::delete('subscribers', [SubscriberController::class, 'deleteMulti'])->name('subscribers.deleteMulti');
    Route::get('subscribers/export', [SubscriberController::class, 'export'])->name('subscribers.export');
    Route::post('subscribers/import', [SubscriberController::class, 'import'])->name('subscribers.import');
    Route::get('subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');

    // Contact form routes
    Route::delete('contact_forms', [ContactFormController::class, 'deleteMulti'])->name('contact_forms.deleteMulti');
    Route::get('contact_forms/export', [ContactFormController::class, 'export'])->name('contact_forms.export');
    Route::get('contact_forms', [ContactFormController::class, 'index'])->name('contact_forms.index');

    // Firewall routes
    Route::post('firewall/block', [FirewallController::class, 'block'])->name('firewall.block');
    Route::post('firewall/unblock', [FirewallController::class, 'unblock'])->name('firewall.unblock');

    // Blocked IPs list
    Route::get('blocked_ips', [BlockedIpController::class, 'index'])->name('blocked_ips.index');

    // Firewall Logs list
    Route::get('firewall_logs/export', [FirewallLogController::class, 'export'])->name('firewall_logs.export');
    Route::get('firewall_logs', [FirewallLogController::class, 'index'])->name('firewall_logs.index');

    // Complaints routes
    Route::get('complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('complaints/export', [ComplaintController::class, 'export'])->name('complaints.export');
    Route::delete('complaints', [ComplaintController::class, 'deleteMulti'])->name('complaints.deleteMulti');
    Route::post('complaints/{complaint}/resolve', [ComplaintController::class, 'resolve'])->name('complaints.resolve');
});
