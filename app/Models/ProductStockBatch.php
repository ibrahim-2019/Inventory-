<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductStockBatch extends Model
{
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'batch_number',
        'supplier_name',
        'quantity_in',
        'quantity_remaining',
        'quantity_used',
        'unit_cost',
        'total_cost',
        'purchase_date',
        'expiry_date',
        'manufacture_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity_in' => 'decimal:2',
        'quantity_remaining' => 'decimal:2',
        'quantity_used' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'purchase_date' => 'date',
        'expiry_date' => 'date',
        'manufacture_date' => 'date',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function movementBatches(): HasMany
    {
        return $this->hasMany(StockMovementBatch::class, 'batch_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('quantity_remaining', '>', 0);
    }

    public function scopeFifoOrder($query)
    {
        return $query->orderBy('purchase_date', 'asc')
                     ->orderBy('id', 'asc');
    }

    public function scopeFefoOrder($query)
    {
        return $query->orderByRaw('
            CASE 
                WHEN expiry_date IS NULL THEN 2
                ELSE 1
            END,
            expiry_date ASC,
            purchase_date ASC
        ');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                     ->where('expiry_date', '<=', now()->addDays($days))
                     ->where('expiry_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                     ->where('expiry_date', '<=', now());
    }

    // Helper Methods
    public function isExhausted(): bool
    {
        return $this->quantity_remaining <= 0;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon($days = 30): bool
    {
        return $this->expiry_date && 
               $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInDays(now()) <= $days;
    }

    public function daysUntilExpiry(): ?int
    {
        return $this->expiry_date ? now()->diffInDays($this->expiry_date, false) : null;
    }
}