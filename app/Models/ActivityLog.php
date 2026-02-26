<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Polymorphic relationship to the logged model
    public function model()
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForModel($query, $modelType, $modelId = null)
    {
        $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper Methods
    public function getChangesAttribute(): array
    {
        $changes = [];
        
        foreach ($this->new_values ?? [] as $key => $newValue) {
            $oldValue = $this->old_values[$key] ?? null;
            
            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }
        
        return $changes;
    }

    public function getDescriptionAttribute(): string
    {
        $userName = $this->user ? $this->user->name : 'Unknown User';
        $modelName = class_basename($this->model_type);
        
        return match($this->action) {
            'created' => "{$userName} created {$modelName} #{$this->model_id}",
            'updated' => "{$userName} updated {$modelName} #{$this->model_id}",
            'deleted' => "{$userName} deleted {$modelName} #{$this->model_id}",
            default => "{$userName} performed {$this->action} on {$modelName} #{$this->model_id}",
        };
    }
}