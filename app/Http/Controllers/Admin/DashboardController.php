<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Models\ProductStock;
use App\Models\ProductStockBatch;
use App\Models\StockMovement;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $reportService;
    
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }
    
    public function index()
    {
        $summary = $this->reportService->dashboardSummary();
        
        $recentMovements = StockMovement::with(['product', 'warehouse'])
            ->latest()
            ->limit(5)
            ->get();
        
        $lowStockProducts = ProductStock::with(['product', 'warehouse'])
            ->whereHas('product', function($q) {
                $q->whereRaw('product_stock.available_quantity <= products.alert_quantity');
            })
            ->limit(5)
            ->get();
        
        $expiringBatches = ProductStockBatch::with(['product', 'warehouse'])
            ->expiringSoon(7)
            ->where('status', 'active')
            ->limit(5)
            ->get();
        
        return Inertia::render('Admin/Dashboard', [
            'summary' => $summary,
            'recentMovements' => $recentMovements,
            'lowStockProducts' => $lowStockProducts,
            'expiringBatches' => $expiringBatches,
        ]);
    }
}