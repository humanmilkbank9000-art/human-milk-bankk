<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use App\Models\HealthScreening;
use Illuminate\Support\Facades\Session;
use App\Notifications\SystemAlert;
use App\Models\Admin;

class DonationController extends Controller
{
    public function admin_breastmilk_donation() {
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
        $pendingDonations = $pendingWalkIn->concat($pendingHomeCollection)
            ->sortBy('created_at');
            
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

        return view('admin.breastmilk-donation', compact(
            'pendingWalkIn',
            'pendingHomeCollection',
            'pendingDonations',
            'successWalkIn', 
            'scheduledHomeCollection',
            'successHomeCollection'
        ));
    }

    public function user_donate() {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user_id = Session::get('account_id');
        
        // Check health screening status
        $healthScreening = HealthScreening::where('user_id', $user_id)->latest()->first();
        
        // Get available dates for calendar highlighting
        $availableDates = \App\Models\Availability::available()
            ->future()
            ->select('available_date')
            ->distinct()
            ->orderBy('available_date')
            ->pluck('available_date')
            ->map(function($date) {
                return $date->format('Y-m-d');
            });
        
        return view('user.donate', compact('healthScreening', 'availableDates'));
    }

    // ==================== STORE DONATION ====================
    public function store(Request $request)
    {
        // Check if user is logged in
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user_id = Session::get('account_id');
        
        // Check health screening status
        $healthScreening = HealthScreening::where('user_id', $user_id)->latest()->first();
        
        if (!$healthScreening) {
            return redirect()->route('user.donate')->with('error', 'Please complete your health screening first before donating.');
        }
        
        if ($healthScreening->status !== 'accepted') {
            $message = $healthScreening->status === 'pending' 
                ? 'Your health screening is still pending approval. Please wait for admin approval before donating.'
                : 'Your health screening has been declined. You cannot donate at this time.';
            return redirect()->route('user.donate')->with('error', $message);
        }

        $donationMethod = $request->donation_method;

        if ($donationMethod === 'walk_in') {
            // Walk-in validation
            $request->validate([
                'donation_method' => 'required|in:walk_in,home_collection',
                'availability_id' => 'required|exists:admin_availability,id',
            ]);

            // Get the availability slot and mark it as booked
            $availability = \App\Models\Availability::findOrFail($request->availability_id);
            
            // Double-check availability is still available
            if ($availability->status !== 'available') {
                return redirect()->route('user.donate')->with('error', 'The selected time slot is no longer available. Please choose another time.');
            }

            // Mark availability slot as booked
            $availability->markAsBooked();

            // Create walk-in donation request (admin will fill volume details)
            Donation::create([
                'health_screening_id' => $healthScreening->health_screening_id,
                'admin_id' => 1, // Default admin
                'user_id' => $user_id,
                'donation_method' => 'walk_in',
                'status' => 'pending_walk_in',
                'availability_id' => $availability->id,
                'donation_date' => $availability->available_date,
                'donation_time' => $availability->start_time,
                // Volume fields will be filled by admin during walk-in
            ]);

            // Notify admins about new donation (walk-in)
            $admins = Admin::all();
            foreach ($admins as $admin) {
                $admin->notify(new SystemAlert('New Donation (Walk-in)', 'A user scheduled a walk-in donation.'));
            }
            $date = \Carbon\Carbon::parse($availability->available_date);
            return redirect()->route('user.donate')->with('success', 'Walk-in appointment scheduled successfully! Please visit the center on ' . $date->format('M d, Y') . ' at ' . $availability->formatted_time);

        } elseif ($donationMethod === 'home_collection') {
            // Home collection validation
            $request->validate([
                'donation_method' => 'required|in:walk_in,home_collection',
                'number_of_bags' => 'required|integer|min:1',
                'bag_volumes' => 'required|array|min:1',
                'bag_volumes.*' => 'required|numeric|min:0.01',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);

            // Validate that number of bag volumes matches number of bags
            if (count($request->bag_volumes) !== (int)$request->number_of_bags) {
                return redirect()->route('user.donate')->with('error', 'Number of bag volumes must match the number of bags.');
            }

            // Calculate total volume from individual bag volumes
            $totalVolume = array_sum($request->bag_volumes);

            // Save location coordinates to user's record if provided
            if ($request->filled('latitude') && $request->filled('longitude')) {
                $user = \App\Models\User::find($user_id);
                if ($user) {
                    $user->latitude = $request->latitude;
                    $user->longitude = $request->longitude;
                    $user->save();
                }
            }

            // Create home collection request (admin will schedule pickup)
            $donation = new Donation();
            $donation->health_screening_id = $healthScreening->health_screening_id;
            $donation->admin_id = 1; // Default admin
            $donation->user_id = $user_id;
            $donation->donation_method = 'home_collection';
            $donation->status = 'pending_home_collection';
            $donation->setBagVolumes($request->bag_volumes);
            $donation->save();

            // Notify admins about new home collection request
            $admins = Admin::all();
            foreach ($admins as $admin) {
                $admin->notify(new SystemAlert('New Donation (Home Collection)', 'A user submitted a home collection donation request.'));
            }

            return redirect()->route('user.donate')->with('success', 'Home collection request submitted successfully! The admin will contact you to schedule a pickup time.');
        }

        return redirect()->route('user.donate')->with('error', 'Invalid donation method selected.');
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
    
    public function validateWalkIn(Request $request, $id)
    {
        $request->validate([
            'number_of_bags' => 'required|integer|min:1',
            'bag_volumes' => 'required|array|min:1',
            'bag_volumes.*' => 'required|numeric|min:0.01'
        ]);

        $donation = Donation::findOrFail($id);
        
        // Ensure it's a pending walk-in donation
        if ($donation->status !== 'pending_walk_in' || $donation->donation_method !== 'walk_in') {
            return response()->json(['success' => false, 'message' => 'Invalid donation status']);
        }

        // Validate that number of bag volumes matches number of bags
        if (count($request->bag_volumes) !== (int)$request->number_of_bags) {
            return response()->json(['success' => false, 'message' => 'Number of bag volumes must match the number of bags']);
        }

        // Update donation with actual values using the helper method
        $donation->setBagVolumes($request->bag_volumes);
        $donation->status = 'success_walk_in';
        $donation->save(); // Save the basic updates first
        
        // Automatically add to inventory
        $donation->addToInventory();

        // Notify user that their donation was validated
        $user = \App\Models\User::find($donation->user_id);
        if ($user) {
            $user->notify(new \App\Notifications\SystemAlert('Donation Validated', 'Your walk-in donation has been validated and added to inventory.'));
        }

        return response()->json(['success' => true, 'message' => 'Walk-in donation validated successfully']);
    }

    public function schedulePickup(Request $request, $id)
    {
        $request->validate([
            'scheduled_pickup_date' => 'required|date|after_or_equal:today',
            'scheduled_pickup_time' => 'required|date_format:H:i'
        ]);

        $donation = Donation::findOrFail($id);
        
        // Ensure it's a pending home collection
        if ($donation->status !== 'pending_home_collection' || $donation->donation_method !== 'home_collection') {
            return response()->json(['success' => false, 'message' => 'Invalid donation status']);
        }

        // Update donation with scheduled pickup details
        $donation->update([
            'scheduled_pickup_date' => $request->scheduled_pickup_date,
            'scheduled_pickup_time' => $request->scheduled_pickup_time,
            'status' => 'scheduled_home_collection'
        ]);

        // Notify user that pickup was scheduled
        $user = \App\Models\User::find($donation->user_id);
        if ($user) {
            $user->notify(new \App\Notifications\SystemAlert('Pickup Scheduled', 'Your home collection pickup has been scheduled.'));
        }

        return response()->json(['success' => true, 'message' => 'Pickup scheduled successfully']);
    }

    public function validatePickup(Request $request, $id)
    {
        $request->validate([
            'number_of_bags' => 'required|integer|min:1',
            'bag_volumes' => 'required|array|min:1',
            'bag_volumes.*' => 'required|numeric|min:0.01'
        ]);

        $donation = Donation::findOrFail($id);
        
        // Ensure it's a scheduled home collection
        if ($donation->status !== 'scheduled_home_collection' || $donation->donation_method !== 'home_collection') {
            return response()->json(['success' => false, 'message' => 'Invalid donation status']);
        }

        // Validate that number of bag volumes matches number of bags
        if (count($request->bag_volumes) !== (int)$request->number_of_bags) {
            return response()->json(['success' => false, 'message' => 'Number of bag volumes must match the number of bags']);
        }

        // Update donation with actual pickup values using the helper method
        $donation->setBagVolumes($request->bag_volumes);
        $donation->status = 'success_home_collection';
        $donation->save(); // Save the basic updates first
        
        // Automatically add to inventory
        $donation->addToInventory();

        // Notify user that pickup has been validated
        $user = \App\Models\User::find($donation->user_id);
        if ($user) {
            $user->notify(new \App\Notifications\SystemAlert('Pickup Validated', 'Your home collection has been validated and added to inventory.'));
        }

        return response()->json(['success' => true, 'message' => 'Pickup validated successfully']);
    }
    // ==================== MY REQUESTS PAGE ====================
    public function user_my_requests()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user_id = Session::get('account_id');
        $requests = \App\Models\BreastmilkRequest::where('user_id', $user_id)
            ->with(['infant', 'availability'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.my-requests', compact('requests'));
    }
}
