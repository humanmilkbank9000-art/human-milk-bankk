<?php

// Debug the SQL query being generated
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Message;
use Illuminate\Support\Facades\DB;

DB::enableQueryLog();

echo "=== TESTING SQL QUERY FOR CRIS JOHN ===\n\n";

$crisJohnId = 1;
$crisJohnType = 'App\\Models\\User';
$adminId = 1;
$adminType = 'App\\Models\\Admin';

$query = Message::conversation($crisJohnId, $crisJohnType, $adminId, $adminType);

// Get the SQL
$messages = $query->get();

$queries = DB::getQueryLog();
echo "SQL Query:\n";
echo $queries[0]['query'] . "\n\n";
echo "Bindings:\n";
print_r($queries[0]['bindings']);

echo "\n\nMessages returned: " . $messages->count() . "\n";
foreach ($messages as $msg) {
    echo "- Message ID {$msg->id}: From {$msg->sender_id} To {$msg->receiver_id}\n";
}
