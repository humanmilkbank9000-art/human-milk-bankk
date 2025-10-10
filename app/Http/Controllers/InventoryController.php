<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Donation;
use App\Models\PasteurizationBatch;
use App\Models\DispensedMilk;

class InventoryController extends Controller
{
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

        return view('admin.inventory', compact(
            'unpasteurizedDonations',
            'pasteurizationBatches', 
            'dispensedMilk'
        ));
    }

    public function pasteurize(Request $request)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'donation_ids' => 'required|array|min:1',
            'donation_ids.*' => 'exists:breastmilk_donation,breastmilk_donation_id',
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        
        try {
            $adminId = Session::get('account_id');
            $donations = Donation::whereIn('breastmilk_donation_id', $request->donation_ids)
                ->readyForPasteurization()
                ->get();

            if ($donations->isEmpty()) {
                throw new \Exception('No valid donations found for pasteurization.');
            }

            // Calculate total volume
            $totalVolume = $donations->sum('total_volume');

            // Create pasteurization batch
            $batch = PasteurizationBatch::create([
                'batch_number' => PasteurizationBatch::generateBatchNumber(),
                'total_volume' => $totalVolume,
                'available_volume' => $totalVolume,
                'date_pasteurized' => now()->toDateString(),
                'time_pasteurized' => now()->toTimeString(),
                'admin_id' => $adminId,
                'status' => 'active',
                'notes' => $request->notes
            ]);

            // Move donations to batch (FIFO - process oldest first)
            foreach ($donations->sortBy('added_to_inventory_at') as $donation) {
                $donation->moveToBatch($batch->batch_id);
            }

            DB::commit();

            // Extract simple batch number (e.g., "1" from "BATCH-001")
            $simpleBatchNumber = intval(substr($batch->batch_number, -3));

            return response()->json([
                'success' => true,
                'message' => "Successfully created {$batch->batch_number} with {$donations->count()} donations totaling {$totalVolume}ml",
                'batch_id' => $batch->batch_id,
                'batch_number' => $batch->batch_number,
                'simple_batch_name' => "Batch {$simpleBatchNumber}",
                'donations_count' => $donations->count(),
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

        return response()->json([
            'batch' => $batch,
            'donations' => $batch->donations->map(function($donation) {
                return [
                    'donor_name' => $donation->user->first_name . ' ' . $donation->user->last_name,
                    'donation_type' => ucfirst(str_replace('_', ' ', $donation->donation_method)),
                    'number_of_bags' => $donation->number_of_bags,
                    'volume_per_bag' => $donation->formatted_bag_volumes,
                    'total_volume' => $donation->total_volume,
                    'date' => $donation->donation_date->format('M d, Y'),
                    'time' => $donation->donation_time ? \Carbon\Carbon::parse($donation->donation_time)->format('g:i A') : '-'
                ];
            })
        ]);
    }

    public function getInventoryStats()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stats = [
            'unpasteurized_total_volume' => Donation::unpasteurizedInventory()->sum('available_volume'),
            'unpasteurized_donations_count' => Donation::unpasteurizedInventory()->count(),
            'pasteurized_total_volume' => PasteurizationBatch::where('status', 'active')->sum('available_volume'),
            'pasteurized_batches_count' => PasteurizationBatch::where('status', 'active')->count(),
            'total_dispensed_volume' => DispensedMilk::sum('volume_dispensed'),
            'dispensed_records_count' => DispensedMilk::count()
        ];

        return response()->json($stats);
    }
}
