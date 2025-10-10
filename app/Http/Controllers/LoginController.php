<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class LoginController extends Controller
{
    // ==================== USER SETTINGS (GET) ====================
    public function user_settings()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first');
        }
    $user = User::find(Session::get('account_id'));
    $infants = $user ? $user->infants : collect();
    return view('user.settings', compact('user', 'infants'));
    }

    // ==================== USER SETTINGS (POST) ====================
    public function user_update_password(Request $request)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = User::find(Session::get('account_id'));
        if (!$user) {
            return back()->withErrors(['current_password' => 'User not found.']);
        }
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('user.settings')->with('status', 'Password updated successfully!');
    }
    // ==================== LOGIN FORM ====================
    public function login_page()
    {
        return view('user-login'); // Single login page for both users and admins
    }

    // ==================== HANDLE LOGIN ====================
    public function handle_login(Request $request)
    {
        // Validate input
        $request->validate([
            'phone' => 'required',
            'password' => 'required'
        ]);

        $role = null;
        $account = null;
        $input = $request->phone;

        // Check if input contains only numbers (for regular users)
        $isNumeric = preg_match('/^[0-9]+$/', $input);

        // Try USER table first (only if numeric input - 11 digits)
        if ($isNumeric) {
            $account = DB::table('user')->where('contact_number', $input)->first();
            if ($account) {
                $role = 'user';
            }
        }

        // If not found in user table, try ADMIN table (check by username)
        if (!$account) {
            $account = DB::table('admin')->where('username', $input)->first();
            if ($account) {
                $role = 'admin';
            }
        }

        // If no account found, return specific error with old input
        if (!$account) {
            return back()
                ->withInput($request->only('phone'))
                ->withErrors(['phone' => 'This contact number/username is not registered in our system.']);
        }

        // Check password
        if (Hash::check($request->password, $account->password)) {
            // ✅ Store session
            Session::put('account_id', $role === 'admin' ? $account->admin_id : $account->user_id);
            Session::put('account_name', $role === 'admin' ? $account->full_name : $account->first_name);
            Session::put('account_role', $role);

            // ✅ Redirect based on role
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Admin!');
            } else {
                return redirect()->route('user.dashboard')->with('success', 'Login successful!');
            }
        }

        // Password is wrong - keep the phone number
        return back()
            ->withInput($request->only('phone'))
            ->withErrors(['password' => 'Incorrect password. Please try again.']);
    }

    // ==================== USER DASHBOARD ====================
    public function user_dashboard()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = User::find(Session::get('account_id'));
        return view('user.dashboard', compact('user'));
    }

    // ==================== ADMIN DASHBOARD ====================
    public function admin_dashboard(Request $request)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        // Get all dates that have availability set
        $availableDates = \App\Models\Availability::select('available_date')
            ->distinct()
            ->where('available_date', '>=', now()->toDateString())
            ->pluck('available_date')
            ->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        // Debug: Log the available dates
        Log::info('Available Dates for Dashboard:', $availableDates);

        // Get statistics for dashboard cards
        $totalDonations = \App\Models\Donation::count();
        $approvedRequests = \App\Models\BreastmilkRequest::whereIn('status', ['approved', 'dispensed'])->count();
        $totalHealthScreenings = \App\Models\HealthScreening::count();

        // Get donation method statistics (Walk-in vs Home Collection) - Only count accepted donations
        $walkInDonations = \App\Models\Donation::where('donation_method', 'walk_in')
            ->where('status', 'success_walk_in')
            ->count();
        $homeCollectionDonations = \App\Models\Donation::where('donation_method', 'home_collection')
            ->where('status', 'success_home_collection')
            ->count();

        // Get health screening status statistics
        $pendingScreenings = \App\Models\HealthScreening::where('status', 'pending')->count();
        $acceptedScreenings = \App\Models\HealthScreening::where('status', 'accepted')->count();
        $declinedScreenings = \App\Models\HealthScreening::where('status', 'declined')->count();

        // Get monthly data for selected year (default to current year)
        $currentYear = $request->input('year', now()->year);
        $monthlyDonations = [];
        $monthlyRequests = [];

        for ($month = 1; $month <= 12; $month++) {
            // Get donations count for each month
            $donationsCount = \App\Models\Donation::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->count();
            $monthlyDonations[] = $donationsCount;

            // Get requests count for each month (approved and dispensed)
            $requestsCount = \App\Models\BreastmilkRequest::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->whereIn('status', ['approved', 'dispensed'])
                ->count();
            $monthlyRequests[] = $requestsCount;
        }

        return view('admin.dashboard', compact(
            'availableDates', 
            'totalDonations', 
            'approvedRequests', 
            'totalHealthScreenings',
            'monthlyDonations',
            'monthlyRequests',
            'currentYear',
            'walkInDonations',
            'homeCollectionDonations',
            'pendingScreenings',
            'acceptedScreenings',
            'declinedScreenings'
        ));
    }

    // ==================== ADMIN SETTINGS (GET) ====================
    public function admin_settings()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $admin = \App\Models\Admin::find(Session::get('account_id'));
        return view('admin.settings', compact('admin'));
    }

    // ==================== ADMIN SETTINGS (POST) ====================
    public function admin_settings_update(Request $request)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'current_password' => 'required|string'
        ]);

        $admin = \App\Models\Admin::find(Session::get('account_id'));
        if (!$admin) {
            return redirect()->back()->with('error', 'Admin not found');
        }

        // Verify current password
        if (!Hash::check($request->current_password, $admin->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $admin->username = $request->username;
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
                $admin->save();

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully');
    }

    // ==================== CHECK USERNAME EXISTS (AJAX) ====================
    public function check_username(Request $request)
    {
        $username = $request->input('username');
        
        if (empty($username)) {
            return response()->json(['exists' => false]);
        }
        
        // Check if username exists in admin table
        $exists = DB::table('admin')->where('username', $username)->exists();
        
        return response()->json(['exists' => $exists]);
    }

    // ==================== LOGOUT ====================
    public function logout()
    {
        Session::forget(['account_id', 'account_name', 'account_role']);
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
