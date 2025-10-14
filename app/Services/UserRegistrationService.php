<?php

namespace App\Services;

use App\Models\User;
use App\Models\Infant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class UserRegistrationService
{
    public function storeTempUser(array $data): void
    {
        $age = Carbon::parse($data['date_of_birth'])->age;

        Session::put('temp_user_data', [
            'first_name'     => $data['first_name'],
            'middle_name'    => $data['middle_name'] ?? null,
            'last_name'      => $data['last_name'],
            'contact_number' => $data['contact_number'],
                'password'       => trim(strip_tags($data['password'])), // plain temporarily
            'address'        => $data['address'],
            'date_of_birth'  => $data['date_of_birth'],
            'age'            => $age,
            'sex'            => $data['sex'],
        ]);
    }

    public function storeUserAndInfant(array $infantData): User
    {
        $userData = Session::get('temp_user_data');
        if (!$userData) {
            throw new \RuntimeException('Session expired. Please register again.');
        }

        $birthDate = Carbon::parse($infantData['infant_date_of_birth']);
        $ageInMonths = $birthDate->diffInMonths(Carbon::now());

        $user = User::create([
            'first_name'     => $userData['first_name'],
            'middle_name'    => $userData['middle_name'],
            'last_name'      => $userData['last_name'],
            'contact_number' => $userData['contact_number'],
            'password'       => Hash::make($userData['password']),
            'address'        => $userData['address'],
            'date_of_birth'  => $userData['date_of_birth'],
            'age'            => $userData['age'],
            'sex'            => $userData['sex'],
            'user_type'      => 'donor',
        ]);

        Infant::create([
            'user_id'        => $user->user_id,
            'first_name'     => $infantData['first_name'],
            'middle_name'    => $infantData['middle_name'] ?? null,
            'last_name'      => $infantData['last_name'],
            'suffix'         => $infantData['suffix'] ?? null,
            'sex'            => $infantData['infant_sex'],
            'date_of_birth'  => $infantData['infant_date_of_birth'],
            'age'            => $ageInMonths,
            'birth_weight'   => $infantData['birth_weight'],
        ]);

        // Auto-login
        Session::put('account_id', $user->user_id);
        Session::put('account_name', $user->first_name);
        Session::put('account_role', 'user');

        // Clear temps
        Session::forget('temp_user_data');
        Session::forget('temp_infant_data');

        return $user;
    }

    public function storeTempInfant(array $data): void
    {
        Session::put('temp_infant_data', [
            'first_name'            => $data['first_name'],
            'middle_name'           => $data['middle_name'] ?? null,
            'last_name'             => $data['last_name'],
            'infant_sex'            => $data['infant_sex'],
            'infant_date_of_birth'  => $data['infant_date_of_birth'],
            'birth_weight'          => $data['birth_weight'],
            'suffix'                => $data['suffix'] ?? null,
        ]);
    }
}
