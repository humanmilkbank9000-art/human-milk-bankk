<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BreastmilkRequest;
use App\Models\Infant;
use App\Models\Availability;
use App\Models\Donation;
use App\Models\PasteurizationBatch;
use App\Models\DispensedMilk;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SystemAlert;
use App\Models\Admin;
use App\Services\BreastmilkRequestService;
use App\Http\Requests\StoreBreastmilkRequestRequest;
use App\Http\Requests\ApproveBreastmilkRequest;
use App\Http\Requests\DispenseBreastmilkRequest;
use App\Http\Requests\AdminNoteRequest;

class BreastmilkRequestController extends Controller
{
    protected BreastmilkRequestService $service;

    public function __construct(BreastmilkRequestService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $userId = Session::get('account_id');

        // Get user's infants
        $infants = Infant::where('user_id', $userId)->get();
        
        // Get available dates for appointment calendar
        $availableDates = Availability::available()
            ->future()
            ->pluck('available_date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->unique()
            ->values()
            ->toArray();

        return view('user.breastmilk-request', compact('infants', 'availableDates'));
    }

    public function getInfantInfo($infantId)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = Session::get('account_id');

        $infant = Infant::where('infant_id', $infantId)
            ->where('user_id', $userId)
            ->first();

        if (!$infant) {
            return response()->json(['error' => 'Infant not found'], 404);
        }

        return response()->json([
            'infant_id' => $infant->infant_id,
            'full_name' => $infant->first_name . ' ' . ($infant->middle_name ? $infant->middle_name . ' ' : '') . $infant->last_name,
            'sex' => ucfirst($infant->sex),
            'date_of_birth' => Carbon::parse($infant->date_of_birth)->format('M d, Y'),
            'age_months' => $infant->getCurrentAgeInMonths(),
            'birth_weight' => $infant->birth_weight . ' kg'
        ]);
    }

    public function store(StoreBreastmilkRequestRequest $request)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first.');
        }
        $userId = Session::get('account_id');

        // Verify the infant belongs to the user
        $infant = Infant::where('infant_id', $request->infant_id)
            ->where('user_id', $userId)
            ->first();

        if (!$infant) {
            return redirect()->back()->with('error', 'Invalid infant selection.');
        }

        try {
            $result = $this->service->createRequest(array_merge($request->validated(), ['prescription' => $request->file('prescription')]), $userId);

            $breastmilkRequest = $result['request'];
            $availability = $result['availability'];

            return redirect()->route('user.my-requests')
                ->with('success', 'Breastmilk request submitted successfully! Your appointment is scheduled for ' . 
                    $availability->formatted_date . ' at ' . $availability->formatted_time . '. Please bring the original prescription.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function myRequests()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $userId = Session::get('account_id');

        $requests = BreastmilkRequest::where('user_id', $userId)
            ->with(['infant', 'availability'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.my-breastmilk-requests', compact('requests'));
    }

    public function admin_breastmilk_request()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return redirect()->route('login')->with('error', 'Please login as admin first.');
        }

        // Get all breastmilk requests organized by status
        $pendingRequests = BreastmilkRequest::where('status', 'pending')
            ->with(['user', 'infant', 'availability'])
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedRequests = BreastmilkRequest::where('status', 'approved')
            ->with(['user', 'infant', 'availability'])
            ->orderBy('approved_at', 'desc')
            ->get();

        $dispensedRequests = BreastmilkRequest::where('status', 'dispensed')
            ->with(['user', 'infant', 'availability', 'dispensedMilk'])
            ->orderBy('dispensed_at', 'desc')
            ->get();

        $declinedRequests = BreastmilkRequest::where('status', 'declined')
            ->with(['user', 'infant', 'availability'])
            ->orderBy('declined_at', 'desc')
            ->get();

        return view('admin.breastmilk-request', compact('pendingRequests', 'approvedRequests', 'dispensedRequests', 'declinedRequests'));
    }

    /**
     * Display prescription image
     */
    public function showPrescription($requestId)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request = BreastmilkRequest::findOrFail($requestId);
        
        if (!$request->hasPrescription()) {
            return response()->json(['error' => 'No prescription found'], 404);
        }

        $base64Data = $request->getPrescriptionAsBase64();
        return response()->json([
            'image' => 'data:' . $request->prescription_mime_type . ';base64,' . $base64Data,
            'filename' => $request->prescription_filename
        ]);
    }

    /**
     * Stream prescription image for users (inline display)
     */
    public function streamPrescription($requestId)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return response('Unauthorized', 401);
        }

        $userId = Session::get('account_id');
        $requestRecord = BreastmilkRequest::where('breastmilk_request_id', $requestId)
            ->where('user_id', $userId)
            ->firstOrFail();

        if (!$requestRecord->hasPrescription()) {
            return response('Not Found', 404);
        }

        $path = $requestRecord->prescription_path;

        // Check public disk first (new files)
        if (Storage::disk('public')->exists($path)) {
            try {
                $fullPath = Storage::disk('public')->path($path);
                if (!file_exists($fullPath)) {
                    return response('Not Found', 404);
                }

                return response()->file($fullPath, [
                    'Content-Type' => $requestRecord->prescription_mime_type ?? 'application/octet-stream',
                    'Content-Disposition' => 'inline; filename="' . ($requestRecord->prescription_filename ?? 'prescription') . '"',
                ]);
            } catch (\Exception $e) {
                return response('Unable to read file', 500);
            }
        }

        // Fallback to default disk (legacy files)
        if (Storage::exists($path)) {
            try {
                $fullPath = Storage::path($path);
                if (!file_exists($fullPath)) {
                    return response('Not Found', 404);
                }

                return response()->file($fullPath, [
                    'Content-Type' => $requestRecord->prescription_mime_type ?? 'application/octet-stream',
                    'Content-Disposition' => 'inline; filename="' . ($requestRecord->prescription_filename ?? 'prescription') . '"',
                ]);
            } catch (\Exception $e) {
                return response('Unable to read file', 500);
            }
        }

        return response('Not Found', 404);
    }

    /**
     * Return prescription as base64 JSON for AJAX authenticated fetch
     */
    public function prescriptionJson($requestId)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = Session::get('account_id');
        $requestRecord = BreastmilkRequest::where('breastmilk_request_id', $requestId)
            ->where('user_id', $userId)
            ->firstOrFail();

        if (!$requestRecord->hasPrescription()) {
            return response()->json(['error' => 'No prescription found'], 404);
        }

        $base64 = $requestRecord->getPrescriptionAsBase64();
        if (!$base64) {
            return response()->json(['error' => 'Unable to read prescription'], 500);
        }

        return response()->json([
            'image' => 'data:' . ($requestRecord->prescription_mime_type ?? 'application/octet-stream') . ';base64,' . $base64,
            'filename' => $requestRecord->prescription_filename
        ]);
    }

    /**
     * Approve request and dispense milk
     */
    public function approve(ApproveBreastmilkRequest $request, $requestId)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return redirect()->route('login')->with('error', 'Please login as admin first.');
        }
        $breastmilkRequest = BreastmilkRequest::findOrFail($requestId);
        $adminId = Session::get('account_id');

        try {
            $this->service->approveAndDispense($breastmilkRequest, $request->validated(), $adminId);
            return back()->with('success', 'Request approved and dispensed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Decline request
     */
    public function decline(AdminNoteRequest $request, $requestId)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('login')->with('error', 'Please login as admin first.');
        }
        $breastmilkRequest = BreastmilkRequest::findOrFail($requestId);
        $adminId = Session::get('account_id');

        try {
            $this->service->decline($breastmilkRequest, $request->input('admin_notes'), $adminId);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Request declined successfully.']);
            }

            return back()->with('success', 'Request declined successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Dispense breastmilk for a request (new method)
     */
    public function dispense(DispenseBreastmilkRequest $request, $requestId)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $breastmilkRequest = BreastmilkRequest::findOrFail($requestId);

        if ($breastmilkRequest->status !== 'pending') {
            return response()->json(['error' => 'Request is not in pending status'], 400);
        }

        $adminId = Session::get('account_id');

        try {
            $dispensed = $this->service->dispense($breastmilkRequest, $request->validated(), $adminId);
            return response()->json(['success' => true, 'message' => 'Breastmilk dispensed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to dispense: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Reject a breastmilk request (new method)
     */
    public function reject(Request $request, $requestId)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        $breastmilkRequest = BreastmilkRequest::findOrFail($requestId);
        
        if ($breastmilkRequest->status !== 'pending') {
            return response()->json(['error' => 'Request is not in pending status'], 400);
        }

        $adminId = Session::get('account_id');

        $breastmilkRequest->update([
            'status' => 'declined',
            'admin_id' => $adminId,
            'admin_notes' => $request->input('admin_notes'),
            'declined_at' => now()
        ]);

        // Notify the request owner
        $user = \App\Models\User::find($breastmilkRequest->user_id);
        if ($user) {
            $user->notify(new SystemAlert(
                'Request Declined', 
                'Your breastmilk request #' . $breastmilkRequest->breastmilk_request_id . ' has been declined. Reason: ' . $request->input('admin_notes')
            ));
        }

        return response()->json([
            'success' => true, 
            'message' => 'Request has been rejected successfully.'
        ]);
    }

    /**
     * Deduct selected unpasteurized donations
     */
    private function deductSelectedUnpasteurizedInventory($selectedItems, $dispensedId, $volumeRequested)
    {
        $totalSelectedVolume = collect($selectedItems)->sum('volume');
        $remainingToDeduct = $volumeRequested;
        
        foreach ($selectedItems as $item) {
            if ($remainingToDeduct <= 0) break;
            
            $donation = Donation::findOrFail($item['id']);
            
            // Calculate proportional amount to deduct from this donation
            $proportionalAmount = ($item['volume'] / $totalSelectedVolume) * $volumeRequested;
            $volumeToTake = min($proportionalAmount, $remainingToDeduct, $donation->available_volume);
            
            if ($volumeToTake <= 0) continue;

            // Validate the donation is available in inventory and has enough volume
            if (!$donation->isInInventory() || $donation->available_volume < $volumeToTake) {
                throw new \Exception("Donation #{$donation->breastmilk_donation_id} does not have sufficient available volume. Available: {$donation->available_volume}ml, Requested: {$volumeToTake}ml");
            }

            // Reduce the available volume
            if (!$donation->reduceVolume($volumeToTake)) {
                throw new \Exception("Failed to reduce volume for donation #{$donation->breastmilk_donation_id}");
            }

            // Create association with dispensed milk
            $donation->dispensedMilk()->attach($dispensedId, [
                'source_type' => 'unpasteurized',
                'volume_used' => $volumeToTake
            ]);
            
            $remainingToDeduct -= $volumeToTake;
        }
    }

    /**
     * Deduct selected pasteurized batches
     */
    private function deductSelectedPasteurizedInventory($selectedItems, $dispensedId, $volumeRequested)
    {
        $totalSelectedVolume = collect($selectedItems)->sum('volume');
        $remainingToDeduct = $volumeRequested;
        
        foreach ($selectedItems as $item) {
            if ($remainingToDeduct <= 0) break;
            
            $batch = PasteurizationBatch::findOrFail($item['id']);
            
            // Calculate proportional amount to deduct from this batch
            $proportionalAmount = ($item['volume'] / $totalSelectedVolume) * $volumeRequested;
            $volumeToTake = min($proportionalAmount, $remainingToDeduct, $batch->available_volume);
            
            if ($volumeToTake <= 0) continue;

            // Validate the batch is available and has enough volume
            if ($batch->status !== 'active' || $batch->available_volume < $volumeToTake) {
                throw new \Exception("Batch {$batch->batch_number} does not have sufficient volume available. Available: {$batch->available_volume}ml, Requested: {$volumeToTake}ml");
            }

            // Reduce batch volume
            if (!$batch->reduceVolume($volumeToTake)) {
                throw new \Exception("Failed to reduce volume for batch {$batch->batch_number}");
            }

            // Create association with dispensed milk
            $batch->dispensedMilk()->attach($dispensedId, [
                'source_type' => 'pasteurized',
                'volume_used' => $volumeToTake
            ]);
            
            $remainingToDeduct -= $volumeToTake;
        }
    }    /**
     * Get available inventory for dispensing selection
     */
    public function getAvailableInventory(Request $request)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $milkType = $request->get('type', $request->get('milk_type'));

        if ($milkType === 'unpasteurized') {
            $donations = Donation::where('available_volume', '>', 0)
                ->where('pasteurization_status', 'unpasteurized')
                ->whereIn('status', ['success_walk_in', 'success_home_collection'])
                ->with(['user'])
                ->orderBy('donation_date', 'asc')
                ->get()
                ->map(function ($donation) {
                    return [
                        'breastmilk_donation_id' => $donation->breastmilk_donation_id,
                        'donor_name' => ($donation->user->first_name ?? 'Anonymous') . ' ' . ($donation->user->last_name ?? ''),
                        'donation_method' => ucfirst(str_replace('_', ' ', $donation->donation_method ?? 'N/A')),
                        'available_volume' => $donation->available_volume,
                        'donation_date' => $donation->donation_date ? Carbon::parse($donation->donation_date)->format('M d, Y') : 'N/A',
                    ];
                });

            return response()->json(['donations' => $donations]);
        } elseif ($milkType === 'pasteurized') {
            $batches = PasteurizationBatch::where('status', 'active')
                ->where('available_volume', '>', 0)
                ->with(['admin'])
                ->orderBy('date_pasteurized', 'asc')
                ->get()
                ->map(function ($batch) {
                    return [
                        'batch_id' => $batch->batch_id,
                        'batch_number' => $batch->batch_number,
                        'available_volume' => $batch->available_volume,
                        'total_volume' => $batch->total_volume,
                        'date_pasteurized' => $batch->date_pasteurized ? Carbon::parse($batch->date_pasteurized)->format('M d, Y') : 'N/A',
                    ];
                });

            return response()->json(['batches' => $batches]);
        }

        return response()->json(['error' => 'Invalid milk type'], 400);
    }
    private function deductPasteurizedInventory($volumeNeeded, $dispensedId)
    {
        $batches = PasteurizationBatch::whereIn('status', ['pasteurized', 'available'])
            ->where('current_volume', '>', 0)
            ->orderBy('pasteurized_at', 'asc') // FIFO
            ->get();

        $remainingVolume = $volumeNeeded;

        foreach ($batches as $batch) {
            if ($remainingVolume <= 0) break;

            $availableVolume = $batch->current_volume;
            $volumeToTake = min($remainingVolume, $availableVolume);

            // Reduce batch volume
            $batch->reduceVolume($volumeToTake);

            // Create association with dispensed milk
            $batch->dispensedMilk()->attach($dispensedId, [
                'volume_used' => $volumeToTake,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $remainingVolume -= $volumeToTake;
        }
    }
}
