<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SendMessageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TopbarController;
use App\Http\Controllers\WaBlastCampaignController;
use App\Http\Controllers\WaDeviceController;
use App\Http\Controllers\WaTemplateController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard.index'));

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/devices', [DashboardController::class, 'devices'])->name('devices');
        Route::get('/messages', [DashboardController::class, 'messages'])->name('messages');
        Route::get('/templates', [DashboardController::class, 'templates'])->name('templates');
        Route::get('/send', [SendMessageController::class, 'index'])->name('send');
        Route::post('/send/single', [SendMessageController::class, 'single'])->name('send.single');
        Route::post('/send/blast', [SendMessageController::class, 'blast'])->name('send.blast');
        Route::get('/appointments/search', [SendMessageController::class, 'searchAppointments'])->name('appointments.search');
        Route::get('/appointments/load-numbers', [SendMessageController::class, 'loadAppointmentNumbers'])->name('appointments.load-numbers');

        Route::get('/blasts', [WaBlastCampaignController::class, 'index'])->name('blasts');
        Route::get('/blasts/{blast}', [WaBlastCampaignController::class, 'show'])->name('blasts.show');
        Route::post('/blasts/{blast}/refresh', [WaBlastCampaignController::class, 'refresh'])->name('blasts.refresh');
        Route::post('/blasts/{blast}/retry-failed', [WaBlastCampaignController::class, 'retryFailed'])->name('blasts.retry-failed');
        Route::get('/blasts/{blast}/failed', [WaBlastCampaignController::class, 'failed'])->name('blasts.failed');

        Route::post('/devices', [WaDeviceController::class, 'store'])->name('devices.store');
        Route::post('/devices/{device}/connect', [WaDeviceController::class, 'connect'])->name('devices.connect');
        Route::get('/devices/{device}/status', [WaDeviceController::class, 'status'])->name('devices.status');
        Route::post('/devices/{device}/disconnect', [WaDeviceController::class, 'disconnect'])->name('devices.disconnect');
        Route::delete('/devices/{device}', [WaDeviceController::class, 'destroy'])->name('devices.destroy');

        Route::post('/templates', [WaTemplateController::class, 'store'])->name('templates.store');
        Route::put('/templates/{template}', [WaTemplateController::class, 'update'])->name('templates.update');
        Route::delete('/templates/{template}', [WaTemplateController::class, 'destroy'])->name('templates.destroy');
        Route::get('/templates/{template}/preview', [WaTemplateController::class, 'preview'])->name('templates.preview');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/whatsapp', [SettingsController::class, 'updateWhatsapp'])->name('settings.whatsapp');
        Route::post('/settings/test-connection', [SettingsController::class, 'testConnection'])->name('settings.test-connection');

        Route::get('/search', [TopbarController::class, 'search'])->name('search');
        Route::get('/notifications', [TopbarController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/read', [TopbarController::class, 'markNotificationsRead'])->name('notifications.read');
    });
});
