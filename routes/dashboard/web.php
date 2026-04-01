<?php

use App\Http\Controllers\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('getData', [DashboardController::class, 'getData'])->name('dashboard.getData');
        Route::get('getTopProducts', [DashboardController::class, 'getTopProducts'])->name('dashboard.getTopProducts');
        Route::get('getConversionRate', [DashboardController::class, 'getConversionRate'])->name('dashboard.getConversionRate');
        Route::get('getCustomersActives', [DashboardController::class, 'getCustomersActives'])->name('dashboard.getCustomersActives');
        Route::get('getSales', [DashboardController::class, 'getSales'])->name('dashboard.getSales');
        Route::get('getSalesOrigin', [DashboardController::class, 'getSalesOrigin'])->name('dashboard.getSalesOrigin');
        Route::get('getParesYearMonth', [DashboardController::class, 'getParesYearMonth'])->name('dashboard.getParesYearMonth');
        Route::get('getSalesColor', [DashboardController::class, 'getSalesColor'])->name('dashboard.getSalesColor');
        Route::get('getSalesSizes', [DashboardController::class, 'getSalesSizes'])->name('dashboard.getSalesSizes');
        Route::get('getDeliveryTime', [DashboardController::class, 'getDeliveryTime'])->name('dashboard.getDeliveryTime');
    });
});
