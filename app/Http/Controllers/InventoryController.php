<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Donation;
use App\Models\PasteurizationBatch;
use App\Models\DispensedMilk;
use App\Services\InventoryService;
use App\Http\Requests\PasteurizeInventoryRequest;

class InventoryController extends Controller
{
    protected InventoryService $service;

    public function __construct(InventoryService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return redirect()->route('login')->with('error', 'Please login as admin first.');
        }
        $data = $this->service->listInventory();

        return view('admin.inventory', $data);
    }

    public function pasteurize(PasteurizeInventoryRequest $request)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        try {
            $adminId = Session::get('account_id');
            $result = $this->service->pasteurize($request->input('donation_ids'), $request->input('notes'), $adminId);

            return response()->json([
                'success' => true,
                'message' => "Successfully created {$result['batch']->batch_number} with {$result['donations_count']} donations totaling {$result['total_volume']}ml",
                'batch_id' => $result['batch']->batch_id,
                'batch_number' => $result['batch']->batch_number,
                'simple_batch_name' => $result['simple_batch_name'],
                'donations_count' => $result['donations_count'],
                'total_volume' => $result['total_volume']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getBatchDetails($batchId)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $batch = $this->service->getBatchDetails((int) $batchId);

        return response()->json([
            'batch_id' => $batch->batch_id,
            'batch_number' => $batch->batch_number,
            'total_volume' => $batch->total_volume,
            'available_volume' => $batch->available_volume,
            'date_pasteurized' => $batch->date_pasteurized,
            'time_pasteurized' => $batch->time_pasteurized,
            'notes' => $batch->notes,
            'donations' => $batch->donations->map(function($donation) {
                return [
                    'breastmilk_donation_id' => $donation->breastmilk_donation_id,
                    'user' => $donation->user ? [
                        'first_name' => $donation->user->first_name,
                        'last_name' => $donation->user->last_name
                    ] : null,
                    'donation_method' => $donation->donation_method,
                    'number_of_bags' => $donation->number_of_bags,
                    'bag_volumes' => $donation->formatted_bag_volumes,
                    'total_volume' => $donation->total_volume,
                    'donation_date' => $donation->donation_date ? $donation->donation_date->format('Y-m-d') : null,
                    'scheduled_pickup_date' => $donation->scheduled_pickup_date ? $donation->scheduled_pickup_date->format('Y-m-d') : null,
                    'donation_time' => $donation->donation_time,
                    'scheduled_pickup_time' => $donation->scheduled_pickup_time
                ];
            })
        ]);
    }

    public function getInventoryStats()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $stats = $this->service->getStats();
        return response()->json($stats);
    }
}
