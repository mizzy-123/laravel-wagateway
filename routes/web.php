<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WaDeviceController;
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

        Route::post('/devices', [WaDeviceController::class, 'store'])->name('devices.store');
        Route::post('/devices/{device}/connect', [WaDeviceController::class, 'connect'])->name('devices.connect');
        Route::get('/devices/{device}/status', [WaDeviceController::class, 'status'])->name('devices.status');
        Route::post('/devices/{device}/disconnect', [WaDeviceController::class, 'disconnect'])->name('devices.disconnect');
        Route::delete('/devices/{device}', [WaDeviceController::class, 'destroy'])->name('devices.destroy');
    });
});
