<?php

use App\Http\Controllers\Api\WaTemplateController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/whatsapp', WebhookController::class)->name('webhook.whatsapp');

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/templates', [WaTemplateController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('api.templates.store');
});
