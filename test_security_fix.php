<?php

// Comprehensive test of the fix
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Message;
use Illuminate\Support\Facades\DB;

echo "=== SECURITY TEST: USER MESSAGE ISOLATION ===\n\n";

// Create test messages
echo "Setting up test data...\n";

// Clear existing messages
DB::table('messages')->truncate();

// Create messages from different users
$messages = [
    ['sender_id' => 1, 'sender_type' => 'App\\Models\\User', 'receiver_id' => 1, 'receiver_type' => 'App\\Models\\Admin', 'message' => 'Message from Cris John', 'is_read' => false],
    ['sender_id' => 3, 'sender_type' => 'App\\Models\\User', 'receiver_id' => 1, 'receiver_type' => 'App\\Models\\Admin', 'message' => 'Message from Maria', 'is_read' => false],
    ['sender_id' => 1, 'sender_type' => 'App\\Models\\Admin', 'receiver_id' => 1, 'receiver_type' => 'App\\Models\\User', 'message' => 'Admin reply to Cris John', 'is_read' => false],
    ['sender_id' => 1, 'sender_type' => 'App\\Models\\Admin', 'receiver_id' => 3, 'receiver_type' => 'App\\Models\\User', 'message' => 'Admin reply to Maria', 'is_read' => false],
];

foreach ($messages as $msg) {
    DB::table('messages')->insert(array_merge($msg, [
        'created_at' => now(),
        'updated_at' => now()
    ]));
}

echo "Created 4 test messages:\n";
echo "  1. Cris John → Admin: 'Message from Cris John'\n";
echo "  2. Maria → Admin: 'Message from Maria'\n";
echo "  3. Admin → Cris John: 'Admin reply to Cris John'\n";
echo "  4. Admin → Maria: 'Admin reply to Maria'\n\n";

// Test 1: Cris John's conversation (user_id 1)
echo "TEST 1: Cris John (user_id 1) logs in and opens chat\n";
echo "Should see: ONLY messages 1 and 3 (his conversation with admin)\n";
$crisMessages = Message::conversation(1, 'App\\Models\\User', 1, 'App\\Models\\Admin')->get();
echo "Result: Found " . $crisMessages->count() . " messages\n";
foreach ($crisMessages as $msg) {
    echo "  ✓ '{$msg->message}'\n";
}
$crisPass = $crisMessages->count() === 2 && 
            $crisMessages->where('message', 'Message from Cris John')->count() === 1 &&
            $crisMessages->where('message', 'Admin reply to Cris John')->count() === 1;
echo $crisPass ? "✅ PASS: Cris John sees only his messages\n\n" : "❌ FAIL: Cris John sees wrong messages!\n\n";

// Test 2: Maria's conversation (user_id 3)
echo "TEST 2: Maria (user_id 3) logs in and opens chat\n";
echo "Should see: ONLY messages 2 and 4 (her conversation with admin)\n";
$mariaMessages = Message::conversation(3, 'App\\Models\\User', 1, 'App\\Models\\Admin')->get();
echo "Result: Found " . $mariaMessages->count() . " messages\n";
foreach ($mariaMessages as $msg) {
    echo "  ✓ '{$msg->message}'\n";
}
$mariaPass = $mariaMessages->count() === 2 && 
             $mariaMessages->where('message', 'Message from Maria')->count() === 1 &&
             $mariaMessages->where('message', 'Admin reply to Maria')->count() === 1;
echo $mariaPass ? "✅ PASS: Maria sees only her messages\n\n" : "❌ FAIL: Maria sees wrong messages!\n\n";

// Test 3: Admin viewing Cris John's conversation
echo "TEST 3: Admin logs in and views Cris John's conversation\n";
echo "Should see: Messages 1 and 3\n";
$adminCrisMessages = Message::conversation(1, 'App\\Models\\Admin', 1, 'App\\Models\\User')->get();
echo "Result: Found " . $adminCrisMessages->count() . " messages\n";
foreach ($adminCrisMessages as $msg) {
    echo "  ✓ '{$msg->message}'\n";
}
$adminCrisPass = $adminCrisMessages->count() === 2;
echo $adminCrisPass ? "✅ PASS: Admin sees Cris John's conversation correctly\n\n" : "❌ FAIL: Admin sees wrong messages!\n\n";

// Test 4: Admin viewing Maria's conversation
echo "TEST 4: Admin logs in and views Maria's conversation\n";
echo "Should see: Messages 2 and 4\n";
$adminMariaMessages = Message::conversation(1, 'App\\Models\\Admin', 3, 'App\\Models\\User')->get();
echo "Result: Found " . $adminMariaMessages->count() . " messages\n";
foreach ($adminMariaMessages as $msg) {
    echo "  ✓ '{$msg->message}'\n";
}
$adminMariaPass = $adminMariaMessages->count() === 2;
echo $adminMariaPass ? "✅ PASS: Admin sees Maria's conversation correctly\n\n" : "❌ FAIL: Admin sees wrong messages!\n\n";

// Summary
echo "\n=== SECURITY TEST RESULTS ===\n";
echo "Cris John isolation: " . ($crisPass ? "✅ SECURE" : "❌ VULNERABLE") . "\n";
echo "Maria isolation: " . ($mariaPass ? "✅ SECURE" : "❌ VULNERABLE") . "\n";
echo "Admin access control: " . ($adminCrisPass && $adminMariaPass ? "✅ WORKING" : "❌ BROKEN") . "\n";

if ($crisPass && $mariaPass && $adminCrisPass && $adminMariaPass) {
    echo "\n🎉 ALL TESTS PASSED! User messages are properly isolated!\n";
} else {
    echo "\n⚠️ SOME TESTS FAILED! Security issue detected!\n";
}
