<?php

// Test the conversation scope query
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Message;

echo "=== TESTING CONVERSATION SCOPE ===\n\n";

// Simulate admin (ID 1) viewing conversation with user (ID 1)
$adminId = 1;
$adminType = 'App\\Models\\Admin';
$userId = 1;
$userType = 'App\\Models\\User';

echo "Looking for conversation between:\n";
echo "Admin ID: {$adminId} (type: {$adminType})\n";
echo "User ID: {$userId} (type: {$userType})\n\n";

// Use the conversation scope
$messages = Message::conversation($adminId, $adminType, $userId, $userType)->get();

echo "Messages found: " . $messages->count() . "\n\n";

if ($messages->count() > 0) {
    echo "--- MESSAGES ---\n";
    foreach ($messages as $msg) {
        echo "ID: {$msg->id}\n";
        echo "From: {$msg->sender_type} (ID: {$msg->sender_id})\n";
        echo "To: {$msg->receiver_type} (ID: {$msg->receiver_id})\n";
        echo "Message: {$msg->message}\n";
        echo "Created: {$msg->created_at}\n";
        echo "---\n";
    }
} else {
    echo "NO MESSAGES FOUND!\n\n";
    
    echo "Let's check what the query is looking for:\n";
    echo "Query 1: sender_id={$adminId}, sender_type={$adminType}, receiver_id={$userId}, receiver_type={$userType}\n";
    echo "OR\n";
    echo "Query 2: sender_id={$userId}, sender_type={$userType}, receiver_id={$adminId}, receiver_type={$adminType}\n\n";
    
    echo "Checking database manually:\n";
    $directQuery = Message::where([
        ['sender_id', '=', $userId],
        ['sender_type', '=', $userType],
        ['receiver_id', '=', $adminId],
        ['receiver_type', '=', $adminType],
    ])->get();
    
    echo "Direct query (User to Admin) found: " . $directQuery->count() . " messages\n";
    
    if ($directQuery->count() > 0) {
        foreach ($directQuery as $msg) {
            echo "  Message ID {$msg->id}: sender_type='{$msg->sender_type}', receiver_type='{$msg->receiver_type}'\n";
        }
    }
}
