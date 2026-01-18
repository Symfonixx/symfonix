<?php
use Illuminate\Support\Facades\Route;
use Modules\SearchEngine\app\Http\Controllers\Admin\SearchKeywordController;

Route::middleware('can:Support Management')->group(function () {
    Route::delete('search_keywords', [SearchKeywordController::class, 'deleteMulti'])->name('search_keywords.deleteMulti');
    Route::get('search_keywords/export', [SearchKeywordController::class, 'export'])->name('search_keywords.export');
    Route::get('search_keywords', [SearchKeywordController::class, 'index'])->name('search_keywords.index');
});
