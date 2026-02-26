<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'movement_type',
        'quantity',
        'total_cost',
        'average_unit_cost',
        'reference_type',
        'reference_id',
        'reference_number',
        'reason',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'average_unit_cost' => 'decimal:2',
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

    public function batches(): HasMany
    {
        return $this->hasMany(StockMovementBatch::class);
    }

    // Scopes
    public function scopeIn($query)
    {
        return $query->where('movement_type', 'in');
    }

    public function scopeOut($query)
    {
        return $query->where('movement_type', 'out');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('movement_type', $type);
    }
}