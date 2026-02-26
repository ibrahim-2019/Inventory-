<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\StockInController;
use App\Http\Controllers\Admin\StockOutController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Products
    Route::resource('products', ProductController::class);
    
    // Brands
    Route::resource('brands', BrandController::class);
    
    // Categories
    Route::resource('categories', CategoryController::class);
    
    // Units
    Route::resource('units', UnitController::class);
    
    // Warehouses
    Route::resource('warehouses', WarehouseController::class);
    
    // Stock Management
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::resource('in', StockInController::class);
        Route::resource('out', StockOutController::class);
    });
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/current-stock', [ReportController::class, 'currentStock'])->name('reports.current-stock');
    Route::get('/reports/movements', [ReportController::class, 'movements'])->name('reports.movements');
    Route::get('/reports/expiry', [ReportController::class, 'expiry'])->name('reports.expiry');
    Route::get('/reports/stock-value', [ReportController::class, 'stockValue'])->name('reports.stock-value');
    Route::get('/reports/slow-moving', [ReportController::class, 'slowMoving'])->name('reports.slow-moving');
    Route::get('/reports/top-moving', [ReportController::class, 'topMoving'])->name('reports.top-moving');
});

require __DIR__.'/auth.php';
