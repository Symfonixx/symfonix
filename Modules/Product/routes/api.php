<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Catalog endpoints live on web routes; this controller is Inertia-only.
});
