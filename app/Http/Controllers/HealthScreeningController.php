<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HealthScreening;
use App\Models\User;
use App\Models\Infant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Notifications\SystemAlert;
use App\Models\Admin;

class HealthScreeningController extends Controller
{
    // ==================== USER ====================

    // Show user health screening form
    public function user_health_screening()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user_id = Session::get('account_id');
        $user = User::find($user_id);
        $infant = Infant::where('user_id', $user_id)->first();

        if (!$user) {
            return redirect()->route('user.register')->with('error', 'Please register first.');
        }

        $existing = HealthScreening::where('user_id', $user_id)->latest()->first();

        return view('user.health-screening', compact('user', 'infant', 'existing'));
    }

    // Store user health screening
    public function store(Request $request)
    {
        try {
            $user_id = Session::get('account_id');

            if (!$user_id) {
                return response()->json([
                    'error' => 'User not logged in'
                ], 401);
            }

            if (HealthScreening::where('user_id', $user_id)->exists()) {
                return response()->json([
                    'error' => 'You have already submitted your health screening.'
                ], 400);
            }

            // Validation rules
            $rules = [
                'civil_status'   => 'required|in:single,married,divorced,widowed',
                'occupation'     => 'required|string',
                'type_of_donor'  => 'required|in:community,private,employee,network_office_agency',
            ];

            $requiredYesNo = [
                'medical_history_01', 'medical_history_03', 'medical_history_07',
                'medical_history_09', 'medical_history_14', 'medical_history_15',
                'sexual_history_01', 'sexual_history_02', 'sexual_history_04',
                'donor_infant_01', 'donor_infant_02', 'donor_infant_03'
            ];

            foreach ($requiredYesNo as $field) {
                $rules[$field] = 'required|in:yes,no';
            }

            $optionalFields = [
                'medical_history_02', 'medical_history_04', 'medical_history_05',
                'medical_history_06', 'medical_history_08', 'medical_history_10',
                'medical_history_11', 'medical_history_12', 'medical_history_13',
                'sexual_history_03', 'donor_infant_04', 'donor_infant_05'
            ];

            foreach ($optionalFields as $field) {
                $rules[$field] = 'nullable|string';
            }

            $validated = $request->validate($rules);

            $data = array_merge($validated, [
                'user_id'       => $user_id,
                'infant_id'     => $request->infant_id,
                'status'        => 'pending',
                'date_accepted' => null,
                'date_declined' => null,
            ]);

            HealthScreening::create($data);

            // Notify admins about new health screening submission
            $admins = Admin::all();
            foreach ($admins as $admin) {
                $admin->notify(new SystemAlert('New Health Screening', 'A user submitted a health screening for review.'));
            }

            return response()->json([
                'message'  => 'Health screening submitted successfully!',
                'redirect' => route('user.health-screening')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error'  => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Health Screening submission error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while submitting your health screening. Please try again.'
            ], 500);
        }
    }

    // ==================== ADMIN ====================

    public function admin_health_screening(Request $request)
    {
        $status = $request->query('status', 'pending');

        $healthScreenings = HealthScreening::where('status', $status)
            ->with('user', 'infant')
            ->get();

        return view('admin.health-screening', compact('healthScreenings', 'status'));
    }

    public function accept($id)
    {
        $screening = HealthScreening::findOrFail($id);
        $screening->status = 'accepted';
        $screening->date_accepted = now();
        
        // Save admin comments if provided
        if (request()->has('comments') && !empty(request('comments'))) {
            $screening->admin_notes = request('comments');
        }
        
        $screening->save();

        // Notify the user
        $user = \App\Models\User::find($screening->user_id);
        if ($user) {
            $user->notify(new SystemAlert('Health Screening Accepted', 'Your health screening has been accepted. You may now donate.'));
        }

        // Check if it's an AJAX request
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Health screening accepted successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Health screening accepted.');
    }

    public function reject($id)
    {
        $screening = HealthScreening::findOrFail($id);
        $screening->status = 'declined';
        $screening->date_declined = now();
        
        // Save admin comments if provided
        if (request()->has('comments') && !empty(request('comments'))) {
            $screening->admin_notes = request('comments');
        }
        
        $screening->save();

        // Notify the user
        $user = \App\Models\User::find($screening->user_id);
        if ($user) {
            $user->notify(new SystemAlert('Health Screening Declined', 'Your health screening has been declined.'));
        }

        // Check if it's an AJAX request
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Health screening declined successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Health screening rejected.');
    }
}
