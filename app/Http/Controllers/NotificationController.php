<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Admin;
use Carbon\Carbon;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    protected NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    // Fetch paginated notifications for the current logged-in entity
    public function index(Request $request)
    {
        $notifiable = $this->service->resolveNotifiable();
        if (!$notifiable) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $perPage = (int)$request->get('per_page', 10);

        $notifications = $this->service->paginateNotifications($notifiable, $perPage);
        return response()->json($notifications);
    }

    public function unreadCount()
    {
        $notifiable = $this->service->resolveNotifiable();
        if (!$notifiable) {
            return response()->json(['count' => 0]);
        }
        return response()->json(['count' => $this->service->unreadCount($notifiable)]);
    }

    public function markAsRead($id)
    {
        $notifiable = $this->service->resolveNotifiable();
        if (!$notifiable) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $this->service->markAsRead($notifiable, $id);
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $notifiable = $this->service->resolveNotifiable();
        if (!$notifiable) {
            return response()->json(['error' => 'Unauthorized'], status: 401);
        }

        $this->service->markAllAsRead($notifiable);

        return response()->json(['success' => true]);
    }
}
