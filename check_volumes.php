<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING BREASTMILK DONATIONS ===\n\n";

$donations = DB::table('breastmilk_donation')
    ->select('breastmilk_donation_id', 'total_volume', 'dispensed_volume', 'available_volume', 'status')
    ->get();

echo "Total donations: " . $donations->count() . "\n\n";

foreach ($donations as $donation) {
    echo "ID: {$donation->breastmilk_donation_id}\n";
    echo "Status: " . ($donation->status ?? 'N/A') . "\n";
    echo "Total Volume: {$donation->total_volume} ml\n";
    echo "Dispensed Volume: {$donation->dispensed_volume} ml\n";
    echo "Available Volume: {$donation->available_volume} ml\n";
    
    $calculated = $donation->total_volume - $donation->dispensed_volume;
    echo "Calculated (total - dispensed): {$calculated} ml\n";
    
    if ($calculated != $donation->available_volume) {
        echo "⚠️  MISMATCH! Available should be {$calculated} but is {$donation->available_volume}\n";
    }
    
    echo "\n---\n\n";
}

echo "\n=== CHECKING WITH ELOQUENT MODEL ===\n\n";

$eloquentDonations = \App\Models\Donation::all();

echo "Total donations via model: " . $eloquentDonations->count() . "\n\n";

foreach ($eloquentDonations as $donation) {
    echo "ID: {$donation->breastmilk_donation_id}\n";
    echo "Status: {$donation->status}\n";
    echo "Total Volume: {$donation->total_volume} ml\n";
    echo "Dispensed Volume: {$donation->dispensed_volume} ml\n";
    echo "Available Volume: {$donation->available_volume} ml\n";
    echo "\n---\n\n";
}
