<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking availability slots...\n\n";

$slots = \App\Models\Availability::where('status', 'available')
    ->where('available_date', '>=', now()->format('Y-m-d'))
    ->orderBy('available_date')
    ->limit(10)
    ->get();

if ($slots->isEmpty()) {
    echo "No available slots found!\n";
} else {
    echo "Found " . $slots->count() . " available slots:\n\n";
    foreach ($slots as $slot) {
        echo "ID: {$slot->id} | Date: {$slot->available_date} | Status: {$slot->status}\n";
    }
}
