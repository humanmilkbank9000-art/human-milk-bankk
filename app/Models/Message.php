<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'sender_type',
        'receiver_id',
        'receiver_type',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the sender (polymorphic relation)
     */
    public function sender(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'sender_type', 'sender_id');
    }

    /**
     * Get the receiver (polymorphic relation)
     */
    public function receiver(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'receiver_type', 'receiver_id');
    }

    /**
     * Scope to get messages for a specific conversation between two parties
     */
    public function scopeConversation($query, $userId, $userType, $partnerId, $partnerType)
    {
        return $query->where(function ($q) use ($userId, $userType, $partnerId, $partnerType) {
            // SECURITY FIX: Use separate where clauses with proper AND logic
            $q->where(function ($q1) use ($userId, $userType, $partnerId, $partnerType) {
                // Messages FROM current user TO partner
                $q1->where('sender_id', '=', $userId)
                   ->where('sender_type', '=', $userType)
                   ->where('receiver_id', '=', $partnerId)
                   ->where('receiver_type', '=', $partnerType);
            })->orWhere(function ($q2) use ($userId, $userType, $partnerId, $partnerType) {
                // Messages FROM partner TO current user
                $q2->where('sender_id', '=', $partnerId)
                   ->where('sender_type', '=', $partnerType)
                   ->where('receiver_id', '=', $userId)
                   ->where('receiver_type', '=', $userType);
            });
        })->orderBy('created_at', 'asc');
    }

    /**
     * Scope to get unread messages for a receiver
     */
    public function scopeUnreadFor($query, $receiverId, $receiverType)
    {
        return $query->where([
            ['receiver_id', '=', $receiverId],
            ['receiver_type', '=', $receiverType],
            ['is_read', '=', false],
        ]);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Get formatted sender name
     */
    public function getSenderNameAttribute(): string
    {
        if ($this->sender_type === 'App\\Models\\User') {
            $sender = User::find($this->sender_id);
            return $sender ? "{$sender->first_name} {$sender->last_name}" : 'Unknown User';
        } elseif ($this->sender_type === 'App\\Models\\Admin') {
            return 'Admin';
        }
        return 'Unknown';
    }
}
