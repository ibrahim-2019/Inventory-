<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUnitConversion extends Model
{
    protected $fillable = [
        'product_id',
        'unit_id',
        'base_unit_id',
        'conversion_factor',
        'is_purchase_unit',
        'is_sale_unit',
        'barcode',
        'price',
    ];

    protected $casts = [
        'conversion_factor' => 'decimal:3',
        'is_purchase_unit' => 'boolean',
        'is_sale_unit' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    // Helper Methods
    public function convertToBase($quantity)
    {
        return $quantity * $this->conversion_factor;
    }

    public function convertFromBase($quantity)
    {
        return $quantity / $this->conversion_factor;
    }
}