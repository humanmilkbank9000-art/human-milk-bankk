<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use App\Models\User;
use App\Models\HealthScreening;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\SystemAlert;
use App\Models\Admin;
use App\Services\DonationService;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\ValidateDonationRequest;
use App\Http\Requests\SchedulePickupRequest;

class DonationController extends Controller
{
    protected DonationService $service;

    public function __construct(DonationService $service)
    {
        $this->service = $service;
    }
    public function admin_breastmilk_donation(Request $request) {
        // Get donation type filter for pending donations
        $donationType = $request->get('donation_type', 'all');
        $status = $request->query('status', 'pending');
        
        // Get donations by status for different sections with pagination
        $pendingWalkIn = Donation::pendingWalkIn()
            ->with(['user', 'availability'])
            ->orderBy('donation_date')
            ->paginate(10, ['*'], 'pending_walkin_page')
            ->appends(['status' => $status, 'donation_type' => $donationType]);
            
        $pendingHomeCollection = Donation::pendingHomeCollection()
            ->with(['user'])
            ->orderBy('created_at')
            ->paginate(10, ['*'], 'pending_home_page')
            ->appends(['status' => $status, 'donation_type' => $donationType]);
            
        // For unified pending view, we'll use collection pagination
        $allPendingQuery = Donation::where('status', 'pending')
            ->with(['user', 'availability']);
        
        // Filter pending donations based on donation type
        if ($donationType === 'walk_in') {
            $allPendingQuery->where('donation_method', 'walk_in');
        } elseif ($donationType === 'home_collection') {
            $allPendingQuery->where('donation_method', 'home_collection');
        }
        
        $pendingDonations = $allPendingQuery
            ->orderBy('created_at')
            ->paginate(10, ['*'], 'pending_page')
            ->appends(['status' => $status, 'donation_type' => $donationType]);
            
        $successWalkIn = Donation::successWalkIn()
            ->with(['user'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'success_walkin_page')
            ->appends(['status' => $status]);
            
        $scheduledHomeCollection = Donation::scheduledHomeCollection()
            ->with(['user'])
            ->orderBy('scheduled_pickup_date')
            ->paginate(10, ['*'], 'scheduled_page')
            ->appends(['status' => $status]);
            
        $successHomeCollection = Donation::successHomeCollection()
            ->with(['user'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'success_home_page')
            ->appends(['status' => $status]);

        // Declined donations for the Declined tab
        $declinedDonations = Donation::where('status', 'declined')
            ->with(['user'])
            ->orderByDesc('declined_at')
            ->paginate(10, ['*'], 'declined_page')
            ->appends(['status' => $status]);
        $declinedCount = Donation::where('status', 'declined')->count();

        return view('admin.breastmilk-donation', compact(
            'pendingWalkIn',
            'pendingHomeCollection',
            'pendingDonations',
            'successWalkIn', 
            'scheduledHomeCollection',
            'successHomeCollection',
            'status',
            'declinedDonations',
            'declinedCount'
        ));
    }

    public function user_donate() {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user_id = Session::get('account_id');
        
        // Check health screening status
        $healthScreening = HealthScreening::where('user_id', $user_id)->latest()->first();
        
        // Get available dates via unified service for parity with admin
        $availableDates = app(\App\Services\AvailabilityService::class)->listAvailableDates();
        
        return view('user.donate', compact('healthScreening', 'availableDates'));
    }

    // ==================== STORE DONATION ====================
    public function store(StoreDonationRequest $request)
    {
        // Check if user is logged in
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Please login first.'], 401);
            }
            return redirect()->route('login')->with('error', 'Please login first.');
        }
        $user_id = Session::get('account_id');

        try {
            $result = $this->service->createDonation($request->validated(), $user_id);

            if ($request->input('donation_method') === 'walk_in') {
                $date = \Carbon\Carbon::parse($result->available_date);
                $message = 'Walk-in appointment scheduled successfully! Please visit the center on ' . $date->format('M d, Y');
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => true, 'message' => $message]);
                }
                return redirect()->route('user.pending')->with('success', $message);
            }

            $message = 'Home collection request submitted successfully! The admin will contact you to schedule a pickup time.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            return redirect()->route('user.pending')->with('success', $message);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return redirect()->route('user.donate')->with('error', $e->getMessage());
        }
    }

    public function user_pending_donation() {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user_id = Session::get('account_id');
        
        // Get all pending donations for this user
        $pendingDonations = Donation::where('user_id', $user_id)
            ->whereIn('status', ['pending_walk_in', 'pending_home_collection', 'scheduled_home_collection'])
            ->with(['availability'])
            ->orderBy('created_at', 'desc')
            ->get();
        // Also load available dates for rescheduling walk-in
        $availableDates = app(\App\Services\AvailabilityService::class)->listAvailableDates();

        return view('user.pending-donation', compact('pendingDonations', 'availableDates'));
    }

    public function user_my_donation_history() {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user_id = Session::get('account_id');
        
        // Get all completed donations for this user (success walk-in and success home collection)
        $completedDonations = Donation::where('user_id', $user_id)
            ->whereIn('status', ['success_walk_in', 'success_home_collection'])
            ->with(['availability'])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return view('user.my-donation-history', compact('completedDonations'));
    }

    // ==================== ADMIN ACTIONS ====================
    
    public function validateWalkIn(ValidateDonationRequest $request, $id)
    {
        try {
            $donation = Donation::findOrFail($id);
            $this->service->validateWalkIn($donation, $request->validated());
            return response()->json(['success' => true, 'message' => 'Walk-in donation validated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function schedulePickup(SchedulePickupRequest $request, $id)
    {
        try {
            $donation = Donation::findOrFail($id);
            $this->service->schedulePickup($donation, $request->validated());
            return response()->json(['success' => true, 'message' => 'Pickup scheduled successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function reschedulePickup(SchedulePickupRequest $request, $id)
    {
        try {
            $donation = Donation::findOrFail($id);
            $this->service->reschedulePickup($donation, $request->validated());
            return response()->json(['success' => true, 'message' => 'Pickup rescheduled successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function validatePickup(ValidateDonationRequest $request, $id)
    {
        try {
            $donation = Donation::findOrFail($id);
            $this->service->validatePickup($donation, $request->validated());
            return response()->json(['success' => true, 'message' => 'Pickup validated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Return lifestyle / screening answers (10-question checklist) for a donation.
     * Used by the admin Schedule Home Collection Pickup modal (tab 3).
     */
    public function screening($id)
    {
        // Restrict to admin
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $donation = Donation::with(['healthScreening','user'])->find($id);
        if (!$donation) {
            return response()->json(['error' => 'Donation not found'], 404);
        }

        $questions = [];

        // Lifestyle checklist answers (10) taken directly from the Donation record
        $lifestyle = [
            'good_health' => $donation->good_health,
            'no_smoking' => $donation->no_smoking,
            'no_medication' => $donation->no_medication,
            'no_alcohol' => $donation->no_alcohol,
            'no_fever' => $donation->no_fever,
            'no_cough_colds' => $donation->no_cough_colds,
            'no_breast_infection' => $donation->no_breast_infection,
            'followed_hygiene' => $donation->followed_hygiene,
            'followed_labeling' => $donation->followed_labeling,
            'followed_storage' => $donation->followed_storage,
        ];

        // If this donation has no captured lifestyle answers, attempt to reuse the user's latest
        // previous donation that contains lifestyle data so admins still see what the donor answered.
        $allNull = true;
        foreach ($lifestyle as $v) { if (!is_null($v) && trim((string)$v) !== '') { $allNull = false; break; } }
        $fromPrevious = false;
        $previousDate = null;
        if ($allNull && $donation->user_id) {
            $prev = Donation::where('user_id', $donation->user_id)
                ->where('breastmilk_donation_id', '!=', $donation->breastmilk_donation_id)
                ->where(function($q){
                    $q->whereNotNull('good_health')
                      ->orWhereNotNull('no_smoking')
                      ->orWhereNotNull('no_medication')
                      ->orWhereNotNull('no_alcohol')
                      ->orWhereNotNull('no_fever')
                      ->orWhereNotNull('no_cough_colds')
                      ->orWhereNotNull('no_breast_infection')
                      ->orWhereNotNull('followed_hygiene')
                      ->orWhereNotNull('followed_labeling')
                      ->orWhereNotNull('followed_storage');
                })
                ->latest('created_at')
                ->first();
            if ($prev) {
                $lifestyle = [
                    'good_health' => $prev->good_health,
                    'no_smoking' => $prev->no_smoking,
                    'no_medication' => $prev->no_medication,
                    'no_alcohol' => $prev->no_alcohol,
                    'no_fever' => $prev->no_fever,
                    'no_cough_colds' => $prev->no_cough_colds,
                    'no_breast_infection' => $prev->no_breast_infection,
                    'followed_hygiene' => $prev->followed_hygiene,
                    'followed_labeling' => $prev->followed_labeling,
                    'followed_storage' => $prev->followed_storage,
                ];
                $fromPrevious = true;
                $previousDate = $prev->created_at ? $prev->created_at->format('M d, Y') : null;
            }
        }

        // Use the exact same wording as shown to the user in the Lifestyle Checklist modal
        $questionsMap = [
            'good_health' => 'I am in good health',
            'no_smoking' => 'I do not smoke',
            'no_medication' => 'I am not taking medication or herbal supplements',
            'no_alcohol' => 'I am not consuming alcohol',
            'no_fever' => 'I have not had a fever',
            'no_cough_colds' => 'I have not had cough or colds',
            'no_breast_infection' => 'I have no breast infections',
            'followed_hygiene' => 'I have followed all hygiene instructions',
            'followed_labeling' => 'I have followed all labeling instructions',
            'followed_storage' => 'I have followed all storage instructions',
        ];

        $normalize = function($raw) {
            if (is_null($raw)) return 'N/A';
            $s = trim(strtolower((string)$raw));
            if ($s === '') return 'N/A';
            $yesVals = ['1','yes','y','true','t','on','checked','yes'];
            $noVals  = ['0','no','n','false','f','off','no'];
            if (in_array($s, $yesVals, true)) return 'Yes';
            if (in_array($s, $noVals, true)) return 'No';
            return strtoupper((string)$raw);
        };

        foreach ($questionsMap as $key => $label) {
            $raw = $lifestyle[$key] ?? null;
            $answer = $normalize($raw);
            $item = [ 'key' => $key, 'label' => $label, 'answer' => $answer ];
            if ($fromPrevious && $previousDate) {
                $item['details'] = 'From previous donation on ' . $previousDate;
            }
            $questions[] = $item;
        }

        return response()->json([
            'donation_id' => $donation->breastmilk_donation_id,
            'user_id' => $donation->user_id,
            'questions' => $questions,
        ]);
    }

    /**
     * Admin endpoint to update/repair Lifestyle checklist answers on a donation.
     */
    public function updateLifestyle(Request $request, $id)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $donation = Donation::find($id);
        if (!$donation) {
            return response()->json(['success' => false, 'message' => 'Donation not found'], 404);
        }

        $data = $request->validate([
            'good_health' => 'nullable|in:YES,NO',
            'no_smoking' => 'nullable|in:YES,NO',
            'no_medication' => 'nullable|in:YES,NO',
            'no_alcohol' => 'nullable|in:YES,NO',
            'no_fever' => 'nullable|in:YES,NO',
            'no_cough_colds' => 'nullable|in:YES,NO',
            'no_breast_infection' => 'nullable|in:YES,NO',
            'followed_hygiene' => 'nullable|in:YES,NO',
            'followed_labeling' => 'nullable|in:YES,NO',
            'followed_storage' => 'nullable|in:YES,NO',
        ]);

        foreach ($data as $k => $v) {
            $donation->{$k} = $v; // save as provided (YES/NO)
        }
        $donation->save();

        return response()->json(['success' => true, 'message' => 'Lifestyle answers updated.']);
    }

    /**
     * Assist Walk-in Donation: Admin creates a walk-in donation and validates it using
     * the same service method as normal walk-in validation to ensure consistent behavior.
     */
    public function assistWalkIn(Request $request)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            return redirect()->route('login')->with('error', 'Please login as admin first.');
        }

        $validated = $request->validate([
            'assist_option' => 'required|in:no_account_direct_record,record_to_existing_user,milk_letting_activity',
            'existing_user_id' => 'nullable|integer',
            'donor_first_name' => 'required|string|max:255',
            'donor_last_name' => 'required|string|max:255',
            'donor_contact' => ['required','string','max:20','regex:/^09\d{9}$/'],
            'donor_address' => 'nullable|string|max:1000',
            'number_of_bags' => 'required|integer|min:1|max:50',
            'bag_volumes' => 'required|array|min:1',
            'bag_volumes.*' => 'required|numeric|min:0.01',
            'donation_date' => 'nullable|date',
            'donation_time' => 'nullable|string|max:10',
        ]);

        try {
            DB::beginTransaction();

            // Resolve user by assist option
            if ($validated['assist_option'] === 'record_to_existing_user') {
                $userId = (int)($validated['existing_user_id'] ?? 0);
                if ($userId <= 0) {
                    DB::rollBack();
                    $msg = 'Please select an existing user from the list.';
                    if ($request->ajax() || $request->wantsJson()) return response()->json(['success'=>false,'message'=>$msg],422);
                    return back()->withInput()->with('error', $msg);
                }
                $user = User::find($userId);
                if (!$user) {
                    DB::rollBack();
                    $msg = 'Selected user not found.';
                    if ($request->ajax() || $request->wantsJson()) return response()->json(['success'=>false,'message'=>$msg],404);
                    return back()->withInput()->with('error', $msg);
                }
            } else {
                // Previous behavior: link by contact if exists else create minimal donor profile
                $user = User::where('contact_number', $validated['donor_contact'])->first();
                if ($user) {
                    $fnameMatch = trim(strtolower($user->first_name)) === trim(strtolower($validated['donor_first_name']));
                    $lnameMatch = trim(strtolower($user->last_name)) === trim(strtolower($validated['donor_last_name']));
                    if (!$fnameMatch || !$lnameMatch) {
                        DB::rollBack();
                        $msg = 'This contact number is registered to ' . trim(($user->first_name ?? '').' '.($user->last_name ?? '')) . '. Please use that exact name or a different contact.';
                        if ($request->ajax() || $request->wantsJson()) return response()->json(['success'=>false,'message'=>$msg],422);
                        return back()->withInput()->with('error', $msg);
                    }
                } else {
                    // Create a minimal donor profile
                    $user = User::create([
                        'contact_number' => $validated['donor_contact'],
                        'password' => bcrypt('temporary_password_' . time()),
                        'first_name' => $validated['donor_first_name'],
                        'middle_name' => null,
                        'last_name' => $validated['donor_last_name'],
                        'address' => $validated['donor_address'] ?? 'Walk-in (not provided)',
                        'latitude' => null,
                        'longitude' => null,
                        'date_of_birth' => now()->toDateString(),
                        'age' => 0,
                        'sex' => 'female',
                        'user_type' => 'donor',
                    ]);
                }
            }

            // Do NOT block on health screening; auto-create a minimal accepted screening if missing
            // IMPORTANT: Our schema requires many non-null enum fields. Provide safe defaults.
            $health = HealthScreening::where('user_id', $user->user_id)->latest()->first();
            if (!$health || $health->status !== 'accepted') {
                $defaults = [
                    'user_id' => $user->user_id,
                    'status' => 'accepted',
                    'date_accepted' => now(),
                    'admin_notes' => 'Paper-based health screening; auto-created via Assist Walk-in Donation.',
                    // Basic info required by schema
                    'civil_status' => 'single',
                    'occupation' => 'Not specified (paper screening)',
                    'type_of_donor' => 'community',
                    // Medical history defaults (all NO)
                    'medical_history_01' => 'no',
                    'medical_history_02' => 'no',
                    'medical_history_03' => 'no',
                    'medical_history_04' => 'no',
                    'medical_history_05' => 'no',
                    'medical_history_06' => 'no',
                    'medical_history_07' => 'no',
                    'medical_history_08' => 'no',
                    'medical_history_09' => 'no',
                    'medical_history_10' => 'no',
                    'medical_history_11' => 'no',
                    'medical_history_12' => 'no',
                    'medical_history_13' => 'no',
                    'medical_history_14' => 'no',
                    'medical_history_15' => 'no',
                    // Sexual history defaults (all NO)
                    'sexual_history_01' => 'no',
                    'sexual_history_02' => 'no',
                    'sexual_history_03' => 'no',
                    'sexual_history_04' => 'no',
                    // Donor infant history defaults (all NO)
                    'donor_infant_01' => 'no',
                    'donor_infant_02' => 'no',
                    'donor_infant_03' => 'no',
                    'donor_infant_04' => 'no',
                    'donor_infant_05' => 'no',
                ];

                // Create with defaults; nullable *_details fields are omitted
                $health = HealthScreening::create($defaults);
            }

            // Create donation first as pending walk-in (reuse the same validation flow)
            $adminId = Session::get('account_id');
            $donation = new Donation();
            $donation->health_screening_id = $health->health_screening_id;
            $donation->admin_id = $adminId;
            $donation->user_id = $user->user_id;
            $donation->donation_method = 'walk_in';
            $donation->status = 'pending_walk_in';
            $donation->donation_date = $validated['donation_date'] ?? now()->toDateString();
            $donation->donation_time = $validated['donation_time'] ?? now()->format('H:i');
            $donation->assist_option = $validated['assist_option'];

            $donation->save();

            // Reuse the same validation logic as standard walk-in validation
            $this->service->validateWalkIn($donation, [
                'number_of_bags' => (int)$validated['number_of_bags'],
                'bag_volumes' => $validated['bag_volumes'],
            ]);

            DB::commit();

            $message = 'Walk-in donation recorded and added to inventory.';
            if (!empty($donation->assist_option)) {
                $map = [
                    'no_account_direct_record' => 'No account or direct record',
                    'record_to_existing_user' => 'Recorded to existing user',
                    'milk_letting_activity' => 'Milk letting activity'
                ];
                $human = $map[$donation->assist_option] ?? $donation->assist_option;
                $message .= ' (Assist Option: ' . $human . ')';
            }
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            // Redirect to Inventory Unpasteurized or to Walk-in Success tab; choose donations page success tab for continuity
            return redirect()->route('admin.donation', ['status' => 'success_walk_in'])->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Assist walk-in donation error: ' . $e->getMessage());
            $msg = 'Failed to record walk-in donation: ' . $e->getMessage();
            if ($request->ajax() || $request->wantsJson()) return response()->json(['success'=>false,'message'=>$msg],500);
            return back()->withInput()->with('error', $msg);
        }
    }
    
    /**
     * Decline a pending donation (walk-in or home collection)
     */
    public function decline($id)
    {
        try {
            $donation = Donation::findOrFail($id);
            if (!in_array($donation->status, ['pending_walk_in', 'pending_home_collection', 'scheduled_home_collection'])) {
                throw new \RuntimeException('Only pending or scheduled donations can be declined.');
            }

            $reason = trim(request('reason', ''));
            if ($reason === '') {
                throw new \RuntimeException('Please provide a reason for declining this donation.');
            }

            $donation->status = 'declined';
            $donation->decline_reason = $reason;
            $donation->declined_at = now();
            $donation->save();

            // Optional: notify user
            $user = \App\Models\User::find($donation->user_id);
            if ($user) {
                $user->notify(new \App\Notifications\SystemAlert('Donation Declined', 'Your donation has been declined by the admin.'));
            }

            return response()->json(['success' => true, 'message' => 'Donation declined successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Show donation details
     */
    public function show($id)
    {
        try {
            $donation = Donation::with(['user', 'availability'])
                ->findOrFail($id);

            // Parse bag details if they exist
            $bagDetails = [];
            if ($donation->bag_details) {
                $bagDetails = is_string($donation->bag_details) 
                    ? json_decode($donation->bag_details, true) 
                    : $donation->bag_details;
            }
            if (empty($bagDetails) && !empty($donation->individual_bag_volumes)) {
                // Build minimal bag details so the modal shows rows for walk-in validations
                $timeStr = $donation->donation_time ? \Carbon\Carbon::parse($donation->donation_time)->format('g:i A') : null;
                $dateStr = $donation->donation_date ? $donation->donation_date->format('M d, Y') : null;
                $bagDetails = [];
                foreach ((array)$donation->individual_bag_volumes as $idx => $vol) {
                    $bagDetails[] = [
                        'bag_number' => ($idx + 1),
                        'time' => $timeStr,
                        'date' => $dateStr,
                        'volume' => $vol,
                        'storage_location' => null,
                        'temperature' => null,
                        'collection_method' => $donation->donation_method === 'walk_in' ? 'Walk-in' : null,
                    ];
                }
            }

            // Determine the display date and time (prefer donation_date/time, fallback to scheduled)
            $displayDate = 'N/A';
            if ($donation->donation_date) {
                $displayDate = $donation->donation_date->format('M d, Y');
            } elseif ($donation->scheduled_pickup_date) {
                $displayDate = $donation->scheduled_pickup_date->format('M d, Y');
            }

            $displayTime = 'N/A';
            if ($donation->donation_time) {
                $displayTime = \Carbon\Carbon::parse($donation->donation_time)->format('g:i A');
            } elseif ($donation->scheduled_pickup_time) {
                $displayTime = \Carbon\Carbon::parse($donation->scheduled_pickup_time)->format('g:i A');
            } elseif ($donation->availability) {
                $displayTime = $donation->availability->formatted_time ?? 'N/A';
            }

            return response()->json([
                'success' => true,
                'donation' => [
                    'id' => $donation->breastmilk_donation_id,
                    'donor_name' => ($donation->user->first_name ?? '') . ' ' . ($donation->user->last_name ?? ''),
                    'contact' => $donation->user->contact_number ?? $donation->user->phone ?? 'N/A',
                    'address' => $donation->user->address ?? 'Not provided',
                    'latitude' => $donation->user->latitude ?? null,
                    'longitude' => $donation->user->longitude ?? null,
                    'donation_method' => $donation->donation_method,
                    'donation_date' => $displayDate,
                    'donation_time' => $displayTime,
                    'first_expression_date' => $donation->first_expression_date ? \Carbon\Carbon::parse($donation->first_expression_date)->format('M d, Y') : null,
                    'last_expression_date' => $donation->last_expression_date ? \Carbon\Carbon::parse($donation->last_expression_date)->format('M d, Y') : null,
                    'number_of_bags' => $donation->number_of_bags ?? 0,
                    'total_volume' => $donation->formatted_total_volume ?? 'N/A',
                    'bag_details' => $bagDetails,
                    'status' => $donation->status,
                    'updated_at' => $donation->updated_at ? $donation->updated_at->format('M d, Y g:i A') : 'N/A',
                ]
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Donation show error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load donation details.'], 500);
        }
    }
    
    // Archive/restore endpoints removed per requirements
    // ==================== MY REQUESTS PAGE ====================
    public function user_my_requests()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user_id = Session::get('account_id');
        $requests = \App\Models\BreastmilkRequest::where('user_id', $user_id)
            ->with(['infant', 'availability', 'dispensedMilk.sourceDonations.user', 'dispensedMilk.sourceBatches'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.my-requests', compact('requests'));
    }

    // ==================== USER ACTIONS ON PENDING DONATIONS ====================
    /**
     * Allow a user to reschedule their pending walk-in donation to another available date.
     */
    public function userRescheduleWalkIn(Request $request, $id)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Please login first.'], 401);
            }
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $userId = Session::get('account_id');

        $validated = $request->validate([
            'availability_id' => 'required|integer|exists:admin_availability,id',
            'appointment_date' => 'required|date',
        ]);

        try {
            $donation = Donation::where('breastmilk_donation_id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            if ($donation->status !== 'pending_walk_in' || $donation->donation_method !== 'walk_in') {
                throw new \RuntimeException('Only pending walk-in donations can be rescheduled.');
            }

            // Ensure selected availability is still available and bookable for the chosen date
            $availability = \App\Models\Availability::where('id', $validated['availability_id'])
                ->where('available_date', $validated['appointment_date'])
                ->available()
                ->future()
                ->first();

            if (!$availability) {
                throw new \RuntimeException('The selected date is no longer available. Please choose another.');
            }

            // Update the donation with the new date (we do not mark availability as booked per current design)
            $donation->availability_id = $availability->id;
            $donation->donation_date = $availability->available_date;
            // Do NOT write human-friendly strings like "9:00 AM" into a TIME column.
            // Leave donation_time unchanged; UI displays Availability::formatted_time when availability exists.
            $donation->save();

            // Optional: notify admins of reschedule
            try {
                $admins = \App\Models\Admin::all();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\SystemAlert('Donation Rescheduled (Walk-in)', 'A user rescheduled their walk-in donation.'));
                }
            } catch (\Throwable $e) {
                // Non-fatal
            }

            $msg = 'Your walk-in appointment was rescheduled to ' . \Carbon\Carbon::parse($availability->available_date)->format('M d, Y') . '.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->route('user.pending')->with('success', $msg);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return redirect()->route('user.pending')->with('error', $e->getMessage());
        }
    }

    /**
     * Allow a user to cancel their pending donation (walk-in or home collection).
     */
    public function userCancelDonation(Request $request, $id)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Please login first.'], 401);
            }
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $userId = Session::get('account_id');

        try {
            $donation = Donation::where('breastmilk_donation_id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            if (!in_array($donation->status, ['pending_walk_in', 'pending_home_collection', 'scheduled_home_collection'])) {
                throw new \RuntimeException('Only pending or scheduled donations can be canceled.');
            }

            $donation->status = 'canceled';
            $donation->decline_reason = 'Canceled by user';
            $donation->declined_at = now();
            $donation->save();

            // Optional: notify admins
            try {
                $admins = \App\Models\Admin::all();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\SystemAlert('Donation Canceled', 'A user canceled their pending donation request.'));
                }
            } catch (\Throwable $e) {
                // ignore
            }

            $msg = 'Your donation request has been canceled.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->route('user.pending')->with('success', $msg);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return redirect()->route('user.pending')->with('error', $e->getMessage());
        }
    }
}
