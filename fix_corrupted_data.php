<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIX CORRUPTED VOLUME DATA ===\n\n";

// Example: If donation ID 1 should have only dispensed 200ml instead of 2000ml
// Adjust these values based on what SHOULD have been dispensed

$donationId = 1;
$shouldHaveDispensed = 200; // What should have been dispensed
$totalVolume = 2000;

echo "Donation ID: $donationId\n";
echo "Total Volume: $totalVolume ml\n";
echo "Should Have Dispensed: $shouldHaveDispensed ml\n";
echo "Should Be Available: " . ($totalVolume - $shouldHaveDispensed) . " ml\n\n";

echo "Do you want to fix this? (This is a DRY RUN - uncomment the update line to actually fix)\n\n";

// Uncomment the line below to actually update the database
/*
DB::table('breastmilk_donation')
    ->where('breastmilk_donation_id', $donationId)
    ->update([
        'dispensed_volume' => $shouldHaveDispensed,
        'available_volume' => $totalVolume - $shouldHaveDispensed
    ]);

echo "✅ Fixed!\n";
*/

echo "To fix, edit this file and:\n";
echo "1. Set the correct \$shouldHaveDispensed amount\n";
echo "2. Uncomment the DB::table()->update() block\n";
echo "3. Run: php fix_corrupted_data.php\n";
