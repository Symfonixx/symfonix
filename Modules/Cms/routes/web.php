<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Modules\Cms\Http\Controllers\PageController;
use Modules\Cms\Http\Controllers\BlogController;

Route::get('about-us', [PageController::class, 'about_us'])->name('about-us');
Route::get('privacy-policy', [PageController::class, 'privacy_policy'])->name('privacy-policy');
Route::get('team', [PageController::class, 'team'])->name('team');
Route::get('testimonials', [PageController::class, 'testimonials'])->name('testimonials');
//Route::get('pricing', [PageController::class, 'pricing'])->name('pricing');
//Route::get('faq', [PageController::class, 'faq'])->name('faq');

Route::get('/p/{slug}', [PageController::class, 'view'])->name('page.view');

// Blog routes
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blogs.show');
