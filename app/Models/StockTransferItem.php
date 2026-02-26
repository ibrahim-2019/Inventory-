<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferItem extends Model
{
    protected $fillable = [
        'stock_transfer_id',
        'product_id',
        'quantity',
        'quantity_received',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'quantity_received' => 'decimal:2',
    ];

    // Relationships
    public function transfer(): BelongsTo
    {
        return $this->belongsTo(StockTransfer::class, 'stock_transfer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Helper Methods
    public function isFullyReceived(): bool
    {
        return $this->quantity_received >= $this->quantity;
    }

    public function getPendingQuantityAttribute(): float
    {
        return max(0, $this->quantity - $this->quantity_received);
    }

    public function getReceivedPercentageAttribute(): float
    {
        return $this->quantity > 0 
            ? ($this->quantity_received / $this->quantity) * 100 
            : 0;
    }
}