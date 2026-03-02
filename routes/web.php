<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\ReportController;

// ─── Public ───────────────────────────────────────────────────────
Route::get('/', function () {
    return Inertia::render('Welcome/Index', [
        'auth' => ['user' => auth()->user()],
    ]);
})->name('home');

// ─── Auth (Breeze) ────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ─── Admin (requires auth + verified) ────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified'])
    ->group(function () {

        // ── Dashboard ──────────────────────────────────────────────
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // ── Products ───────────────────────────────────────────────
        Route::resource('products', ProductController::class);

        // ── Stock In / Out ─────────────────────────────────────────
        Route::prefix('stock')->name('stock.')->group(function () {
            Route::get('/in',   [StockController::class, 'inIndex']) ->name('in');
            Route::post('/in',  [StockController::class, 'storeIn']) ->name('in.store');
            Route::get('/out',  [StockController::class, 'outIndex'])->name('out');
            Route::post('/out', [StockController::class, 'storeOut'])->name('out.store');
        });

        // ── Warehouses ─────────────────────────────────────────────
        Route::resource('warehouses', WarehouseController::class);

        // ── Reports ────────────────────────────────────────────────
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/current-stock', [ReportController::class, 'currentStock'])
                ->name('current-stock');
            Route::get('/expiry', [ReportController::class, 'expiry'])
                ->name('expiry');
        });

    });