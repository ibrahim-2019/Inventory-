<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductStockBatch;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * تقرير المخزون الحالي
     */
    public function currentStockReport($filters = [])
    {
        $query = ProductStock::with(['product.baseUnit', 'product.category', 'warehouse']);
        
        // Filters
        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }
        
        if (!empty($filters['category_id'])) {
            $query->whereHas('product', function($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            });
        }
        
        if (!empty($filters['low_stock_only'])) {
            $query->whereHas('product', function($q) {
                $q->whereRaw('product_stock.available_quantity <= products.alert_quantity');
            });
        }
        
        $stocks = $query->get();
        
        return [
            'stocks' => $stocks,
            'summary' => [
                'total_products' => $stocks->count(),
                'total_value' => $stocks->sum('total_cost'),
                'low_stock_items' => $stocks->filter(function($stock) {
                    return $stock->available_quantity <= $stock->product->alert_quantity;
                })->count(),
            ],
        ];
    }
    
    /**
     * تقرير حركات المخزون
     */
    public function stockMovementsReport($filters = [])
    {
        $query = StockMovement::with(['product.baseUnit', 'warehouse', 'creator']);
        
        // Filters
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }
        
        if (!empty($filters['movement_type'])) {
            $query->where('movement_type', $filters['movement_type']);
        }
        
        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }
        
        $movements = $query->orderBy('created_at', 'desc')->get();
        
        return [
            'movements' => $movements,
            'summary' => [
                'total_movements' => $movements->count(),
                'total_in' => $movements->where('movement_type', 'in')->sum('quantity'),
                'total_out' => $movements->where('movement_type', 'out')->sum('quantity'),
                'total_cost_in' => $movements->where('movement_type', 'in')->sum('total_cost'),
                'total_cost_out' => $movements->where('movement_type', 'out')->sum('total_cost'),
            ],
        ];
    }
    
    /**
     * تقرير المنتجات منتهية أو قريبة من الانتهاء
     */
    public function expiryReport($filters = [])
    {
        $query = ProductStockBatch::with(['product.baseUnit', 'warehouse'])
            ->where('status', 'active')
            ->where('quantity_remaining', '>', 0)
            ->whereNotNull('expiry_date');
        
        // Filters
        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }
        
        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'expired':
                    $query->where('expiry_date', '<=', now());
                    break;
                case 'expiring_7_days':
                    $query->where('expiry_date', '>', now())
                          ->where('expiry_date', '<=', now()->addDays(7));
                    break;
                case 'expiring_30_days':
                    $query->where('expiry_date', '>', now())
                          ->where('expiry_date', '<=', now()->addDays(30));
                    break;
            }
        }
        
        $batches = $query->orderBy('expiry_date', 'asc')->get();
        
        // تصنيف الـ Batches
        $expired = $batches->filter(fn($b) => $b->isExpired());
        $expiring7Days = $batches->filter(fn($b) => !$b->isExpired() && $b->isExpiringSoon(7));
        $expiring30Days = $batches->filter(fn($b) => !$b->isExpired() && $b->isExpiringSoon(30) && !$b->isExpiringSoon(7));
        
        return [
            'batches' => $batches,
            'expired' => $expired,
            'expiring_7_days' => $expiring7Days,
            'expiring_30_days' => $expiring30Days,
            'summary' => [
                'total_batches' => $batches->count(),
                'expired_count' => $expired->count(),
                'expired_value' => $expired->sum(fn($b) => $b->quantity_remaining * $b->unit_cost),
                'expiring_7_days_count' => $expiring7Days->count(),
                'expiring_30_days_count' => $expiring30Days->count(),
            ],
        ];
    }
    
    /**
     * تقرير قيمة المخزون
     */
    public function stockValueReport($filters = [])
    {
        $query = ProductStock::with(['product.category', 'product.baseUnit', 'warehouse']);
        
        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }
        
        $stocks = $query->get();
        
        // تجميع حسب الفئة
        $byCategory = $stocks->groupBy('product.category.name')->map(function($group) {
            return [
                'count' => $group->count(),
                'total_quantity' => $group->sum('total_quantity'),
                'total_value' => $group->sum('total_cost'),
            ];
        });
        
        // تجميع حسب المخزن
        $byWarehouse = $stocks->groupBy('warehouse.name')->map(function($group) {
            return [
                'count' => $group->count(),
                'total_quantity' => $group->sum('total_quantity'),
                'total_value' => $group->sum('total_cost'),
            ];
        });
        
        return [
            'stocks' => $stocks,
            'by_category' => $byCategory,
            'by_warehouse' => $byWarehouse,
            'summary' => [
                'total_products' => $stocks->count(),
                'total_quantity' => $stocks->sum('total_quantity'),
                'total_value' => $stocks->sum('total_cost'),
                'average_value_per_product' => $stocks->count() > 0 
                    ? $stocks->sum('total_cost') / $stocks->count() 
                    : 0,
            ],
        ];
    }
    
    /**
     * تقرير المخزون البطيء الحركة
     */
    public function slowMovingReport($days = 90, $filters = [])
    {
        $dateThreshold = now()->subDays($days);
        
        // المنتجات اللي ماتحركتش من فترة
        $query = Product::with(['baseUnit', 'stock'])
            ->whereHas('stock', function($q) {
                $q->where('total_quantity', '>', 0);
            })
            ->whereDoesntHave('movements', function($q) use ($dateThreshold) {
                $q->where('created_at', '>=', $dateThreshold);
            });
        
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        
        $products = $query->get();
        
        return [
            'products' => $products,
            'days_threshold' => $days,
            'summary' => [
                'total_products' => $products->count(),
                'total_value' => $products->sum(function($p) {
                    return $p->stock->sum('total_cost');
                }),
            ],
        ];
    }
    
    /**
     * تقرير أكثر المنتجات حركة
     */
    public function topMovingProductsReport($days = 30, $limit = 10)
    {
        $dateThreshold = now()->subDays($days);
        
        $topProducts = StockMovement::with(['product.baseUnit'])
            ->where('movement_type', 'out')
            ->where('created_at', '>=', $dateThreshold)
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();
        
        return [
            'products' => $topProducts,
            'period_days' => $days,
            'summary' => [
                'top_product' => $topProducts->first(),
                'total_quantity_moved' => $topProducts->sum('total_quantity'),
            ],
        ];
    }
    
    /**
     * Dashboard Summary
     */
    public function dashboardSummary()
    {
        return [
            'total_products' => Product::count(),
            'total_warehouses' => Warehouse::count(),
            'total_stock_value' => ProductStock::sum('total_cost'),
            'low_stock_count' => ProductStock::whereHas('product', function($q) {
                $q->whereRaw('product_stock.available_quantity <= products.alert_quantity');
            })->count(),
            'expired_batches' => ProductStockBatch::expired()
                ->where('status', 'active')
                ->count(),
            'expiring_soon' => ProductStockBatch::expiringSoon(7)
                ->where('status', 'active')
                ->count(),
            'movements_today' => StockMovement::whereDate('created_at', today())->count(),
            'stock_in_today' => StockMovement::whereDate('created_at', today())
                ->where('movement_type', 'in')
                ->sum('quantity'),
            'stock_out_today' => StockMovement::whereDate('created_at', today())
                ->where('movement_type', 'out')
                ->sum('quantity'),
        ];
    }
}