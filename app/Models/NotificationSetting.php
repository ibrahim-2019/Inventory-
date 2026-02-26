<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'notification_type',
        'channels',
        'is_active',
    ];

    protected $casts = [
        'channels' => 'array', // ['email', 'whatsapp', 'sms', 'in_app']
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('notification_type', $type);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helper Methods
    public function hasChannel($channel): bool
    {
        return in_array($channel, $this->channels);
    }

    public function addChannel($channel): void
    {
        $channels = $this->channels;
        if (!in_array($channel, $channels)) {
            $channels[] = $channel;
            $this->channels = $channels;
            $this->save();
        }
    }

    public function removeChannel($channel): void
    {
        $channels = array_filter($this->channels, fn($ch) => $ch !== $channel);
        $this->channels = array_values($channels);
        $this->save();
    }

    public function getChannelsListAttribute(): string
    {
        return implode(', ', $this->channels);
    }
}