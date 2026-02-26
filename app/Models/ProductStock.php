<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStock extends Model
{
    protected $table = 'product_stock';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'total_quantity',
        'reserved_quantity',
        'total_cost',
        'average_cost',
        'last_updated',
    ];

    protected $casts = [
        'total_quantity' => 'decimal:2',
        'reserved_quantity' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'average_cost' => 'decimal:2',
        'last_updated' => 'datetime',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Accessors
    public function getAvailableQuantityAttribute()
    {
        return $this->total_quantity - $this->reserved_quantity;
    }

    // Helper Methods
    public function isLowStock(): bool
    {
        return $this->available_quantity <= $this->product->alert_quantity;
    }
}