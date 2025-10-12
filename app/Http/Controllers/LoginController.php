<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\AdminSettingsRequest;

class LoginController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
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
    public function user_update_password(UpdatePasswordRequest $request)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first');
        }
        try {
            $this->authService->updateUserPassword(Session::get('account_id'), $request->input('current_password'), $request->input('new_password'));
            // Redirect back to the settings page and keep the password tab active so the user sees confirmation
            return redirect()->route('user.settings', ['tab' => 'password'])->with('status', 'Password updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['current_password' => $e->getMessage()]);
        }
    }
    // ==================== LOGIN FORM ====================
    public function login_page()
    {
        return view('user-login'); // Single login page for both users and admins
    }

    // ==================== HANDLE LOGIN ====================
    public function handle_login(LoginRequest $request)
    {
        $input = $request->input('phone');
        $password = $request->input('password');

        $result = $this->authService->attemptLogin($input, $password);

        if (!$result['success']) {
            return back()->withInput($request->only('phone'))->withErrors(['phone' => $result['error']]);
        }

        if ($result['role'] === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Admin!');
        }

        return redirect()->route('user.dashboard')->with('success', 'Login successful!');
    }

    // ==================== USER DASHBOARD ====================
    public function user_dashboard()
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'user') {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $userId = Session::get('account_id');
        $user = User::find($userId);
        
        // Gather dashboard statistics for the user
        $stats = $this->authService->gatherUserDashboardStats($userId);
        
        return view('user.dashboard', array_merge(compact('user'), $stats));
    }

    // ==================== ADMIN DASHBOARD ====================
    public function admin_dashboard(Request $request)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return redirect()->route('login')->with('error', 'Please login first');
        }
        $year = $request->input('year', now()->year);
        $data = $this->authService->gatherAdminDashboardStats((int) $year);
        $data['currentYear'] = $year;

        return view('admin.dashboard', $data);
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
    public function admin_settings_update(AdminSettingsRequest $request)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return redirect()->route('login')->with('error', 'Please login first');
        }
        try {
            $this->authService->updateAdminSettings(Session::get('account_id'), $request->validated());
            return redirect()->route('admin.settings')->with('success', 'Settings updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ==================== CHECK USERNAME EXISTS (AJAX) ====================
    public function check_username(Request $request)
    {
        $username = $request->input('username');
        
        if (empty($username)) {
            return response()->json(['exists' => false]);
        }
        return response()->json(['exists' => $this->authService->checkUsernameExists($username)]);
    }

    // ==================== LOGOUT ====================
    public function logout()
    {
        $this->authService->logout();
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
