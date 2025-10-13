<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Admin;

class NotificationService
{
    public function resolveNotifiable()
    {
        if (!Session::has('account_id')) {
            return null;
        }

        $id = Session::get('account_id');
        $role = Session::get('account_role', 'user');

        if ($role === 'admin') {
            return Admin::where('admin_id', $id)->first();
        }

        return User::where('user_id', $id)->first();
    }

    public function paginateNotifications($notifiable, int $perPage = 10)
    {
        $notifications = $notifiable->notifications()->orderBy('created_at', 'desc')->paginate($perPage);

        $notifications->getCollection()->transform(function ($n) {
            return [
                'id' => $n->id,
                'data' => $n->data,
                'title' => $n->data['title'] ?? null,
                'message' => $n->data['message'] ?? null,
                'type' => $n->data['type'] ?? 'info',
                'is_read' => (bool)$n->read_at,
                'created_at' => $n->created_at->toDateTimeString(),
            ];
        });

        return $notifications;
    }

    public function unreadCount($notifiable)
    {
        return $notifiable->unreadNotifications()->count();
    }

    public function markAsRead($notifiable, $id)
    {
        $notification = $notifiable->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();
        return true;
    }

    public function markAllAsRead($notifiable)
    {
        $notifiable->unreadNotifications->markAsRead();
        return true;
    }
}
