<?php

namespace App\Services;

use App\Models\Product;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QRCodeService
{
    /**
     * توليد QR Code لمنتج
     */
    public function generateForProduct($productId)
    {
        $product = Product::findOrFail($productId);
        
        // البيانات المشفرة في الـ QR Code
        $data = json_encode([
            'type' => 'product',
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
        ]);
        
        // توليد الـ QR Code
        $qrCode = QrCode::format('png')
            ->size(300)
            ->generate($data);
        
        // حفظ الصورة
        $filename = "qr-product-{$product->id}-" . time() . ".png";
        $path = "qrcodes/products/{$filename}";
        
        Storage::disk('public')->put($path, $qrCode);
        
        // تحديث المنتج
        $product->update([
            'qr_code' => $path,
        ]);
        
        return [
            'success' => true,
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
            'data' => $data,
        ];
    }
    
    /**
     * توليد QR Code لـ Batch
     */
    public function generateForBatch($batchId)
    {
        $batch = \App\Models\ProductStockBatch::with('product')->findOrFail($batchId);
        
        $data = json_encode([
            'type' => 'batch',
            'id' => $batch->id,
            'batch_number' => $batch->batch_number,
            'product_id' => $batch->product_id,
            'product_name' => $batch->product->name,
            'expiry_date' => $batch->expiry_date?->format('Y-m-d'),
        ]);
        
        $qrCode = QrCode::format('png')
            ->size(300)
            ->generate($data);
        
        $filename = "qr-batch-{$batch->id}-" . time() . ".png";
        $path = "qrcodes/batches/{$filename}";
        
        Storage::disk('public')->put($path, $qrCode);
        
        return [
            'success' => true,
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
            'data' => $data,
        ];
    }
    
    /**
     * مسح QR Code
     */
    public function scan($data)
    {
        try {
            $decoded = json_decode($data, true);
            
            if (!$decoded || !isset($decoded['type'])) {
                throw new \Exception('Invalid QR Code data');
            }
            
            return match($decoded['type']) {
                'product' => $this->getProductInfo($decoded['id']),
                'batch' => $this->getBatchInfo($decoded['id']),
                default => throw new \Exception('Unknown QR Code type'),
            };
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * معلومات المنتج من QR Code
     */
    protected function getProductInfo($productId)
    {
        $product = Product::with(['baseUnit', 'stock.warehouse', 'category', 'brand'])
            ->findOrFail($productId);
        
        return [
            'success' => true,
            'type' => 'product',
            'data' => [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category?->name,
                'brand' => $product->brand?->name,
                'base_unit' => $product->baseUnit->name,
                'stock' => $product->stock->map(function($s) {
                    return [
                        'warehouse' => $s->warehouse->name,
                        'quantity' => $s->available_quantity,
                        'unit' => $s->product->baseUnit->short_name,
                    ];
                }),
            ],
        ];
    }
    
    /**
     * معلومات الـ Batch من QR Code
     */
    protected function getBatchInfo($batchId)
    {
        $batch = \App\Models\ProductStockBatch::with(['product.baseUnit', 'warehouse'])
            ->findOrFail($batchId);
        
        return [
            'success' => true,
            'type' => 'batch',
            'data' => [
                'id' => $batch->id,
                'batch_number' => $batch->batch_number,
                'product_name' => $batch->product->name,
                'product_sku' => $batch->product->sku,
                'warehouse' => $batch->warehouse->name,
                'quantity_remaining' => $batch->quantity_remaining,
                'unit' => $batch->product->baseUnit->short_name,
                'unit_cost' => $batch->unit_cost,
                'expiry_date' => $batch->expiry_date?->format('Y-m-d'),
                'days_until_expiry' => $batch->daysUntilExpiry(),
                'status' => $batch->status,
            ],
        ];
    }
    
    /**
     * طباعة ملصقات QR Code
     */
    public function generateLabels($productIds, $options = [])
    {
        $products = Product::whereIn('id', $productIds)->get();
        
        $labels = [];
        
        foreach ($products as $product) {
            if (!$product->qr_code) {
                $this->generateForProduct($product->id);
                $product->refresh();
            }
            
            $labels[] = [
                'product' => $product,
                'qr_code_url' => Storage::disk('public')->url($product->qr_code),
                'include_name' => $options['include_name'] ?? true,
                'include_sku' => $options['include_sku'] ?? true,
                'include_barcode' => $options['include_barcode'] ?? false,
            ];
        }
        
        return $labels;
    }
}