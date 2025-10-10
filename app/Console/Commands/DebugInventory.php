<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Donation;

class DebugInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:inventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug donation inventory status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== DONATION INVENTORY DEBUG ===');
        
        $donations = Donation::all();
        $this->info("Total donations: " . $donations->count());
        
        foreach ($donations as $donation) {
            $this->info("--- Donation #{$donation->donation_id} ---");
            $this->info("Status: " . $donation->status);
            $this->info("Method: " . $donation->donation_method);
            $this->info("Number of bags: " . ($donation->number_of_bags ?? 'NULL'));
            $this->info("Total volume: " . ($donation->total_volume ?? 'NULL'));
            $this->info("Available volume: " . ($donation->available_volume ?? 'NULL'));
            $this->info("Inventory status: " . ($donation->inventory_status ?? 'NULL'));
            $this->info("Pasteurization status: " . ($donation->pasteurization_status ?? 'NULL'));
            $this->info("Individual bag volumes: " . json_encode($donation->individual_bag_volumes));
            $this->info("");
        }
        
        $this->info('=== FIXING AVAILABLE VOLUMES ===');
        $fixed = 0;
        foreach ($donations as $donation) {
            if ($donation->available_volume === null && $donation->total_volume > 0) {
                $donation->available_volume = $donation->total_volume;
                $donation->save();
                $this->info("Fixed Donation #{$donation->donation_id}: Set available_volume to {$donation->total_volume}ml");
                $fixed++;
            }
        }
        $this->info("Fixed {$fixed} donations");
        
        $this->info('=== UNPASTEURIZED INVENTORY QUERY (AFTER FIX) ===');
        $unpasteurized = Donation::unpasteurizedInventory()->get();
        $this->info("Unpasteurized inventory count: " . $unpasteurized->count());
        
        foreach ($unpasteurized as $donation) {
            $this->info("Donation #{$donation->donation_id} - {$donation->available_volume}ml available");
        }
        
        return 0;
    }
}
