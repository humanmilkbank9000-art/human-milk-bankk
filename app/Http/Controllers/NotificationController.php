<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Admin;
use Carbon\Carbon;

class NotificationController extends Controller
{
    protected function resolveNotifiable()
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

    // Fetch paginated notifications for the current logged-in entity
    public function index(Request $request)
    {
        $notifiable = $this->resolveNotifiable();
        if (!$notifiable) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $perPage = (int)$request->get('per_page', 10);

        $notifications = $notifiable->notifications()->orderBy('created_at', 'desc')->paginate($perPage);

        // map to include some convenience fields
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

        return response()->json($notifications);
    }

    public function unreadCount()
    {
        $notifiable = $this->resolveNotifiable();
        if (!$notifiable) {
            return response()->json(['count' => 0]);
        }

        $count = $notifiable->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }

    public function markAsRead($id)
    {
        $notifiable = $this->resolveNotifiable();
        if (!$notifiable) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notification = $notifiable->notifications()->where('id', $id)->firstOrFail();

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $notifiable = $this->resolveNotifiable();
        if (!$notifiable) {
            return response()->json(['error' => 'Unauthorized'], status: 401);
        }

        $notifiable->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
