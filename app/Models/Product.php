<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sku',
        'barcode',
        'qr_code',
        'name',
        'slug',
        'description',
        'category_id',
        'brand_id',
        'base_unit_id',
        'cost_price',
        'selling_price',
        'tax_percentage',
        'track_batches',
        'has_expiry_date',
        'withdrawal_strategy',
        'alert_quantity',
        'expiry_alert_days',
        'auto_block_expired',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'track_batches' => 'boolean',
        'has_expiry_date' => 'boolean',
        'auto_block_expired' => 'boolean',
        'is_active' => 'boolean',
        'alert_quantity' => 'integer',
        'expiry_alert_days' => 'integer',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function unitConversions(): HasMany
    {
        return $this->hasMany(ProductUnitConversion::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ProductStockBatch::class);
    }

    public function stock(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithExpiry($query)
    {
        return $query->where('has_expiry_date', true);
    }

    // Helper Methods
    public function primaryImage()
    {
        return $this->images()->where('is_primary', true)->first();
    }

    public function getStockInWarehouse($warehouseId)
    {
        return $this->stock()->where('warehouse_id', $warehouseId)->first();
    }
}