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

            $timePart = !empty($availability->formatted_time) ? ' at ' . $availability->formatted_time : '';
            return redirect()->route('user.my-requests')
                ->with('success', 'Breastmilk request submitted successfully! Your appointment is scheduled for ' . 
                    $availability->formatted_date . '. Please bring the original prescription.');
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

        $status = request()->query('status', 'pending');

        $pendingRequests = BreastmilkRequest::where('status', 'pending')
            ->with(['user', 'infant', 'availability'])
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedRequests = BreastmilkRequest::where('status', 'approved')
            ->with(['user', 'infant', 'availability'])
            ->orderBy('approved_at', 'desc')
            ->get();

        $dispensedRequests = BreastmilkRequest::where('status', 'dispensed')
            ->with(['user', 'infant', 'availability', 'dispensedMilk.sourceDonations.user', 'dispensedMilk.sourceBatches'])
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

        // Prepare user details to include in the JSON response for display in admin modal
        $user = $request->user;
        $userDetails = null;
        if ($user) {
            $userDetails = [
                'full_name' => trim(($user->first_name ?? '') . ' ' . ($user->middle_name ?? '') . ' ' . ($user->last_name ?? '')),
                'contact_number' => $user->contact_number ?? null,
                'address' => $user->address ?? null,
            ];
        }

        return response()->json([
            'image' => 'data:' . $request->prescription_mime_type . ';base64,' . $base64Data,
            'filename' => $request->prescription_filename,
            'user' => $userDetails,
            'request_id' => $request->breastmilk_request_id,
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

    // Archive/restore endpoints removed per requirements

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
                            'number_of_bags' => $donation->number_of_bags,
                            'individual_bag_volumes' => $donation->individual_bag_volumes ?? []
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

    /**
     * AJAX: check if a contact number already exists in users table
     */
    public function checkContact(Request $request)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $contact = $request->get('contact');
        if (!$contact) {
            return response()->json(['error' => 'Missing contact parameter'], 400);
        }

        $user = \App\Models\User::where('contact_number', $contact)->first();
        if ($user) {
            return response()->json(['exists' => true, 'user' => [
                'user_id' => $user->user_id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'contact_number' => $user->contact_number,
            ]]);
        }

        return response()->json(['exists' => false]);
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

    public function storeAssisted(Request $request)
    {
        // Validate admin access
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        // Validate request data
        $validated = $request->validate([
            'guardian_first_name' => 'required|string|max:255',
            'guardian_last_name' => 'required|string|max:255',
            // enforce normalized mobile format (e.g., 09XXXXXXXXX) and max length
            'guardian_contact' => ['required','string','max:20','regex:/^09\d{9}$/'],
            'infant_first_name' => 'required|string|max:255',
            'infant_last_name' => 'required|string|max:255',
            'infant_date_of_birth' => 'required|date|before_or_equal:today',
            'infant_sex' => 'required|in:Male,Female',
            'infant_weight' => 'required|numeric|min:0.5|max:20',
            'medical_condition' => 'required|string',
            'prescription' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'request_date' => 'required|date',
            'milk_type' => 'required|in:unpasteurized,pasteurized',
            'admin_notes' => 'nullable|string'
            // optional fields for immediate dispensing when assisting
            ,'dispense_now' => 'nullable|in:1',
            'selected_sources_json' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Check if user exists by contact number
            $user = \App\Models\User::where('contact_number', $validated['guardian_contact'])->first();

            // If user exists but provided name does not match, prevent accidental duplicate/linking
            if ($user && (trim(strtolower($user->first_name)) !== trim(strtolower($validated['guardian_first_name'])) || trim(strtolower($user->last_name)) !== trim(strtolower($validated['guardian_last_name'])))) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'The contact number you entered is already registered to another user (Name: ' . ($user->first_name ?? '') . ' ' . ($user->last_name ?? '') . '). If you intend to link to that user, enter their exact name; otherwise use a different contact number.');
            }

            // If user doesn't exist, create a new minimal requester profile per current schema
            if (!$user) {
                $user = \App\Models\User::create([
                    'contact_number' => $validated['guardian_contact'],
                    'password' => bcrypt('temporary_password_' . time()), // Temporary password
                    'first_name' => $validated['guardian_first_name'],
                    'middle_name' => null,
                    'last_name' => $validated['guardian_last_name'],
                    'address' => 'Walk-in (not provided)',
                    'latitude' => null,
                    'longitude' => null,
                    'date_of_birth' => now()->toDateString(),
                    'age' => 0,
                    'sex' => 'female', // default for guardians when not specified
                    'user_type' => 'requester',
                ]);
            }

            // Create infant record (schema requires lowercase sex and non-null age)
            $dob = \Carbon\Carbon::parse($validated['infant_date_of_birth'])->startOfDay();
            $now = \Carbon\Carbon::now()->startOfDay();
            $months = max(0, ($dob->diff($now)->y * 12) + $dob->diff($now)->m);
            $sexLower = strtolower($validated['infant_sex']); // map 'Male'/'Female' -> 'male'/'female'

            $infant = Infant::create([
                'user_id' => $user->user_id,
                'first_name' => $validated['infant_first_name'],
                'last_name' => $validated['infant_last_name'],
                'date_of_birth' => $validated['infant_date_of_birth'],
                'sex' => $sexLower,
                'age' => $months,
                'birth_weight' => $validated['infant_weight'],
            ]);

            // Handle prescription upload
            $prescriptionPath = null;
            if ($request->hasFile('prescription')) {
                $file = $request->file('prescription');
                $prescriptionPath = $file->store('prescriptions', 'public');
            }

            // Create breastmilk request (volume_requested left null; if dispensing now, it will be set during approve+dispense)
            $breastmilkRequest = BreastmilkRequest::create([
                'user_id' => $user->user_id,
                'infant_id' => $infant->infant_id,
                'request_date' => $validated['request_date'],
                'request_time' => now()->format('H:i:s'),
                'volume_requested' => null,
                'milk_type' => $validated['milk_type'],
                'prescription_path' => $prescriptionPath,
                'status' => 'pending',
                'admin_notes' => 'Medical Condition: ' . $validated['medical_condition'] . 
                    ($validated['admin_notes'] ? "\nStaff Notes: " . $validated['admin_notes'] : ''),
                'assisted_by_admin' => Session::get('account_id'), // Track which admin assisted
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            // If admin chose to dispense immediately, attempt to record dispensing
            if ($request->has('dispense_now') && $request->input('dispense_now') == '1') {
                $selectedJson = $request->input('selected_sources_json');
                $sources = [];
                if ($selectedJson) {
                    $decoded = json_decode($selectedJson, true);
                    if (is_array($decoded)) {
                        // normalize sources: expect {type,id,volume}
                        foreach ($decoded as $s) {
                            if (!empty($s['type']) && !empty($s['id']) && !empty($s['volume'])) {
                                $sources[] = [
                                    'type' => $s['type'],
                                    'id' => $s['id'],
                                    'volume' => (float)$s['volume']
                                ];
                            }
                        }
                    }
                }

                if (empty($sources)) {
                    // no sources selected — inform admin
                    return redirect()->route('admin.request')
                        ->with('warning', 'Assisted request created but no inventory sources were selected for immediate dispensing.');
                }

                // Compute total volume from selected sources
                $totalSelectedVolume = 0.0;
                foreach ($sources as $s) {
                    $totalSelectedVolume += (float) ($s['volume'] ?? 0);
                }
                if ($totalSelectedVolume <= 0) {
                    return redirect()->route('admin.request')
                        ->with('warning', 'Assisted request created but selected volumes total is zero. No dispensing performed.');
                }

                try {
                    // Use the same approval+dispense flow used by admins when approving user requests
                    $adminId = Session::get('account_id');
                    // Normalize selected sources to selected_items expected by approveAndDispense
                    $selectedItems = [];
                    foreach ($sources as $s) {
                        $si = ['id' => $s['id'], 'volume' => $s['volume']];
                        if (isset($s['bag_index'])) $si['bag_index'] = (int)$s['bag_index'];
                        $selectedItems[] = $si;
                    }

                    $payload = [
                        'volume_requested' => $totalSelectedVolume,
                        'milk_type' => $validated['milk_type'],
                        'selected_items' => $selectedItems,
                        'admin_notes' => $validated['admin_notes'] ?? null
                    ];

                    $this->service->approveAndDispense($breastmilkRequest, $payload, $adminId);

                    return redirect()->route('admin.request', ['status' => 'dispensed'])
                        ->with('success', 'Assisted request submitted and dispensed successfully for ' . $validated['guardian_first_name'] . ' ' . $validated['guardian_last_name']);
                } catch (\Exception $e) {
                    Log::error('Error dispensing during assisted request: ' . $e->getMessage());
                    // Dispense failed but request created — notify admin and redirect
                    return redirect()->route('admin.request')
                        ->with('error', 'Assisted request was created but dispensing failed: ' . $e->getMessage());
                }
            }

            // Notify all admins about the new assisted request
            $admins = Admin::all();
            foreach ($admins as $admin) {
                $admin->notify(new SystemAlert(
                    'New Assisted Walk-in Request',
                    'A new breastmilk request has been submitted by staff for ' . $validated['guardian_first_name'] . ' ' . $validated['guardian_last_name']
                ));
            }

            return redirect()->route('admin.request')
                ->with('success', 'Assisted request submitted successfully for ' . $validated['guardian_first_name'] . ' ' . $validated['guardian_last_name']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating assisted request: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit assisted request. Please try again.');
        }
    }
}
