<?php

// Test getConversationPartners query
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== TESTING GET CONVERSATION PARTNERS FOR ADMIN ===\n\n";

$adminId = 1;
$adminType = 'App\\Models\\Admin';

echo "Admin ID: {$adminId}\n";
echo "Admin Type: {$adminType}\n\n";

// Enable query logging
DB::enableQueryLog();

// Run the query from getConversationPartners
$userIds = Message::where(function ($query) use ($adminId, $adminType) {
    $query->where([
        ['sender_id', '=', $adminId],
        ['sender_type', '=', $adminType],
    ])->orWhere([
        ['receiver_id', '=', $adminId],
        ['receiver_type', '=', $adminType],
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

$queries = DB::getQueryLog();
echo "SQL Query:\n";
echo $queries[0]['query'] . "\n\n";
echo "Bindings:\n";
print_r($queries[0]['bindings']);

echo "\n\nUser IDs found: ";
print_r($userIds->toArray());

if ($userIds->count() > 0) {
    echo "\n\nFetching user details...\n";
    $users = User::whereIn('user_id', $userIds)->get();
    echo "Users found: " . $users->count() . "\n";
    foreach ($users as $user) {
        echo "  - User ID {$user->user_id}: {$user->first_name} {$user->last_name}\n";
    }
} else {
    echo "\n\nNO USERS FOUND!\n";
    echo "This means the query is not finding the message.\n\n";
    
    echo "Let's check the message directly:\n";
    $msg = Message::first();
    if ($msg) {
        echo "Message exists:\n";
        echo "  ID: {$msg->id}\n";
        echo "  From: {$msg->sender_type} (ID: {$msg->sender_id})\n";
        echo "  To: {$msg->receiver_type} (ID: {$msg->receiver_id})\n";
        echo "  Message: {$msg->message}\n";
    }
}
