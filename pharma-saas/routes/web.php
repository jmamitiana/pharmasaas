<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'language'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    Route::middleware(['permission:manage_products'])->group(function () {
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    });
    
    Route::middleware(['permission:manage_sales'])->group(function () {
        Route::resource('sales', \App\Http\Controllers\Admin\SaleController::class);
        Route::get('sales/{sale}/receipt', [\App\Http\Controllers\Admin\SaleController::class, 'receipt'])->name('sales.receipt');
    });
    
    Route::middleware(['permission:manage_purchases'])->group(function () {
        Route::resource('purchases', \App\Http\Controllers\Admin\PurchaseController::class);
        Route::post('purchases/{purchase}/receive', [\App\Http\Controllers\Admin\PurchaseController::class, 'receive'])->name('purchases.receive');
    });
    
    Route::middleware(['permission:manage_transfers'])->group(function () {
        Route::resource('transfers', \App\Http\Controllers\Admin\TransferController::class);
    });
    
    Route::middleware(['permission:manage_stock'])->group(function () {
        Route::get('stock', [\App\Http\Controllers\Admin\StockController::class, 'index'])->name('stock.index');
    });
    
    Route::middleware(['permission:manage_settings'])->group(function () {
        Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    });
    
    Route::middleware(['permission:manage_settings'])->group(function () {
        Route::resource('backups', \App\Http\Controllers\Admin\BackupController::class)->only(['index', 'create', 'destroy']);
        Route::get('backups/{backup}/download', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('backups.download');
        Route::post('backups/{backup}/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('backups.restore');
    });
    
    Route::middleware(['permission:manage_users'])->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    });
    
    Route::get('categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::get('suppliers', [\App\Http\Controllers\Admin\SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('warehouses', [\App\Http\Controllers\Admin\WarehouseController::class, 'index'])->name('warehouses.index');
});

Route::get('language/{locale}', function ($locale) {
    if (in_array($locale, ['fr', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');
