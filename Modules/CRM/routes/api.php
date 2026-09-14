<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Public CRM HTTP APIs are registered in admin.php / web.php.
});
