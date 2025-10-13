<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Donation;
use Illuminate\Support\Facades\DB;

class FixAvailableVolumeData extends Command
{
    protected $signature = 'fix:available-volume';
    protected $description = 'Fix donations where available_volume is incorrect or missing';

    public function handle()
    {
        $this->info('Checking for donations with data inconsistencies...');
        
        // Find donations where available_volume is null or doesn't match the calculation
        $inconsistentDonations = Donation::whereIn('status', ['success_walk_in', 'success_home_collection'])
            ->where('pasteurization_status', 'unpasteurized')
            ->where(function($query) {
                $query->whereNull('available_volume')
                      ->orWhereRaw('available_volume != (total_volume - dispensed_volume)');
            })
            ->get();

        if ($inconsistentDonations->isEmpty()) {
            $this->info('✓ No inconsistencies found. All donations have correct available_volume values.');
            
            // Show current inventory summary
            $this->info("\n=== CURRENT INVENTORY ===");
            $availableCount = Donation::where('pasteurization_status', 'unpasteurized')
                ->where('available_volume', '>', 0)
                ->count();
            $availableVolume = Donation::where('pasteurization_status', 'unpasteurized')
                ->where('available_volume', '>', 0)
                ->sum('available_volume');
            
            $this->info("Donations with available volume: {$availableCount}");
            $this->info("Total available volume: {$availableVolume}ml");
            
            return 0;
        }

        $this->warn("Found {$inconsistentDonations->count()} donations with inconsistent data:");
        
        $fixed = 0;
        foreach ($inconsistentDonations as $donation) {
            $this->info("\nDonation ID: {$donation->breastmilk_donation_id}");
            $this->line("  Status: {$donation->status}");
            $this->line("  Total Volume: {$donation->total_volume}ml");
            $this->line("  Dispensed Volume: {$donation->dispensed_volume}ml");
            $this->line("  Available Volume (current): " . ($donation->available_volume ?? 'NULL') . "ml");
            
            // Calculate what available_volume should be
            $totalVolume = (float) ($donation->total_volume ?? 0);
            $dispensedVolume = (float) ($donation->dispensed_volume ?? 0);
            $correctAvailableVolume = $totalVolume - $dispensedVolume;
            
            $this->line("  → Correct Available Volume should be: {$correctAvailableVolume}ml");
            
            // Update the available_volume
            $donation->attributes['available_volume'] = (float)$correctAvailableVolume == (int)$correctAvailableVolume 
                ? (int)$correctAvailableVolume 
                : rtrim(rtrim(number_format($correctAvailableVolume, 2, '.', ''), '0'), '.');
            
            // Check if depleted
            if ($correctAvailableVolume <= 0) {
                $this->warn("  → No volume available (fully dispensed)");
            } else {
                $this->info("  → Has available volume");
            }
            
            $donation->save();
            $this->info("  ✓ Fixed!");
            $fixed++;
        }
        
        $this->info("\n✓ Fixed {$fixed} donations!");
        
        // Show summary
        $this->info("\n=== SUMMARY ===");
        $availableCount = Donation::where('pasteurization_status', 'unpasteurized')
            ->where('available_volume', '>', 0)
            ->count();
        $availableVolume = Donation::where('pasteurization_status', 'unpasteurized')
            ->where('available_volume', '>', 0)
            ->sum('available_volume');
        
        $this->info("Donations with available volume: {$availableCount}");
        $this->info("Total available volume: {$availableVolume}ml");
        
        return 0;
    }
}
