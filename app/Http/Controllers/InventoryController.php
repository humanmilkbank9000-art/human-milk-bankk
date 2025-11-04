<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Donation;
use App\Models\PasteurizationBatch;
use App\Models\DispensedMilk;
use App\Models\DisposedMilk;
use App\Services\InventoryService;

class InventoryController extends Controller
{
    /**
     * Inventory service instance.
     * @var InventoryService
     */
    protected $service;

    public function __construct(InventoryService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return redirect()->route('login')->with('error', 'Please login as admin first.');
        }

        // Section 1: Unpasteurized Breastmilk (Ready for direct dispensing or pasteurization)
        $unpasteurizedDonations = Donation::with(['user', 'admin'])
            ->unpasteurizedInventory()
            ->orderBy('added_to_inventory_at', 'asc') // FIFO ordering
            ->get();

        // Section 2: Pasteurized Breastmilk (Batches with available volume)
        $pasteurizationBatches = PasteurizationBatch::with(['admin', 'donations.user'])
            ->where('status', 'active')
            ->where('available_volume', '>', 0)
            ->orderBy('date_pasteurized', 'asc')
            ->get();

        // Section 3: Dispensed Breastmilk (All dispensing records)
        $dispensedMilk = DispensedMilk::with([
            'guardian', 'recipient', 'admin',
            'sourceDonations.user', 'sourceBatches'
        ])
            ->orderBy('date_dispensed', 'desc')
            ->get();

        // Section 4: Disposed records split by type
        $disposedUnpasteurized = DisposedMilk::with(['sourceDonation.user', 'admin'])
            ->whereNotNull('source_donation_id')
            ->orderBy('date_disposed', 'desc')
            ->get();

        $disposedPasteurized = DisposedMilk::with(['sourceBatch', 'admin'])
            ->whereNotNull('source_batch_id')
            ->orderBy('date_disposed', 'desc')
            ->get();


        return view('admin.inventory', compact(
            'unpasteurizedDonations',
            'pasteurizationBatches', 
            'dispensedMilk',
            'disposedUnpasteurized',
            'disposedPasteurized'
        ));
    }

    public function pasteurize(Request $request)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Accept either a donation_map (bag-level) or donation_ids (full-donation ids).
        // We'll validate notes and then ensure at least one of the two payloads is present.
        $request->validate([
            'donation_map' => 'nullable|array',
            'donation_ids' => 'nullable|array',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Require that at least one of donation_map or donation_ids is provided
        if (empty($request->donation_map) && empty($request->donation_ids)) {
            return response()->json(['error' => 'The donation_map or donation_ids field is required.'], 422);
        }

        DB::beginTransaction();
        
        try {
            $adminId = Session::get('account_id');
            
            // Extract donation_map from request: { donationId: [bagIndex,...], ... }
            $donationMap = (array) $request->donation_map;

            // If donation_map is empty but donation_ids were provided (legacy or other callers),
            // treat each donation id as a full-donation move by building indices for all bags.
            if (empty($donationMap) && !empty($request->donation_ids)) {
                // Normalize donation_ids: accept comma-separated string, array of ids, or form array
                $rawIds = $request->donation_ids;
                if (is_string($rawIds)) {
                    $ids = array_filter(array_map('trim', explode(',', $rawIds)), function ($v) { return $v !== ''; });
                } elseif (is_array($rawIds)) {
                    $ids = $rawIds;
                } else {
                    $ids = [];
                }
                $ids = array_map('intval', $ids);
                $ids = array_values(array_filter($ids, function ($v) { return $v > 0; }));

                if (empty($ids)) {
                    // nothing to do
                    $donationIds = [];
                } else {
                    // We'll fetch the donations first to know how many bags each has.
                    $donationsForMap = Donation::whereIn('breastmilk_donation_id', $ids)
                        ->readyForPasteurization()
                        ->get()
                        ->keyBy('breastmilk_donation_id');

                    foreach ($donationsForMap as $id => $donation) {
                        // prefer stored bag arrays to determine count
                        $bagVolumes = $donation->individual_bag_volumes ?? [];
                        if (empty($bagVolumes) && !empty($donation->bag_details) && is_array($donation->bag_details)) {
                            $bagVolumes = $donation->bag_details;
                        }
                        $totalBags = is_array($bagVolumes) ? count($bagVolumes) : max(1, intval($donation->number_of_bags ?? 1));
                        // full indices 0..(totalBags-1)
                        $indices = range(0, max(0, $totalBags - 1));
                        $donationMap[$id] = $indices;
                    }
                }
            }

            $donationIds = array_keys($donationMap);

            $donations = Donation::whereIn('breastmilk_donation_id', $donationIds)
                ->readyForPasteurization()
                ->get()
                ->keyBy('breastmilk_donation_id');

            if ($donations->isEmpty()) {
                throw new \Exception('No valid donations found for pasteurization.');
            }

            // Calculate total volume based on selected bag indices (not entire donation totals)
            $totalVolume = 0.0;

            // We'll collect processed donation count
            $processedDonations = 0;

            // Create pasteurization batch placeholder first (we need batch id when creating moved records)
            $batch = PasteurizationBatch::create([
                'batch_number' => PasteurizationBatch::generateBatchNumber(),
                'total_volume' => 0, // will update after computing
                'available_volume' => 0,
                'date_pasteurized' => now()->toDateString(),
                'time_pasteurized' => now()->toTimeString(),
                'admin_id' => $adminId,
                'status' => 'active',
                'notes' => $request->notes
            ]);

            // Process each donation entry in the donation_map
            foreach ($donationMap as $donationId => $bagIndices) {
                // ensure donation exists and is eligible
                if (!isset($donations[$donationId])) {
                    // skip unknown or ineligible donation ids
                    continue;
                }

                $donation = $donations[$donationId];

                // Normalize bag indices array and ensure integer indices
                $indices = is_array($bagIndices) ? array_values($bagIndices) : [];
                $indices = array_map('intval', $indices);

                // Try to get per-bag volumes: prefer individual_bag_volumes then bag_details
                $bagVolumes = $donation->individual_bag_volumes ?? [];
                if (empty($bagVolumes) && !empty($donation->bag_details) && is_array($donation->bag_details)) {
                    // extract 'volume' from bag_details objects
                    $bagVolumes = array_map(function ($b) {
                        return isset($b['volume']) ? (float)$b['volume'] : 0.0;
                    }, $donation->bag_details);
                }

                // Compute selected volume for this donation
                $selectedVolume = 0.0;
                $selectedBagVolumes = [];
                $selectedBagDetails = [];

                foreach ($indices as $idx) {
                    // ensure index is integer
                    $idx = intval($idx);
                    if (isset($bagVolumes[$idx])) {
                        $vol = (float) $bagVolumes[$idx];
                        $selectedVolume += $vol;
                        $selectedBagVolumes[] = $vol;
                    } elseif (!empty($donation->bag_details) && isset($donation->bag_details[$idx])) {
                        $detail = $donation->bag_details[$idx];
                        $vol = isset($detail['volume']) ? (float) $detail['volume'] : 0.0;
                        $selectedVolume += $vol;
                        $selectedBagVolumes[] = $vol;
                        $selectedBagDetails[] = $detail;
                    } else {
                        // missing bag index - skip
                    }
                }

                // Log selection for debugging when volumes look unexpected
                if ($selectedVolume <= 0 || $selectedVolume != array_sum($selectedBagVolumes)) {
                    Log::info('Pasteurize selection debug', [
                        'donation_id' => $donationId,
                        'indices' => $indices,
                        'bagVolumes' => $bagVolumes,
                        'bag_details' => $donation->bag_details,
                        'selectedBagVolumes' => $selectedBagVolumes,
                        'selectedVolume_calculated' => $selectedVolume
                    ]);
                }

                // Skip if no volume selected for this donation
                if ($selectedVolume <= 0) {
                    continue;
                }

                $totalVolume += $selectedVolume;

                // Determine if this is a full-donation move
                $totalBags = is_array($bagVolumes) ? count($bagVolumes) : (is_array($donation->bag_details) ? count($donation->bag_details) : 0);
                $isFullMove = false;
                if ($totalBags > 0 && count($indices) === $totalBags) {
                    $isFullMove = true;
                } elseif ((float)$selectedVolume >= (float)$donation->available_volume) {
                    $isFullMove = true;
                }

                if ($isFullMove) {
                    // move entire donation into batch
                    $donation->moveToBatch($batch->batch_id);
                } else {
                    // Partial move: create a new donation record representing the moved bags
                    $movedDonation = Donation::create([
                        'health_screening_id' => $donation->health_screening_id,
                        'admin_id' => $adminId,
                        'user_id' => $donation->user_id,
                        'donation_method' => $donation->donation_method,
                        'status' => $donation->status,
                        'number_of_bags' => count($selectedBagVolumes),
                        'individual_bag_volumes' => $selectedBagVolumes,
                        'total_volume' => $selectedVolume,
                        'dispensed_volume' => 0,
                        'available_volume' => $selectedVolume,
                        'donation_date' => $donation->donation_date,
                        'donation_time' => $donation->donation_time,
                        'scheduled_pickup_date' => $donation->scheduled_pickup_date,
                        'scheduled_pickup_time' => $donation->scheduled_pickup_time,
                        'availability_id' => $donation->availability_id,
                        'pasteurization_status' => 'pasteurized',
                        'pasteurization_batch_id' => $batch->batch_id,
                        'added_to_inventory_at' => now(),
                        'expiration_date' => $donation->expiration_date,
                    ]);

                    // If bag_details exist, attach moved bag details to the moved donation
                    if (!empty($selectedBagDetails)) {
                        $movedDonation->bag_details = $selectedBagDetails;
                        $movedDonation->save();
                    }

                    // Update original donation: remove moved bags and reduce available_volume
                    $remainingBagVolumes = [];
                    if (is_array($bagVolumes) && count($bagVolumes) > 0) {
                        foreach ($bagVolumes as $i => $v) {
                            if (!in_array($i, $indices, true)) {
                                $remainingBagVolumes[] = $v;
                            }
                        }
                        $donation->individual_bag_volumes = $remainingBagVolumes;
                        $donation->number_of_bags = max(0, count($remainingBagVolumes));
                    }

                    if (!empty($donation->bag_details) && is_array($donation->bag_details)) {
                        $remainingDetails = [];
                        foreach ($donation->bag_details as $i => $d) {
                            if (!in_array($i, $indices, true)) {
                                $remainingDetails[] = $d;
                            }
                        }
                        $donation->bag_details = $remainingDetails;
                    }

                    // Reduce available volume on the original donation
                    $donation->available_volume = max(0, (float)$donation->available_volume - $selectedVolume);
                    $donation->save();
                }

                $processedDonations++;
            }

            // Update batch totals now that we've processed donations
            $batch->total_volume = $totalVolume;
            $batch->available_volume = $totalVolume;
            $batch->save();

            Log::info('Pasteurization batch summary', [
                'batch_number' => $batch->batch_number,
                'batch_id' => $batch->batch_id,
                'processedDonations' => $processedDonations,
                'totalVolume' => $totalVolume
            ]);

            DB::commit();

            // Extract simple batch number (e.g., "1" from "BATCH-001")
            $simpleBatchNumber = intval(substr($batch->batch_number, -3));

            return response()->json([
                'success' => true,
                'message' => "Successfully created {$batch->batch_number} with {$processedDonations} donation(s) totaling {$totalVolume}ml",
                'batch_id' => $batch->batch_id,
                'batch_number' => $batch->batch_number,
                'simple_batch_name' => "Batch {$simpleBatchNumber}",
                'donations_count' => $processedDonations,
                'total_volume' => $totalVolume
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    

    public function getBatchDetails($batchId)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $batch = PasteurizationBatch::with(['donations.user', 'admin'])
            ->findOrFail($batchId);

        $donations = $batch->donations->map(function($donation) {
            // determine volume per bag: prefer formatted individual_bag_volumes, else derive from bag_details
            $volumePerBag = $donation->formatted_bag_volumes ?? '-';
            if (($volumePerBag === '-' || empty($volumePerBag)) && !empty($donation->bag_details) && is_array($donation->bag_details)) {
                $vols = array_map(function($d) {
                    return isset($d['volume']) ? (float)$d['volume'] : 0;
                }, $donation->bag_details);
                if (!empty($vols)) {
                    $volumePerBag = implode(', ', array_map(function($v) {
                        return ((float)$v == (int)$v) ? (int)$v . 'ml' : rtrim(rtrim(number_format($v, 2, '.', ''), '0'), '.') . 'ml';
                    }, $vols));
                }
            }

            // determine date and time with fallbacks similar to view
            $date = '-';
            if (!empty($donation->donation_date)) {
                try {
                    $date = $donation->donation_date->format('M d, Y');
                } catch (\Exception $e) {
                    $date = (string) $donation->donation_date;
                }
            } elseif (!empty($donation->scheduled_pickup_date)) {
                try {
                    $date = $donation->scheduled_pickup_date->format('M d, Y');
                } catch (\Exception $e) {
                    $date = (string) $donation->scheduled_pickup_date;
                }
            }

            $time = '-';
            if (!empty($donation->availability) && !empty($donation->availability->formatted_time)) {
                $time = $donation->availability->formatted_time;
            } elseif (!empty($donation->donation_time)) {
                try {
                    $time = \Carbon\Carbon::parse($donation->donation_time)->format('g:i A');
                } catch (\Exception $e) {
                    $time = (string) $donation->donation_time;
                }
            } elseif (!empty($donation->scheduled_pickup_time)) {
                $time = $donation->scheduled_pickup_time;
            }

            return [
                'donor_name' => trim((($donation->user->first_name ?? '') . ' ' . ($donation->user->last_name ?? ''))),
                'donation_type' => ucfirst(str_replace('_', ' ', $donation->donation_method)),
                'number_of_bags' => $donation->number_of_bags,
                'volume_per_bag' => $volumePerBag,
                'total_volume' => $donation->total_volume,
                'date' => $date,
                'time' => $time,
            ];
        });

        return response()->json([
            'batch' => $batch,
            'donations' => $donations
        ]);
    }

    public function getInventoryStats()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Return structured stats matching keys expected by the frontend
        $stats = [
            'unpasteurized_donations_count' => Donation::unpasteurizedInventory()->count(),
            'unpasteurized_total_volume' => Donation::unpasteurizedInventory()->sum('available_volume'),
            'pasteurized_batches_count' => PasteurizationBatch::where('status', 'active')->count(),
            'pasteurized_total_volume' => PasteurizationBatch::where('status', 'active')->sum('available_volume'),
            'total_dispensed_volume' => DispensedMilk::sum('volume_dispensed'),
            'dispensed_records_count' => DispensedMilk::count(),
        ];

        return response()->json($stats);
    }

    /**
     * Dispose selected bags (AJAX).
     * Expects { donation_map: { donationId: [bagIndex,...], ... }, notes: 'optional' }
     */
    public function dispose(Request $request)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'donation_map' => 'required|array|min:1',
            'notes' => 'nullable|string|max:1000'
        ]);

        $adminId = Session::get('account_id');
        $donationMap = $request->input('donation_map', []);
        $notes = $request->input('notes');

        try {
            $result = $this->service->dispose($donationMap, $notes, $adminId);

            if (!empty($result['warnings'])) {
                return response()->json(['success' => true, 'message' => "Disposed {$result['total_disposed_bags']} bag(s) totaling {$result['total_disposed_volume']}ml", 'warnings' => $result['warnings']]);
            }

            return response()->json(['success' => true, 'message' => "Disposed {$result['total_disposed_bags']} bag(s) totaling {$result['total_disposed_volume']}ml", 'total_disposed_bags' => $result['total_disposed_bags'], 'total_disposed_volume' => $result['total_disposed_volume']]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to dispose selected bags. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Dispose entire pasteurized batches (by batch ids).
     * Expects { batch_ids: [id, ...], notes?: string }
     */
    public function disposeBatches(Request $request)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'batch_ids' => 'required|array|min:1',
            'batch_ids.*' => 'integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $adminId = Session::get('account_id');
        $batchIds = $request->input('batch_ids', []);
        $notes = $request->input('notes');

        try {
            $result = $this->service->disposeBatches($batchIds, $notes, $adminId);

            return response()->json([
                'success' => true,
                'message' => "Disposed {$result['count']} batch(es) totaling {$result['total_volume']}ml",
                'count' => $result['count'],
                'total_volume' => $result['total_volume'],
                'warnings' => $result['warnings'] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to dispose selected batches. ' . $e->getMessage()], 500);
        }
    }
}
