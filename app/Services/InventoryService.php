<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\PasteurizationBatch;
use App\Models\DispensedMilk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    public function listInventory()
    {
        $unpasteurizedDonations = Donation::with(['user', 'admin'])->unpasteurizedInventory()->get();

        $pasteurizationBatches = PasteurizationBatch::with(['admin', 'donations.user'])
            ->where('status', 'active')
            ->where('available_volume', '>', 0)
            ->orderBy('date_pasteurized', 'asc')
            ->get();

        $dispensedMilk = DispensedMilk::with([
            'guardian', 'recipient', 'admin', 
            'sourceDonations.user', 'sourceBatches'
        ])->orderBy('date_dispensed', 'desc')->get();

        return compact('unpasteurizedDonations', 'pasteurizationBatches', 'dispensedMilk');
    }

    public function pasteurize(array $donationIds, ?string $notes, int $adminId)
    {
        DB::beginTransaction();
        try {
            $donations = Donation::whereIn('breastmilk_donation_id', $donationIds)
                ->readyForPasteurization()
                ->get();

            if ($donations->isEmpty()) {
                throw new \RuntimeException('No valid donations found for pasteurization.');
            }

            $totalVolume = $donations->sum('total_volume');

            $batch = PasteurizationBatch::create([
                'batch_number' => PasteurizationBatch::generateBatchNumber(),
                'total_volume' => $totalVolume,
                'available_volume' => $totalVolume,
                'date_pasteurized' => now()->toDateString(),
                'time_pasteurized' => now()->toTimeString(),
                'admin_id' => $adminId,
                'status' => 'active',
                'notes' => $notes
            ]);

            foreach ($donations->sortBy('added_to_inventory_at') as $donation) {
                $donation->moveToBatch($batch->batch_id);
            }

            DB::commit();

            $simpleBatchNumber = intval(substr($batch->batch_number, -3));

            return [
                'batch' => $batch,
                'donations_count' => $donations->count(),
                'total_volume' => $totalVolume,
                'simple_batch_name' => "Batch {$simpleBatchNumber}"
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Pasteurization error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getBatchDetails(int $batchId)
    {
        $batch = PasteurizationBatch::with(['donations.user', 'admin'])->findOrFail($batchId);
        return $batch;
    }

    public function getStats()
    {
        return [
            'unpasteurized_total_volume' => Donation::unpasteurizedInventory()->where('available_volume', '>', 0)->sum('available_volume'),
            'unpasteurized_donations_count' => Donation::unpasteurizedInventory()->count(),
            'pasteurized_total_volume' => PasteurizationBatch::where('status', 'active')->sum('available_volume'),
            'pasteurized_batches_count' => PasteurizationBatch::where('status', 'active')->count(),
            'total_dispensed_volume' => DispensedMilk::sum('volume_dispensed'),
            'dispensed_records_count' => DispensedMilk::count()
        ];
    }
}
