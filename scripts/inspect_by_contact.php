<?php
// Boot Laravel framework so we can use Eloquent outside artisan
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Use models
use App\Models\User;
use App\Models\Donation;

$contact = $argv[1] ?? '09614904243';
$user = User::where('contact_number', $contact)->first();
if (!$user) {
    echo "NO_USER\n";
    exit(0);
}
$donations = Donation::with('user')
    ->where('user_id', $user->user_id)
    ->where('donation_method', 'home_collection')
    ->orderByDesc('updated_at')
    ->get()
    ->map(function($d){
        return [
            'id' => $d->breastmilk_donation_id,
            'status' => $d->status,
            'total' => $d->total_volume,
            'available' => $d->available_volume,
            'bag_details' => $d->bag_details,
            'individual_bag_volumes' => $d->individual_bag_volumes ?? null,
            'updated_at' => (string)$d->updated_at,
        ];
    });

echo json_encode($donations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
