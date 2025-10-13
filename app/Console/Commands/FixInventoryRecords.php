<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Donation;
use Illuminate\Support\Facades\DB;

class FixInventoryRecords extends Command
{
    protected $signature = 'inventory:fix';
    protected $description = 'Fix existing successful donations to have proper inventory fields';

    public function handle()
    {
        $this->info('Checking successful donations...');
        
        $successfulDonations = Donation::whereIn('status', ['success_walk_in', 'success_home_collection'])->get();
        
        $this->info("Found {$successfulDonations->count()} successful donations");
        
        $updated = 0;
        foreach ($successfulDonations as $donation) {
            $this->line("Checking donation {$donation->breastmilk_donation_id}:");
            $this->line("  Status: {$donation->status}");
            $this->line("  Total Volume: " . ($donation->total_volume ?? 'NULL'));
            $this->line("  Available Volume: " . ($donation->available_volume ?? 'NULL'));
            $this->line("  Pasteurization Status: " . ($donation->pasteurization_status ?? 'NULL'));
            
            if (!$donation->isInInventory()) {
                $this->warn("  Adding to inventory...");
                $donation->addToInventory();
                $updated++;
                $this->info("  ✓ Added to inventory");
            } else {
                $this->info("  ✓ Already in inventory");
            }
            $this->line("");
        }
        
        $this->info("Updated {$updated} donations");
        
        // Test the query
        $inventoryCount = Donation::unpasteurizedInventory()->count();
        $this->info("Donations now in unpasteurized inventory: {$inventoryCount}");
        
        return 0;
    }
}
