<?php

use Illuminate\Support\Facades\Route;
use Modules\Base\Http\Controllers\HomeController;
use Modules\Base\Http\Controllers\SitemapController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Dynamic sitemap.xml
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
