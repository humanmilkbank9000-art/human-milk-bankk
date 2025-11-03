<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\PasteurizationBatch;
use App\Models\DispensedMilk;
use App\Models\DisposedMilk;
use App\Models\PasteurizationBatch as Batch;
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
            // Only include donations that are unpasteurized and still have remaining available volume
            $donations = Donation::whereIn('breastmilk_donation_id', $donationIds)
                ->readyForPasteurization()
                ->where('available_volume', '>', 0)
                ->get();

            if ($donations->isEmpty()) {
                throw new \RuntimeException('No valid donations found for pasteurization.');
            }

            // Use remaining available_volume, not the original total_volume, to compute batch size
            $totalVolume = $donations->sum('available_volume');

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

    /**
     * Dispose selected bags from donations.
     * @param array $donationMap { donationId: [bagIndex, ...], ... }
     * @param string|null $notes
     * @param int $adminId
     * @return array { total_disposed_bags, total_disposed_volume, warnings: [] }
     */
    public function dispose(array $donationMap, ?string $notes, int $adminId): array
    {
        DB::beginTransaction();
        try {
            $donationIds = array_keys($donationMap);
            $donations = Donation::whereIn('breastmilk_donation_id', $donationIds)
                ->readyForPasteurization()
                ->where('available_volume', '>', 0)
                ->get()
                ->keyBy('breastmilk_donation_id');

            if ($donations->isEmpty()) {
                throw new \RuntimeException('No valid donations found to dispose from.');
            }

            $totalDisposedBags = 0;
            $totalDisposedVolume = 0.0;
            $warnings = [];

            foreach ($donationMap as $donationId => $bagIndices) {
                if (!isset($donations[$donationId])) {
                    $warnings[] = "Donation {$donationId} not eligible or not found.";
                    continue;
                }

                $donation = $donations[$donationId];

                // Determine bag volumes array
                $bagVolumes = $donation->individual_bag_volumes ?? [];
                $bagDetails = is_array($donation->bag_details) ? $donation->bag_details : [];
                if (empty($bagVolumes) && !empty($bagDetails)) {
                    // Map bag_details to volumes if needed
                    $bagVolumes = array_map(function ($d) {
                        return isset($d['volume']) ? (float)$d['volume'] : 0.0;
                    }, $bagDetails);
                }

                // Normalize selected indices
                $indices = is_array($bagIndices) ? array_values(array_map('intval', $bagIndices)) : [];
                $indices = array_unique(array_filter($indices, function ($i) { return $i >= 0; }));

                if (empty($indices)) {
                    $warnings[] = "No bags specified for donation {$donationId}.";
                    continue;
                }

                // Sum selected volumes and count
                $selectedVolume = 0.0;
                $selectedCount = 0;
                foreach ($indices as $idx) {
                    if (isset($bagVolumes[$idx])) {
                        $selectedVolume += (float)$bagVolumes[$idx];
                        $selectedCount++;
                    } elseif (isset($bagDetails[$idx]['volume'])) {
                        $selectedVolume += (float)$bagDetails[$idx]['volume'];
                        $selectedCount++;
                    }
                }

                if ($selectedCount === 0 || $selectedVolume <= 0) {
                    $warnings[] = "Donation {$donationId} had no measurable selected volume.";
                    continue;
                }

                // Determine if full disposal
                $totalBags = is_array($bagVolumes) ? count($bagVolumes) : 0;
                $isFullDisposal = ($totalBags > 0 && $selectedCount === $totalBags) || ($selectedVolume >= (float)$donation->available_volume);

                if ($isFullDisposal) {
                    // Zero out inventory for donation
                    $donation->available_volume = 0.0;
                    $donation->individual_bag_volumes = [];
                    $donation->bag_details = [];
                    $donation->number_of_bags = 0;
                    $donation->save();
                } else {
                    // Partial disposal: remove selected indices
                    $remainingVolumes = [];
                    $remainingDetails = [];
                    foreach ($bagVolumes as $i => $vol) {
                        if (!in_array($i, $indices, true)) {
                            $remainingVolumes[] = (float)$vol;
                            if (!empty($bagDetails)) {
                                $remainingDetails[] = $bagDetails[$i] ?? null;
                            }
                        }
                    }

                    $donation->individual_bag_volumes = $remainingVolumes;
                    if (!empty($bagDetails)) {
                        // Filter out nulls if any slipped in
                        $remainingDetails = array_values(array_filter($remainingDetails, function ($v) { return $v !== null; }));
                        $donation->bag_details = $remainingDetails;
                    }
                    $donation->number_of_bags = count($remainingVolumes);
                    $donation->available_volume = max(0, (float)$donation->available_volume - $selectedVolume);
                    $donation->save();
                }

                // Record disposal per donation
                DisposedMilk::create([
                    'source_donation_id' => $donation->breastmilk_donation_id,
                    'source_batch_id' => null,
                    'volume_disposed' => $selectedVolume,
                    'date_disposed' => now()->toDateString(),
                    'time_disposed' => now()->toTimeString(),
                    'admin_id' => $adminId,
                    'notes' => $notes,
                    'bag_indices' => array_values($indices),
                ]);

                $totalDisposedBags += $selectedCount;
                $totalDisposedVolume += $selectedVolume;
            }

            DB::commit();

            return [
                'total_disposed_bags' => $totalDisposedBags,
                'total_disposed_volume' => $totalDisposedVolume,
                'warnings' => $warnings,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Disposal error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    /**
     * Dispose entire pasteurized batches by IDs. Fully removes their available_volume and records a DisposedMilk row.
     * @param int[] $batchIds
     * @param string|null $notes
     * @param int $adminId
     * @return array{count:int,total_volume:float,warnings:array}
     */
    public function disposeBatches(array $batchIds, ?string $notes, int $adminId): array
    {
        DB::beginTransaction();
        try {
            $warnings = [];
            $totalDisposedVolume = 0.0;
            $count = 0;

            $batches = Batch::whereIn('batch_id', $batchIds)
                ->where('status', 'active')
                ->where('available_volume', '>', 0)
                ->lockForUpdate()
                ->get();

            foreach ($batches as $batch) {
                $avail = (float)$batch->available_volume;
                if ($avail <= 0) {
                    $warnings[] = "Batch {$batch->batch_number} has no available volume.";
                    continue;
                }

                // Record disposal
                DisposedMilk::create([
                    'source_donation_id' => null,
                    'source_batch_id' => $batch->batch_id,
                    'volume_disposed' => $avail,
                    'date_disposed' => now()->toDateString(),
                    'time_disposed' => now()->toTimeString(),
                    'admin_id' => $adminId,
                    'notes' => $notes,
                    'bag_indices' => null,
                ]);

                // Zero the available volume for the batch
                $batch->available_volume = 0.0;
                $batch->save();

                $totalDisposedVolume += $avail;
                $count++;
            }

            DB::commit();

            return [
                'count' => $count,
                'total_volume' => $totalDisposedVolume,
                'warnings' => $warnings,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Disposal (batches) error: ' . $e->getMessage());
            throw $e;
        }
    }
}
