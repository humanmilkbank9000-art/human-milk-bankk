<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SystemAlert extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $type;
    protected $meta;

    public function __construct($title, $message, $type = 'info', $meta = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->meta = $meta;
    }

    // Persist to database and broadcast for real-time updates
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    // The array stored in the database `notifications` table
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'meta' => $this->meta
        ];
    }

    // The broadcast payload sent over channels (used by Echo on the client)
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'meta' => $this->meta,
            'created_at' => now()->toDateTimeString(),
        ]);
    }
}
