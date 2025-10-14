<?php

namespace App\Http\Controllers;

use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    protected MessageService $service;

    public function __construct(MessageService $service)
    {
        $this->service = $service;
    }

    /**
     * Get unread message count
     */
    public function unreadCount(Request $request)
    {
        try {
            $accountId = Session::get('account_id');
            $accountRole = Session::get('account_role', 'user');

            if (!$accountId) {
                return response()->json(['count' => 0]);
            }

            $receiverType = $accountRole === 'admin' ? 'App\\Models\\Admin' : 'App\\Models\\User';
            $count = $this->service->getUnreadCount($accountId, $receiverType);

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            Log::error('Failed to get unread message count: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Get conversation messages - SECURITY: Only show messages for current user
     */
    public function getConversation(Request $request)
    {
        try {
            $accountId = Session::get('account_id');
            $accountRole = Session::get('account_role', 'user');

            if (!$accountId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $userType = $accountRole === 'admin' ? 'App\\Models\\Admin' : 'App\\Models\\User';
            
            // SECURITY: Enforce role-based access
            // For users, they can ONLY chat with admin
            // For admin, they can ONLY chat with the specific user_id provided
            if ($accountRole === 'user') {
                // Users can ONLY see their own messages with admin
                $partnerId = 1; // Default admin ID
                $partnerType = 'App\\Models\\Admin';
                
                // SECURITY CHECK: Verify we're getting messages for THIS user only
                Log::info("User {$accountId} loading conversation with Admin");
            } else {
                // Admin must specify which user's conversation to view
                $validator = Validator::make($request->all(), [
                    'user_id' => 'required|integer|exists:user,user_id',
                ]);

                if ($validator->fails()) {
                    return response()->json(['error' => 'Invalid user ID'], 400);
                }

                $partnerId = $request->input('user_id');
                $partnerType = 'App\\Models\\User';
                
                // SECURITY CHECK: Admin can only view this specific user's messages
                Log::info("Admin {$accountId} loading conversation with User {$partnerId}");
            }

            // Get messages - this will ONLY return messages between current user and partner
            $messages = $this->service->getConversation($accountId, $userType, $partnerId, $partnerType);

            // DEBUG: Log what we're searching for and what we found
            Log::info('Conversation Query Details', [
                'current_user_id' => $accountId,
                'current_user_type' => $userType,
                'partner_id' => $partnerId,
                'partner_type' => $partnerType,
                'messages_found' => $messages->count(),
                'messages' => $messages->map(function($m) {
                    return [
                        'id' => $m->id,
                        'from' => $m->sender_type . ':' . $m->sender_id,
                        'to' => $m->receiver_type . ':' . $m->receiver_id,
                        'text' => substr($m->message, 0, 30)
                    ];
                })
            ]);

            // Mark messages as read (only messages sent TO this user)
            $this->service->markConversationAsRead($accountId, $userType, $partnerId, $partnerType);

            // Return with clear role identification
            // FIX: Ensure messages is always an array, not an object
            return response()->json([
                'success' => true,
                'messages' => $messages->values()->toArray(), // Force to array to prevent empty object {}
                'current_user_id' => $accountId,
                'current_user_type' => $accountRole,
                'current_user_type_full' => $userType,
                'partner_id' => $partnerId,
                'partner_type_full' => $partnerType,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get conversation: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Failed to load messages', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request)
    {
        try {
            $accountId = Session::get('account_id');
            $accountRole = Session::get('account_role', 'user');

            if (!$accountId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $validator = Validator::make($request->all(), [
                'message' => 'required|string|max:2000',
                'receiver_id' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $message = $request->input('message');
            $senderType = $accountRole === 'admin' ? 'App\\Models\\Admin' : 'App\\Models\\User';

            // Determine receiver
            if ($accountRole === 'user') {
                $receiverId = 1; // Default admin ID
                $receiverType = 'App\\Models\\Admin';
            } else {
                $receiverId = $request->input('receiver_id');
                if (!$receiverId) {
                    return response()->json(['error' => 'Receiver ID required for admin'], 400);
                }
                $receiverType = 'App\\Models\\User';
            }

            $newMessage = $this->service->sendMessage($accountId, $senderType, $receiverId, $receiverType, $message);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $newMessage,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send message: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }

    /**
     * Get conversation partners (for admin to see list of users)
     */
    public function getPartners(Request $request)
    {
        try {
            $accountId = Session::get('account_id');
            $accountRole = Session::get('account_role', 'user');

            if (!$accountId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if ($accountRole !== 'admin') {
                return response()->json(['error' => 'Only admin can view conversation partners'], 403);
            }

            $userType = 'App\\Models\\Admin';

            // Allow searching users via 'q' param (admin can search any user)
            $q = $request->query('q');
            if ($q) {
                $found = $this->service->searchUsers($q);

                $partnersWithUnread = $found->map(function ($partner) use ($accountId, $userType) {
                    $unreadCount = \App\Models\Message::where([
                        ['receiver_id', '=', $accountId],
                        ['receiver_type', '=', $userType],
                        ['sender_id', '=', $partner->user_id],
                        ['sender_type', '=', 'App\\Models\\User'],
                        ['is_read', '=', false],
                    ])->count();

                    return [
                        'user_id' => $partner->user_id,
                        'name' => "{$partner->first_name} {$partner->last_name}",
                        'unread_count' => $unreadCount,
                    ];
                });

                return response()->json(['partners' => $partnersWithUnread]);
            }

            // No search - return users that have conversations with this admin
            $partners = $this->service->getConversationPartners($accountId, $userType);

            // Get unread count for each partner
            $partnersWithUnread = $partners->map(function ($partner) use ($accountId, $userType) {
                $unreadCount = \App\Models\Message::where([
                    ['receiver_id', '=', $accountId],
                    ['receiver_type', '=', $userType],
                    ['sender_id', '=', $partner->user_id],
                    ['sender_type', '=', 'App\\Models\\User'],
                    ['is_read', '=', false],
                ])->count();

                return [
                    'user_id' => $partner->user_id,
                    'name' => "{$partner->first_name} {$partner->last_name}",
                    'unread_count' => $unreadCount,
                ];
            });

            return response()->json(['partners' => $partnersWithUnread]);
        } catch (\Exception $e) {
            Log::error('Failed to get conversation partners: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load partners'], 500);
        }
    }

    /**
     * Delete a single message
     */
    public function deleteMessage($id)
    {
        try {
            $accountId = Session::get('account_id');
            $accountRole = Session::get('account_role', 'user');

            if (!$accountId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $userType = $accountRole === 'admin' ? 'App\\Models\\Admin' : 'App\\Models\\User';

            $result = $this->service->deleteMessage($id, $accountId, $userType);

            if (!$result) {
                return response()->json(['error' => 'Message not found or unauthorized'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Message deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to delete message: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete message'], 500);
        }
    }

    /**
     * Delete entire conversation with a partner
     */
    public function deleteConversation($partnerId)
    {
        try {
            $accountId = Session::get('account_id');
            $accountRole = Session::get('account_role', 'user');

            if (!$accountId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $userType = $accountRole === 'admin' ? 'App\\Models\\Admin' : 'App\\Models\\User';
            
            // Determine partner type based on role
            if ($accountRole === 'user') {
                // User can only delete conversation with admin
                $partnerType = 'App\\Models\\Admin';
            } else {
                // Admin deleting conversation with a user
                $partnerType = 'App\\Models\\User';
            }

            $deletedCount = $this->service->deleteConversation($accountId, $userType, $partnerId, $partnerType);

            return response()->json([
                'success' => true, 
                'message' => 'Conversation deleted successfully',
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete conversation: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete conversation'], 500);
        }
    }
}
