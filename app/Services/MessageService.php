<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class MessageService
{
    /**
     * Send a message
     */
    public function sendMessage(int $senderId, string $senderType, int $receiverId, string $receiverType, string $message): Message
    {
        // Sanitize message
        $sanitizedMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        return Message::create([
            'sender_id' => $senderId,
            'sender_type' => $senderType,
            'receiver_id' => $receiverId,
            'receiver_type' => $receiverType,
            'message' => $sanitizedMessage,
            'is_read' => false,
        ]);
    }

    /**
     * Get conversation between two parties
     */
    public function getConversation(int $userId, string $userType, int $partnerId, string $partnerType, int $limit = 50): Collection
    {
        return Message::conversation($userId, $userType, $partnerId, $partnerType)
            ->oldest('created_at')
            ->limit($limit)
            ->get()
            ->values();
    }

    /**
     * Get unread message count for a receiver
     */
    public function getUnreadCount(int $receiverId, string $receiverType): int
    {
        return Message::unreadFor($receiverId, $receiverType)->count();
    }

    /**
     * Mark all messages from a specific sender as read
     */
    public function markConversationAsRead(int $receiverId, string $receiverType, int $senderId, string $senderType): int
    {
        return Message::where([
            ['receiver_id', '=', $receiverId],
            ['receiver_type', '=', $receiverType],
            ['sender_id', '=', $senderId],
            ['sender_type', '=', $senderType],
            ['is_read', '=', false],
        ])->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark a single message as read
     */
    public function markAsRead(int $messageId, int $receiverId, string $receiverType): bool
    {
        $message = Message::where([
            ['id', '=', $messageId],
            ['receiver_id', '=', $receiverId],
            ['receiver_type', '=', $receiverType],
        ])->first();

        if ($message && !$message->is_read) {
            $message->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Get all users who have conversations with the given user/admin
     * For admin: get all users who have sent messages
     * For user: get admin only
     */
    public function getConversationPartners(int $userId, string $userType): Collection
    {
        if ($userType === 'App\\Models\\Admin') {
            // Admin: get all users who have exchanged messages with this admin
            $userIds = Message::where(function ($query) use ($userId, $userType) {
                $query->where([
                    ['sender_id', '=', $userId],
                    ['sender_type', '=', $userType],
                ])->orWhere([
                    ['receiver_id', '=', $userId],
                    ['receiver_type', '=', $userType],
                ]);
            })
            ->where(function ($query) {
                $query->where('sender_type', 'App\\Models\\User')
                      ->orWhere('receiver_type', 'App\\Models\\User');
            })
            ->select(DB::raw('CASE 
                WHEN sender_type = "App\\\\Models\\\\User" THEN sender_id 
                ELSE receiver_id 
            END as user_id'))
            ->distinct()
            ->pluck('user_id');

            return User::whereIn('user_id', $userIds)->get();
        } else {
            // User: only chat with admin (return admin info)
            return Admin::limit(1)->get();
        }
    }

    /**
     * Get the last message in a conversation
     */
    public function getLastMessage(int $userId, string $userType, int $partnerId, string $partnerType): ?Message
    {
        return Message::conversation($userId, $userType, $partnerId, $partnerType)
            ->latest('created_at')
            ->first();
    }
}
