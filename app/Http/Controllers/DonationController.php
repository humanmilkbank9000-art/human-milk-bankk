<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
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
                'archived'
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
            'archivedCount'
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
