<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'variant_name',
        'variant_value',
        'sku',
        'barcode',
        'additional_price',
        'is_active',
    ];

    protected $casts = [
        'additional_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods
    public function getFullNameAttribute(): string
    {
        return "{$this->product->name} - {$this->variant_name}: {$this->variant_value}";
    }

    public function getFinalPriceAttribute(): float
    {
        return ($this->product->selling_price ?? 0) + $this->additional_price;
    }
}