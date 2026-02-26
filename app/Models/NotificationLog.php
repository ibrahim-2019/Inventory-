<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'channels_sent',
        'status',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'channels_sent' => 'array', // ['email', 'whatsapp', 'sms', 'in_app']
        'status' => 'array', // ['email' => 'success', 'whatsapp' => 'failed']
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper Methods
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function wasSuccessful($channel = null): bool
    {
        if ($channel) {
            return isset($this->status[$channel]) && $this->status[$channel] === 'success';
        }
        
        // Check if any channel was successful
        return in_array('success', $this->status ?? []);
    }

    public function hasFailed($channel = null): bool
    {
        if ($channel) {
            return isset($this->status[$channel]) && $this->status[$channel] === 'failed';
        }
        
        // Check if all channels failed
        $statuses = $this->status ?? [];
        return !empty($statuses) && !in_array('success', $statuses);
    }

    public function getChannelStatus($channel): ?string
    {
        return $this->status[$channel] ?? null;
    }

    public function getShortMessageAttribute(): string
    {
        return strlen($this->message) > 100 
            ? substr($this->message, 0, 100) . '...' 
            : $this->message;
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}