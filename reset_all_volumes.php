<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== RESET ALL DONATIONS TO FULL VOLUME ===\n\n";

$donations = DB::table('breastmilk_donation')->get();

echo "This will reset ALL donations to full volume (dispensed_volume = 0, available_volume = total_volume)\n\n";

foreach ($donations as $donation) {
    echo "ID {$donation->breastmilk_donation_id}: Will reset to {$donation->total_volume}ml available\n";
}

echo "\n⚠️  THIS IS A DRY RUN - Uncomment the update code to actually reset\n\n";

// Uncomment below to actually reset
/*
DB::table('breastmilk_donation')->update([
    'dispensed_volume' => 0,
    'available_volume' => DB::raw('total_volume')
]);

echo "✅ All donations reset to full volume!\n";
*/

echo "To reset:\n";
echo "1. Uncomment the DB::table()->update() block\n";
echo "2. Run: php reset_all_volumes.php\n";
