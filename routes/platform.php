<?php

use App\Http\Controllers\Platform\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Platform\DashboardController;
use App\Http\Controllers\Platform\TenantController;
use Illuminate\Support\Facades\Route;

Route::prefix('platform')->name('platform.')->group(function () {
    Route::middleware('guest:platform')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);
    });

    Route::middleware('auth:platform')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::get('tenants/data', [TenantController::class, 'data'])->name('tenants.data');
        Route::get('tenants/{tenant}/users/data', [TenantController::class, 'usersData'])->name('tenants.users.data');

        Route::get('tenants', [TenantController::class, 'index'])->name('tenants.index');
        Route::get('tenants/create', [TenantController::class, 'create'])->name('tenants.create');
        Route::post('tenants', [TenantController::class, 'store'])->name('tenants.store');
        Route::get('tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
        Route::get('tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
        Route::put('tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
        Route::patch('tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
        Route::patch('tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
    });
});
