<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Donation;

if ($argc < 4) {
    echo "Usage: php update_bag_volume.php <donation_id> <bag_index_1based> <new_volume>\n";
    exit(1);
}
$donationId = (int)$argv[1];
$bagIndex1 = (int)$argv[2];
$newVolume = (float)$argv[3];
$bagIndex = $bagIndex1 - 1;

$d = Donation::find($donationId);
if (!$d) { echo "Donation not found\n"; exit(1); }

echo "Before:\n";
echo "id={$d->breastmilk_donation_id}, total={$d->total_volume}, available={$d->available_volume}\n";
echo "bag_details:\n"; print_r($d->bag_details);
echo "individual_bag_volumes:\n"; print_r($d->individual_bag_volumes ?? []);

$bd = $d->bag_details ?? [];
if (!isset($bd[$bagIndex])) {
    echo "Bag index {$bagIndex1} not found in bag_details\n";
    exit(1);
}
$bd[$bagIndex]['volume'] = $newVolume;

// recompute vols
$vols = array_map(function($b){ return isset($b['volume']) ? (float)$b['volume'] : 0.0; }, $bd);
$d->bag_details = $bd;
$d->number_of_bags = count($bd);
$d->total_volume = array_sum($vols);
$d->available_volume = array_sum($vols);
$d->individual_bag_volumes = array_values($vols);
$d->save();

echo "After:\n";
echo "id={$d->breastmilk_donation_id}, total={$d->total_volume}, available={$d->available_volume}\n";
echo "bag_details:\n"; print_r($d->bag_details);
echo "individual_bag_volumes:\n"; print_r($d->individual_bag_volumes ?? []);
