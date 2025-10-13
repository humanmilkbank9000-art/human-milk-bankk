<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Donation;

class FixDispensedVolumes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donations:fix-dispensed-volumes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix dispensed_volume for existing donations by calculating from total_volume - available_volume';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Fixing Dispensed Volume for Existing Donations ===');
        $this->newLine();

        $donations = Donation::whereIn('status', ['success_walk_in', 'success_home_collection'])->get();

        if ($donations->count() === 0) {
            $this->info('No donations found to fix.');
            return 0;
        }

        $fixed = 0;
        $alreadyCorrect = 0;

        $this->withProgressBar($donations, function ($donation) use (&$fixed, &$alreadyCorrect) {
            $total = (float) ($donation->total_volume ?? 0);
            $available = (float) ($donation->available_volume ?? 0);
            $dispensed = (float) ($donation->dispensed_volume ?? 0);
            
            // Calculate what dispensed_volume SHOULD be
            $correctDispensed = $total - $available;
            
            if (abs($correctDispensed - $dispensed) > 0.01) { // Use small epsilon for float comparison
                $donation->dispensed_volume = $correctDispensed;
                $donation->save();
                $fixed++;
            } else {
                $alreadyCorrect++;
            }
        });

        $this->newLine(2);
        $this->info('=== Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Donations', $donations->count()],
                ['Fixed', $fixed],
                ['Already Correct', $alreadyCorrect],
            ]
        );

        if ($fixed > 0) {
            $this->info("✓ Successfully fixed {$fixed} donation(s)!");
        } else {
            $this->info('✓ All donations already have correct dispensed_volume values.');
        }

        return 0;
    }
}

