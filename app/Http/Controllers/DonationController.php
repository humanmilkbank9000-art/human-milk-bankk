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
        
        // Get donations by status for different sections
        $pendingWalkIn = Donation::pendingWalkIn()
            ->with(['user', 'availability'])
            ->orderBy('donation_date')
            ->get();
            
        $pendingHomeCollection = Donation::pendingHomeCollection()
            ->with(['user'])
            ->orderBy('created_at')
            ->get();
            
        // Combine pending donations for unified view
        $allPendingDonations = $pendingWalkIn->concat($pendingHomeCollection)
            ->sortBy('created_at');
        
        // Filter pending donations based on donation type
        if ($donationType === 'walk_in') {
            $pendingDonations = $allPendingDonations->filter(function($donation) {
                return $donation->donation_method === 'walk_in';
            });
        } elseif ($donationType === 'home_collection') {
            $pendingDonations = $allPendingDonations->filter(function($donation) {
                return $donation->donation_method === 'home_collection';
            });
        } else {
            $pendingDonations = $allPendingDonations;
        }
            
        $successWalkIn = Donation::successWalkIn()
            ->with(['user'])
            ->orderBy('updated_at', 'desc')
            ->get();
            
        $scheduledHomeCollection = Donation::scheduledHomeCollection()
            ->with(['user'])
            ->orderBy('scheduled_pickup_date')
            ->get();
            
        $successHomeCollection = Donation::successHomeCollection()
            ->with(['user'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Declined donations for the Declined tab
        $declinedDonations = Donation::where('status', 'declined')
            ->with(['user'])
            ->orderByDesc('declined_at')
            ->get();
        $declinedCount = $declinedDonations->count();

        $archivedCount = Donation::onlyTrashed()->count();

        if ($status === 'archived') {
            $archived = Donation::onlyTrashed()->with(['user', 'availability'])->get();

            // pass the same variables the view expects to avoid undefined variable errors
            return view('admin.breastmilk-donation', compact(
                'pendingWalkIn',
                'pendingHomeCollection',
                'pendingDonations',
                'successWalkIn',
                'scheduledHomeCollection',
                'successHomeCollection',
                'status',
                'archivedCount',
                'donationType',
                'archived',
                'declinedDonations',
                'declinedCount'
            ));
        }

        return view('admin.breastmilk-donation', compact(
            'pendingWalkIn',
            'pendingHomeCollection',
            'pendingDonations',
            'successWalkIn', 
            'scheduledHomeCollection',
            'successHomeCollection',
            'status',
            'archivedCount',
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
        
        return view('user.pending-donation', compact('pendingDonations'));
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

            // Find existing user by contact; ensure name consistency to avoid mis-linking
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

            $donation->save();

            // Reuse the same validation logic as standard walk-in validation
            $this->service->validateWalkIn($donation, [
                'number_of_bags' => (int)$validated['number_of_bags'],
                'bag_volumes' => $validated['bag_volumes'],
            ]);

            DB::commit();

            $message = 'Walk-in donation recorded and added to inventory.';
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
            if (!in_array($donation->status, ['pending_walk_in', 'pending_home_collection'])) {
                throw new \RuntimeException('Only pending donations can be declined.');
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
                    'donation_date' => $donation->donation_date ? $donation->donation_date->format('m/d/Y') : 'N/A',
                    'donation_time' => $donation->donation_time ? \Carbon\Carbon::parse($donation->donation_time)->format('g:i A') : ($donation->availability ? $donation->availability->formatted_time : 'N/A'),
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
    
    /**
     * Archive (soft-delete) a donation record
     */
    public function archive($id)
    {
        try {
            $donation = Donation::findOrFail($id);
            $donation->delete();

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Donation archived successfully.']);
            }

            return redirect()->back()->with('success', 'Donation archived.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Donation archive error: ' . $e->getMessage());
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['error' => 'Failed to archive donation.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to archive donation.');
        }
    }

    /**
     * Restore (unarchive) a donation
     */
    public function restore($id)
    {
        try {
            $donation = Donation::withTrashed()->findOrFail($id);
            $donation->restore();

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Donation restored successfully.']);
            }

            return redirect()->back()->with('success', 'Donation restored.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Donation restore error: ' . $e->getMessage());
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['error' => 'Failed to restore donation.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to restore donation.');
        }
    }
    // ==================== MY REQUESTS PAGE ====================
    public function user_my_requests()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user_id = Session::get('account_id');
        $requests = \App\Models\BreastmilkRequest::where('user_id', $user_id)
            ->with(['infant', 'availability', 'dispensedMilk'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.my-requests', compact('requests'));
    }
}
