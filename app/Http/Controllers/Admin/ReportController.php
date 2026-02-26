<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Models\Warehouse;
use App\Models\Category;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;
    
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }
    
    public function index()
    {
        return view('admin.reports.index');
    }
    
    /**
     * تقرير المخزون الحالي
     */
    public function currentStock(Request $request)
    {
        $filters = $request->only(['warehouse_id', 'category_id', 'low_stock_only']);
        
        $report = $this->reportService->currentStockReport($filters);
        
        $warehouses = Warehouse::active()->get();
        $categories = Category::active()->get();
        
        return view('admin.reports.current-stock', compact('report', 'warehouses', 'categories'));
    }
    
    /**
     * تقرير حركات المخزون
     */
    public function movements(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to', 'warehouse_id', 'movement_type', 'product_id']);
        
        $report = $this->reportService->stockMovementsReport($filters);
        
        $warehouses = Warehouse::active()->get();
        
        return view('admin.reports.movements', compact('report', 'warehouses'));
    }
    
    /**
     * تقرير الصلاحية
     */
    public function expiry(Request $request)
    {
        $filters = $request->only(['warehouse_id', 'status']);
        
        $report = $this->reportService->expiryReport($filters);
        
        $warehouses = Warehouse::active()->get();
        
        return view('admin.reports.expiry', compact('report', 'warehouses'));
    }
    
    /**
     * تقرير قيمة المخزون
     */
    public function stockValue(Request $request)
    {
        $filters = $request->only(['warehouse_id']);
        
        $report = $this->reportService->stockValueReport($filters);
        
        $warehouses = Warehouse::active()->get();
        
        return view('admin.reports.stock-value', compact('report', 'warehouses'));
    }
    
    /**
     * تقرير المخزون البطيء
     */
    public function slowMoving(Request $request)
    {
        $days = $request->input('days', 90);
        $filters = $request->only(['category_id']);
        
        $report = $this->reportService->slowMovingReport($days, $filters);
        
        $categories = Category::active()->get();
        
        return view('admin.reports.slow-moving', compact('report', 'categories', 'days'));
    }
    
    /**
     * تقرير أكثر المنتجات حركة
     */
    public function topMoving(Request $request)
    {
        $days = $request->input('days', 30);
        $limit = $request->input('limit', 10);
        
        $report = $this->reportService->topMovingProductsReport($days, $limit);
        
        return view('admin.reports.top-moving', compact('report', 'days', 'limit'));
    }
}