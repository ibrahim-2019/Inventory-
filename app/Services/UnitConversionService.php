<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductUnitConversion;
use Exception;

class UnitConversionService
{
    /**
     * إضافة تحويل وحدة لمنتج
     */
    public function addConversion(array $data)
    {
        $product = Product::findOrFail($data['product_id']);
        
        // التحقق من عدم تكرار الوحدة
        $exists = ProductUnitConversion::where('product_id', $data['product_id'])
            ->where('unit_id', $data['unit_id'])
            ->exists();
        
        if ($exists) {
            throw new Exception("هذه الوحدة موجودة بالفعل لهذا المنتج");
        }
        
        return ProductUnitConversion::create([
            'product_id' => $data['product_id'],
            'unit_id' => $data['unit_id'],
            'base_unit_id' => $product->base_unit_id,
            'conversion_factor' => $data['conversion_factor'],
            'is_purchase_unit' => $data['is_purchase_unit'] ?? false,
            'is_sale_unit' => $data['is_sale_unit'] ?? false,
            'barcode' => $data['barcode'] ?? null,
            'price' => $data['price'] ?? null,
        ]);
    }
    
    /**
     * تحويل من وحدة لوحدة
     */
    public function convert($productId, $quantity, $fromUnitId, $toUnitId)
    {
        $product = Product::findOrFail($productId);
        
        // 1. تحويل للوحدة الأساسية
        $quantityInBase = $this->convertToBase($product, $quantity, $fromUnitId);
        
        // 2. تحويل من الوحدة الأساسية للوحدة المطلوبة
        $result = $this->convertFromBase($product, $quantityInBase, $toUnitId);
        
        return $result;
    }
    
    /**
     * تحويل للوحدة الأساسية
     */
    public function convertToBase($product, $quantity, $unitId)
    {
        if ($unitId == $product->base_unit_id) {
            return $quantity;
        }
        
        $conversion = $product->unitConversions()
            ->where('unit_id', $unitId)
            ->first();
        
        if (!$conversion) {
            throw new Exception("معامل التحويل غير موجود");
        }
        
        return $quantity * $conversion->conversion_factor;
    }
    
    /**
     * تحويل من الوحدة الأساسية
     */
    public function convertFromBase($product, $quantityInBase, $unitId)
    {
        if ($unitId == $product->base_unit_id) {
            return $quantityInBase;
        }
        
        $conversion = $product->unitConversions()
            ->where('unit_id', $unitId)
            ->first();
        
        if (!$conversion) {
            throw new Exception("معامل التحويل غير موجود");
        }
        
        return $quantityInBase / $conversion->conversion_factor;
    }
    
    /**
     * عرض الكمية بوحدات مختلفة
     */
    public function displayInMultipleUnits($product, $quantityInBase)
    {
        $conversions = $product->unitConversions()->with('unit')->get();
        
        $result = [];
        
        // الوحدة الأساسية
        $result[] = [
            'unit' => $product->baseUnit,
            'quantity' => $quantityInBase,
            'display' => number_format($quantityInBase, 2) . ' ' . $product->baseUnit->short_name,
        ];
        
        // الوحدات الأخرى
        foreach ($conversions as $conversion) {
            $qty = $quantityInBase / $conversion->conversion_factor;
            $result[] = [
                'unit' => $conversion->unit,
                'quantity' => $qty,
                'display' => number_format($qty, 2) . ' ' . $conversion->unit->short_name,
            ];
        }
        
        return $result;
    }
}