<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStockBatch;
use App\Models\StockMovement;
use App\Models\StockMovementBatch;
use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * إضافة مخزون (Stock IN)
     */
    public function stockIn(array $data)
    {
        return DB::transaction(function () use ($data) {
            
            // 1. التحقق من المنتج
            $product = Product::findOrFail($data['product_id']);
            
            // 2. تحويل الكمية للوحدة الأساسية (Base Unit)
            $quantityInBaseUnit = $this->convertToBaseUnit(
                $data['quantity'],
                $data['unit_id'] ?? $product->base_unit_id,
                $product->id
            );
            
            // 3. حساب التكلفة للوحدة الأساسية
            $unitCostInBase = $this->calculateUnitCostInBase(
                $data['unit_cost'],
                $data['unit_id'] ?? $product->base_unit_id,
                $product->id
            );
            
            // 4. توليد رقم Batch
            $batchNumber = $data['batch_number'] ?? $this->generateBatchNumber();
            
            // 5. إنشاء الـ Batch
            $batch = ProductStockBatch::create([
                'product_id' => $product->id,
                'warehouse_id' => $data['warehouse_id'],
                'batch_number' => $batchNumber,
                'supplier_name' => $data['supplier_name'] ?? null,
                'quantity_in' => $quantityInBaseUnit,
                'quantity_remaining' => $quantityInBaseUnit,
                'unit_cost' => $unitCostInBase,
                'total_cost' => $quantityInBaseUnit * $unitCostInBase,
                'purchase_date' => $data['purchase_date'] ?? now(),
                'expiry_date' => $data['expiry_date'] ?? null,
                'manufacture_date' => $data['manufacture_date'] ?? null,
                'status' => 'active',
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
            
            // 6. إنشاء حركة Stock IN
            $movement = StockMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $data['warehouse_id'],
                'movement_type' => 'in',
                'quantity' => $quantityInBaseUnit,
                'total_cost' => $batch->total_cost,
                'average_unit_cost' => $unitCostInBase,
                'reference_type' => $data['reference_type'] ?? 'manual',
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
            
            // 7. ربط الحركة بالـ Batch
            StockMovementBatch::create([
                'stock_movement_id' => $movement->id,
                'batch_id' => $batch->id,
                'quantity' => $quantityInBaseUnit,
                'unit_cost' => $unitCostInBase,
                'total_cost' => $batch->total_cost,
            ]);
            
            // 8. تحديث المخزون الإجمالي
            $this->updateProductStock($product->id, $data['warehouse_id']);
            
            return [
                'success' => true,
                'batch' => $batch->load('product', 'warehouse'),
                'movement' => $movement,
                'quantity_in_base_unit' => $quantityInBaseUnit,
                'message' => "تم إضافة {$quantityInBaseUnit} {$product->baseUnit->short_name} بنجاح",
            ];
        });
    }
    
    /**
     * خصم مخزون (Stock OUT) - FIFO/FEFO
     */
    public function stockOut(array $data)
    {
        return DB::transaction(function () use ($data) {
            
            // 1. التحقق من المنتج
            $product = Product::findOrFail($data['product_id']);
            
            // 2. تحويل الكمية للوحدة الأساسية
            $quantityInBaseUnit = $this->convertToBaseUnit(
                $data['quantity'],
                $data['unit_id'] ?? $product->base_unit_id,
                $product->id
            );
            
            // 3. التحقق من توفر المخزون
            $availableStock = $this->getAvailableStock($product->id, $data['warehouse_id']);
            
            if ($availableStock < $quantityInBaseUnit) {
                throw new Exception(
                    "المخزون غير كافي! المتاح: {$availableStock} {$product->baseUnit->short_name}، المطلوب: {$quantityInBaseUnit}"
                );
            }
            
            // 4. جلب الـ Batches حسب استراتيجية السحب
            $batches = $this->getBatchesForWithdrawal($product, $data['warehouse_id']);
            
            // 5. توزيع الكمية على الـ Batches
            $distribution = $this->distributQuantityOnBatches($batches, $quantityInBaseUnit);
            
            // 6. إنشاء حركة Stock OUT
            $movement = StockMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $data['warehouse_id'],
                'movement_type' => $data['movement_type'] ?? 'out',
                'quantity' => $quantityInBaseUnit,
                'total_cost' => $distribution['total_cost'],
                'average_unit_cost' => $distribution['average_cost'],
                'reference_type' => $data['reference_type'] ?? 'manual',
                'reference_number' => $data['reference_number'] ?? null,
                'reason' => $data['reason'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
            
            // 7. تطبيق التوزيع وربط الحركة بالـ Batches
            foreach ($distribution['batches_used'] as $batchDistribution) {
                // خصم الكمية من الـ Batch
                $batch = ProductStockBatch::find($batchDistribution['batch_id']);
                $batch->quantity_remaining -= $batchDistribution['quantity'];
                
                // تحديث حالة الـ Batch إذا نفد
                if ($batch->quantity_remaining <= 0) {
                    $batch->status = 'exhausted';
                }
                
                $batch->save();
                
                // ربط الحركة بالـ Batch
                StockMovementBatch::create([
                    'stock_movement_id' => $movement->id,
                    'batch_id' => $batch->id,
                    'quantity' => $batchDistribution['quantity'],
                    'unit_cost' => $batchDistribution['unit_cost'],
                    'total_cost' => $batchDistribution['total_cost'],
                ]);
            }
            
            // 8. تحديث المخزون الإجمالي
            $this->updateProductStock($product->id, $data['warehouse_id']);
            
            return [
                'success' => true,
                'movement' => $movement->load('product', 'warehouse', 'batches'),
                'quantity_in_base_unit' => $quantityInBaseUnit,
                'total_cost' => $distribution['total_cost'],
                'average_cost' => $distribution['average_cost'],
                'batches_used' => $distribution['batches_used'],
                'message' => "تم خصم {$quantityInBaseUnit} {$product->baseUnit->short_name} بنجاح",
            ];
        });
    }
    
    /**
     * جلب الـ Batches حسب استراتيجية السحب (FIFO/FEFO)
     */
    protected function getBatchesForWithdrawal($product, $warehouseId)
    {
        $query = ProductStockBatch::where('product_id', $product->id)
            ->where('warehouse_id', $warehouseId)
            ->where('quantity_remaining', '>', 0)
            ->where('status', 'active');
        
        // منع السحب من المنتجات منتهية الصلاحية
        if ($product->auto_block_expired) {
            $query->where(function($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>', now());
            });
        }
        
        // اختيار الاستراتيجية
        switch ($product->withdrawal_strategy) {
            case 'fefo':
                // First Expired, First Out
                return $query->fefoOrder()->get();
                
            case 'fifo':
                // First In, First Out
                return $query->fifoOrder()->get();
                
            case 'manual':
                // للاختيار اليدوي - نرجع كل الـ Batches المتاحة
                return $query->orderBy('purchase_date', 'asc')->get();
                
            default:
                return $query->fifoOrder()->get();
        }
    }
    
    /**
     * توزيع الكمية على الـ Batches
     */
    protected function distributQuantityOnBatches($batches, $quantityNeeded)
    {
        $remainingQty = $quantityNeeded;
        $totalCost = 0;
        $usedBatches = [];
        
        foreach ($batches as $batch) {
            if ($remainingQty <= 0) break;
            
            // حساب الكمية المأخوذة من هذا الـ Batch
            $qtyToTake = min($remainingQty, $batch->quantity_remaining);
            
            // حساب التكلفة
            $costFromBatch = $qtyToTake * $batch->unit_cost;
            $totalCost += $costFromBatch;
            
            $usedBatches[] = [
                'batch_id' => $batch->id,
                'batch_number' => $batch->batch_number,
                'quantity' => $qtyToTake,
                'unit_cost' => $batch->unit_cost,
                'total_cost' => $costFromBatch,
            ];
            
            $remainingQty -= $qtyToTake;
        }
        
        // التحقق من اكتمال الكمية
        if ($remainingQty > 0) {
            throw new Exception("المخزون غير كافي! ينقص: {$remainingQty}");
        }
        
        return [
            'batches_used' => $usedBatches,
            'total_cost' => $totalCost,
            'average_cost' => $totalCost / $quantityNeeded,
        ];
    }
    
    /**
     * تحديث المخزون الإجمالي
     */
    protected function updateProductStock($productId, $warehouseId)
    {
        // حساب الكمية الكلية من الـ Batches النشطة
        $totalQuantity = ProductStockBatch::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('status', 'active')
            ->sum('quantity_remaining');
        
        // حساب التكلفة الكلية
        $totalCost = ProductStockBatch::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('status', 'active')
            ->selectRaw('SUM(quantity_remaining * unit_cost) as total')
            ->value('total') ?? 0;
        
        // حساب متوسط التكلفة
        $averageCost = $totalQuantity > 0 ? ($totalCost / $totalQuantity) : 0;
        
        // تحديث أو إنشاء السجل
        ProductStock::updateOrCreate(
            [
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
            ],
            [
                'total_quantity' => $totalQuantity,
                'total_cost' => $totalCost,
                'average_cost' => $averageCost,
                'last_updated' => now(),
            ]
        );
    }
    
    /**
     * تحويل الكمية للوحدة الأساسية
     */
    protected function convertToBaseUnit($quantity, $unitId, $productId)
    {
        $product = Product::find($productId);
        
        // إذا الوحدة هي الوحدة الأساسية نفسها
        if ($unitId == $product->base_unit_id) {
            return $quantity;
        }
        
        // جلب معامل التحويل
        $conversion = $product->unitConversions()
            ->where('unit_id', $unitId)
            ->first();
        
        if (!$conversion) {
            throw new Exception("معامل التحويل غير موجود للوحدة المحددة");
        }
        
        return $quantity * $conversion->conversion_factor;
    }
    
    /**
     * حساب التكلفة للوحدة الأساسية
     */
    protected function calculateUnitCostInBase($unitCost, $unitId, $productId)
    {
        $product = Product::find($productId);
        
        // إذا الوحدة هي الوحدة الأساسية نفسها
        if ($unitId == $product->base_unit_id) {
            return $unitCost;
        }
        
        // جلب معامل التحويل
        $conversion = $product->unitConversions()
            ->where('unit_id', $unitId)
            ->first();
        
        if (!$conversion) {
            throw new Exception("معامل التحويل غير موجود للوحدة المحددة");
        }
        
        // السعر للوحدة الكبيرة ÷ عدد الوحدات الصغيرة = السعر للوحدة الصغيرة
        return $unitCost / $conversion->conversion_factor;
    }
    
    /**
     * الحصول على المخزون المتاح
     */
    public function getAvailableStock($productId, $warehouseId)
    {
        return ProductStockBatch::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('status', 'active')
            ->sum('quantity_remaining');
    }
    
    /**
     * توليد رقم Batch
     */
    protected function generateBatchNumber()
    {
        return 'BATCH-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
    
    /**
     * الحصول على تفاصيل المخزون
     */
    public function getStockDetails($productId, $warehouseId)
    {
        $product = Product::with('baseUnit')->findOrFail($productId);
        
        $batches = ProductStockBatch::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('status', 'active')
            ->orderBy('purchase_date', 'asc')
            ->get();
        
        $summary = ProductStock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();
        
        return [
            'product' => $product,
            'batches' => $batches,
            'summary' => $summary,
            'total_quantity' => $summary->total_quantity ?? 0,
            'available_quantity' => $summary->available_quantity ?? 0,
            'reserved_quantity' => $summary->reserved_quantity ?? 0,
            'total_cost' => $summary->total_cost ?? 0,
            'average_cost' => $summary->average_cost ?? 0,
            'total_batches' => $batches->count(),
        ];
    }
    
    /**
     * تعديل المخزون (Stock Adjustment)
     */
    public function stockAdjustment(array $data)
    {
        return DB::transaction(function () use ($data) {
            
            $product = Product::findOrFail($data['product_id']);
            
            // الكمية الحالية
            $currentStock = $this->getAvailableStock($product->id, $data['warehouse_id']);
            
            // الكمية الجديدة (بالوحدة الأساسية)
            $newQuantity = $this->convertToBaseUnit(
                $data['new_quantity'],
                $data['unit_id'] ?? $product->base_unit_id,
                $product->id
            );
            
            $difference = $newQuantity - $currentStock;
            
            if ($difference > 0) {
                // زيادة - نضيف batch جديد
                return $this->stockIn([
                    'product_id' => $product->id,
                    'warehouse_id' => $data['warehouse_id'],
                    'quantity' => $difference,
                    'unit_id' => $product->base_unit_id,
                    'unit_cost' => $data['unit_cost'] ?? 0,
                    'reference_type' => 'adjustment',
                    'notes' => $data['notes'] ?? 'Stock adjustment',
                ]);
            } else {
                // نقصان - نخصم
                return $this->stockOut([
                    'product_id' => $product->id,
                    'warehouse_id' => $data['warehouse_id'],
                    'quantity' => abs($difference),
                    'unit_id' => $product->base_unit_id,
                    'movement_type' => 'adjustment',
                    'reference_type' => 'adjustment',
                    'reason' => $data['reason'] ?? 'Stock adjustment',
                    'notes' => $data['notes'] ?? null,
                ]);
            }
        });
    }
}