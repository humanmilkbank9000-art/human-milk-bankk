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
use App\Services\HealthScreeningService;
use App\Http\Requests\StoreHealthScreeningRequest;

class HealthScreeningController extends Controller
{
    protected HealthScreeningService $service;

    public function __construct(HealthScreeningService $service)
    {
        $this->service = $service;
    }
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
    public function store(StoreHealthScreeningRequest $request)
    {
        try {
            $user_id = Session::get('account_id');
            if (!$user_id) {
                return response()->json(['error' => 'User not logged in'], 401);
            }

            $screening = $this->service->create($request->validated(), $user_id);

            return response()->json([
                'message' => 'Health screening submitted successfully!',
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
        $search = $request->query('q');

        // Base query with status filter
        $query = HealthScreening::where('status', $status)
            ->with('user', 'infant');

        // Apply search filter if present
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('contact_number', 'LIKE', "%{$search}%");
                });
            });
        }

        // Counts (not affected by search)
        $pendingCount = HealthScreening::where('status', 'pending')->count();
        $acceptedCount = HealthScreening::where('status', 'accepted')->count();
        $declinedCount = HealthScreening::where('status', 'declined')->count();
        
        $healthScreenings = $query->paginate(10)
            ->appends(['status' => $status, 'q' => $search]);

        return view('admin.health-screening', compact('healthScreenings', 'status', 'pendingCount', 'acceptedCount', 'declinedCount'));
    }

    public function accept($id)
    {
        $screening = HealthScreening::findOrFail($id);
        $comments = request()->input('comments');

        try {
            $this->service->accept($screening, $comments);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Health screening accepted successfully.']);
            }

            return redirect()->back()->with('success', 'Health screening accepted.');
        } catch (\Exception $e) {
            Log::error('HealthScreening accept error: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $screening = HealthScreening::findOrFail($id);
        $comments = $request->input('comments');

        // Enforce comments when declining. For AJAX requests return JSON 422, for normal requests use validator
        if (($request->wantsJson() || $request->ajax()) && empty(trim((string) $comments))) {
            return response()->json(['error' => 'Comments are required when declining.'], 422);
        }

        if (!($request->wantsJson() || $request->ajax())) {
            // For non-AJAX requests, use Laravel validation to redirect back with errors if empty
            $request->validate([
                'comments' => 'required|string'
            ]);
        }

        try {
            $this->service->reject($screening, $comments);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Health screening declined successfully.']);
            }

            return redirect()->back()->with('success', 'Health screening rejected.');
        } catch (\Exception $e) {
            Log::error('HealthScreening reject error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function undoDecline($id)
    {
        $screening = HealthScreening::findOrFail($id);
        $comments = request()->input('comments');

        try {
            $this->service->undoDecline($screening, $comments);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Health screening has been reversed and is now accepted.']);
            }

            return redirect()->back()->with('success', 'Health screening has been accepted.');
        } catch (\Exception $e) {
            Log::error('HealthScreening undoDecline error: ' . $e->getMessage());
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // Archive/restore endpoints removed per requirements
}
