<?php

// Test conversation for Maria Database (user_id 3)
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Message;

echo "=== TESTING CONVERSATION WITH MARIA DATABASE ===\n\n";

$adminId = 1;
$adminType = 'App\\Models\\Admin';
$userId = 3; // Maria Database
$userType = 'App\\Models\\User';

echo "Looking for conversation between:\n";
echo "Admin ID: {$adminId} (type: {$adminType})\n";
echo "User ID: {$userId} (Maria Database) (type: {$userType})\n\n";

$messages = Message::conversation($adminId, $adminType, $userId, $userType)->get();

echo "Messages found: " . $messages->count() . "\n\n";

if ($messages->count() > 0) {
    echo "--- MESSAGES ---\n";
    foreach ($messages as $msg) {
        echo "ID: {$msg->id}\n";
        echo "From: {$msg->sender_type} (ID: {$msg->sender_id})\n";
        echo "To: {$msg->receiver_type} (ID: {$msg->receiver_id})\n";
        echo "Message: {$msg->message}\n";
        echo "---\n";
    }
} else {
    echo "NO MESSAGES FOUND!\n";
}
