<?php

// Test script to check messages in database
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Message;
use Illuminate\Support\Facades\DB;

echo "=== CHECKING MESSAGES TABLE ===\n\n";

// Get all messages
$allMessages = Message::orderBy('created_at', 'desc')->get();

echo "Total messages in database: " . $allMessages->count() . "\n\n";

if ($allMessages->count() > 0) {
    echo "--- ALL MESSAGES ---\n";
    foreach ($allMessages as $msg) {
        echo "ID: {$msg->id}\n";
        echo "Sender: {$msg->sender_type} (ID: {$msg->sender_id})\n";
        echo "Receiver: {$msg->receiver_type} (ID: {$msg->receiver_id})\n";
        echo "Message: " . substr($msg->message, 0, 50) . "...\n";
        echo "Created: {$msg->created_at}\n";
        echo "Read: " . ($msg->is_read ? 'Yes' : 'No') . "\n";
        echo "---\n";
    }
    
    echo "\n=== CHECKING SPECIFIC USER CONVERSATIONS ===\n\n";
    
    // Get list of users
    $users = DB::table('users')->select('user_id', 'first_name', 'last_name')->get();
    
    foreach ($users as $user) {
        $userName = trim($user->first_name . ' ' . $user->last_name);
        echo "User: {$userName} (ID: {$user->user_id})\n";
        
        // Count messages for this user
        $userMessages = Message::where(function($query) use ($user) {
            $query->where(function($q) use ($user) {
                // Messages FROM user TO admin
                $q->where('sender_id', $user->user_id)
                  ->where('sender_type', 'App\\Models\\User')
                  ->where('receiver_type', 'App\\Models\\Admin');
            })->orWhere(function($q) use ($user) {
                // Messages FROM admin TO user
                $q->where('receiver_id', $user->user_id)
                  ->where('receiver_type', 'App\\Models\\User')
                  ->where('sender_type', 'App\\Models\\Admin');
            });
        })->get();
        
        echo "  Messages: {$userMessages->count()}\n";
        
        if ($userMessages->count() > 0) {
            foreach ($userMessages as $msg) {
                $direction = $msg->sender_type === 'App\\Models\\User' ? 'User → Admin' : 'Admin → User';
                echo "    - [{$direction}] " . substr($msg->message, 0, 40) . "...\n";
            }
        }
        echo "\n";
    }
} else {
    echo "No messages found in database.\n";
}

echo "\n=== CHECKING ADMINS ===\n\n";
$admins = DB::table('admins')->select('admin_id', 'username')->get();
echo "Total admins: {$admins->count()}\n";
foreach ($admins as $admin) {
    echo "Admin ID: {$admin->admin_id}, Username: {$admin->username}\n";
}
