<?php

use Illuminate\Support\Facades\Route;
use Modules\AI\Http\Controllers\Admin\AssistantController;
use Modules\AI\Http\Controllers\Admin\ContentGenerationController;
use Modules\AI\Http\Controllers\Admin\ImageEditController;

Route::prefix('ai')->name('ai.')->group(function () {
    Route::post('image-edits/generate', [ImageEditController::class, 'generate'])->name('image-edits.generate');
    Route::post('image-edits/apply', [ImageEditController::class, 'apply'])->name('image-edits.apply');

    Route::post('content/generate', [ContentGenerationController::class, 'generate'])->name('content.generate');
    Route::post('content/generate-form', [ContentGenerationController::class, 'generateForm'])->name('content.generate-form');
    Route::post('content/generate-quote', [ContentGenerationController::class, 'generateQuote'])->name('content.generate-quote');
    Route::post('content/generate-marketing-email', [ContentGenerationController::class, 'generateMarketingEmail'])->name('content.generate-marketing-email');
    Route::post('leads/{lead}/follow-up', [ContentGenerationController::class, 'generateFollowUp'])->name('leads.follow-up');

    Route::prefix('assistant')->name('assistant.')->group(function () {
        Route::get('bootstrap', [AssistantController::class, 'bootstrap'])->name('bootstrap');
        Route::get('conversations', [AssistantController::class, 'index'])->name('conversations.index');
        Route::post('conversations', [AssistantController::class, 'store'])->name('conversations.store');
        Route::get('conversations/{conversation}', [AssistantController::class, 'show'])->name('conversations.show');
        Route::delete('conversations/{conversation}', [AssistantController::class, 'destroy'])->name('conversations.destroy');
        Route::post('conversations/{conversation}/messages', [AssistantController::class, 'messages'])->name('conversations.messages');
        Route::post('conversations/{conversation}/regenerate', [AssistantController::class, 'regenerate'])->name('conversations.regenerate');
    });
});
