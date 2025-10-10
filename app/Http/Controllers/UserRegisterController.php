<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Infant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class UserRegisterController extends Controller
{
    // Show user registration form
    public function user_register()
    {
        return view('user-register');
    }

    // Store user registration data
    public function store_user(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string',
            'middle_name'    => 'nullable|string',
            'last_name'      => 'required|string',
            'contact_number' => 'required|max:11',
            'password'       => 'required|confirmed',
            'address'        => 'required|string',
            'date_of_birth'  => 'required|date',
            'sex'            => 'required|in:female,male',
        ]);

        $age = Carbon::parse($request->date_of_birth)->age;

        $user = User::create([
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name,
            'last_name'      => $request->last_name,
            'contact_number' => $request->contact_number,
            'password'       => Hash::make($request->password),
            'address'        => $request->address,
            'date_of_birth'  => $request->date_of_birth,
            'age'            => $age,
            'sex'            => $request->sex,
            'user_type'      => 'donor',
        ]);

        // ✅ Auto-login user with consistent session keys
        Session::put('account_id', $user->user_id);
        Session::put('account_name', $user->first_name);
        Session::put('account_role', 'user');

        // Redirect to infant registration step
        return redirect()->route('user.register.infant')->with('success', 'User registered! Please register your infant.');
    }

    // Show infant registration form
    public function user_register_infant()
    {
        $user = User::find(Session::get('account_id'));
        return view('user-register-infant', compact('user'));
    }

    // Store infant data linked to user
    public function store_infant(Request $request)
    {
        $request->validate([
            'first_name'            => 'required|string',
            'middle_name'           => 'nullable|string',
            'last_name'             => 'required|string',
            'infant_sex'            => 'required|in:female,male',
            'infant_date_of_birth'  => 'required|date',
            'birth_weight'          => 'required|numeric|min:0',
        ]);

        // Calculate age in months
        $birthDate = Carbon::parse($request->infant_date_of_birth);
        $ageInMonths = $birthDate->diffInMonths(Carbon::now());

        Infant::create([
            'user_id'        => Session::get('account_id'),
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name,
            'last_name'      => $request->last_name,
            'sex'            => $request->infant_sex,
            'date_of_birth'  => $request->infant_date_of_birth,
            'age'            => $ageInMonths,
            'birth_weight'   => $request->birth_weight,
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Infant registered successfully. Welcome!');
    }
}
