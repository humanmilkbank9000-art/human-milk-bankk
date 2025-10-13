<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Infant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Services\UserRegistrationService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\StoreInfantRequest;

class UserRegisterController extends Controller
{
    protected UserRegistrationService $service;

    public function __construct(UserRegistrationService $service)
    {
        $this->service = $service;
    }

    // Show user registration form
    public function user_register()
    {
        // Retrieve user data from session if coming back from infant registration
        $userData = Session::get('temp_user_data', []);
        
        return view('user-register', compact('userData'));
    }

    // Store user registration data temporarily in session
    public function store_user(StoreUserRequest $request)
    {
        $this->service->storeTempUser($request->validated());
        return redirect()->route('user.register.infant')->with('success', 'Please register your infant to complete the registration.');
    }

    // Show infant registration form
    public function user_register_infant()
    {
        // Check if user data exists in session
        $userData = Session::get('temp_user_data');
        
        if (!$userData) {
            return redirect()->route('user.register')->with('error', 'Please complete user registration first.');
        }

        // Retrieve infant data from session if coming back
        $infantData = Session::get('temp_infant_data', []);
        
        return view('user-register-infant', compact('userData', 'infantData'));
    }

    // Save temporary infant data to session and redirect back to user registration
    public function save_temp_infant(Request $request)
    {
        // Don't validate when just saving temp data - only save what's entered
        $infantData = $request->only([
            'first_name',
            'middle_name', 
            'last_name',
            'suffix',
            'infant_sex',
            'infant_date_of_birth',
            'birth_weight'
        ]);
        
        $this->service->storeTempInfant($infantData);
        
        // Clear any validation errors from the previous form submission
        Session::forget('errors');
        
        return redirect()->route('user.register');
    }

    // Store infant data and save both user and infant to database
    public function store_infant(StoreInfantRequest $request)
    {
        try {
            $this->service->storeUserAndInfant($request->validated());
            return redirect()->route('user.dashboard')->with('success', 'Registration completed successfully. Welcome!');
        } catch (\Exception $e) {
            return redirect()->route('user.register')->with('error', $e->getMessage());
        }
    }
}
