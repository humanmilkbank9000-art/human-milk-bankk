<?php

// Test what Cris John (user_id 1) should see
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Message;

echo "=== TESTING CRIS JOHN'S CONVERSATION (User ID 1) ===\n\n";

// When Cris John (user_id 1) logs in and opens chat with admin
$crisJohnId = 1;
$crisJohnType = 'App\\Models\\User';
$adminId = 1;
$adminType = 'App\\Models\\Admin';

echo "Cris John (User ID {$crisJohnId}) conversation with Admin:\n\n";

$crisMessages = Message::conversation($crisJohnId, $crisJohnType, $adminId, $adminType)->get();

echo "Messages found: " . $crisMessages->count() . "\n\n";

if ($crisMessages->count() > 0) {
    foreach ($crisMessages as $msg) {
        echo "ID: {$msg->id}\n";
        echo "From: {$msg->sender_type} (ID: {$msg->sender_id})\n";
        echo "To: {$msg->receiver_type} (ID: {$msg->receiver_id})\n";
        echo "Message: {$msg->message}\n";
        echo "---\n";
    }
} else {
    echo "NO MESSAGES\n";
}

echo "\n=== TESTING MARIA'S CONVERSATION (User ID 3) ===\n\n";

// When Maria (user_id 3) logs in and opens chat with admin
$mariaId = 3;
$mariaType = 'App\\Models\\User';

echo "Maria Database (User ID {$mariaId}) conversation with Admin:\n\n";

$mariaMessages = Message::conversation($mariaId, $mariaType, $adminId, $adminType)->get();

echo "Messages found: " . $mariaMessages->count() . "\n\n";

if ($mariaMessages->count() > 0) {
    foreach ($mariaMessages as $msg) {
        echo "ID: {$msg->id}\n";
        echo "From: {$msg->sender_type} (ID: {$msg->sender_id})\n";
        echo "To: {$msg->receiver_type} (ID: {$msg->receiver_id})\n";
        echo "Message: {$msg->message}\n";
        echo "---\n";
    }
} else {
    echo "NO MESSAGES\n";
}

echo "\n=== ALL MESSAGES IN DATABASE ===\n";
$allMessages = Message::all();
foreach ($allMessages as $msg) {
    echo "ID {$msg->id}: From {$msg->sender_type}:{$msg->sender_id} → To {$msg->receiver_type}:{$msg->receiver_id} | \"{$msg->message}\"\n";
}
